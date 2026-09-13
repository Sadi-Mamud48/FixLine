<?php
class Customer {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

   
    public function searchServices($category = null, $keyword = null) {
        $sql = "SELECT s.*, sp.id AS provider_id, sp.profession, sp.affiliate,
                       u.name AS provider_name
                FROM services s
                JOIN service_providers sp ON sp.id = s.provider_id
                JOIN users u ON u.id = sp.user_id
                WHERE sp.status = 'approved'";
        $params = [];

        if (!empty($category)) {
            $sql .= " AND LOWER(s.category) = LOWER(:category)";
            $params[':category'] = $category;
        }

        if (!empty($keyword)) {
            $sql .= " AND (s.service_name LIKE :keyword_name OR s.description LIKE :keyword_description
                         OR u.name LIKE :keyword_provider)";
            $keywordValue = "%" . $keyword . "%";
            $params[':keyword_name'] = $keywordValue;
            $params[':keyword_description'] = $keywordValue;
            $params[':keyword_provider'] = $keywordValue;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceProviders() {
        $stmt = $this->db->query(
            "SELECT sp.id, sp.profession, u.name
             FROM service_providers sp
             JOIN users u ON u.id = sp.user_id
             WHERE sp.status = 'approved'
             ORDER BY u.name"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceCatalog() {
        $stmt = $this->db->query(
            "SELECT s.id AS service_id, s.service_name, s.category, s.price,
                    sp.id AS provider_id, sp.profession, sp.affiliate, u.name AS provider_name
             FROM services s
             JOIN service_providers sp ON sp.id = s.provider_id
             JOIN users u ON u.id = sp.user_id
             WHERE sp.status = 'approved'
             ORDER BY s.category, u.name, s.service_name"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJobApplicationsForCustomer($customerId, $category = null) {
        $sql = "SELECT ja.id AS application_id, ja.provider_id,
                       ja.proposed_price AS price, j.id AS job_id,
                       j.title AS service_name, j.category, j.description,
                       j.location, sp.profession, sp.affiliate,
                       u.name AS provider_name
                FROM job_applications ja
                JOIN jobs j ON j.id = ja.job_id
                JOIN service_providers sp ON sp.id = ja.provider_id
                JOIN users u ON u.id = sp.user_id
                WHERE j.customer_id = :customer_id
                  AND sp.status = 'approved'
                                    AND ja.status = 'pending'
                                    AND NOT EXISTS (
                      SELECT 1 FROM services s
                      WHERE s.job_application_id = ja.id
                                    )";
        $params = [':customer_id' => $customerId];

        if (!empty($category)) {
            $sql .= " AND LOWER(j.category) = LOWER(:category)";
            $params[':category'] = $category;
        }

        $sql .= " ORDER BY ja.applied_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createServiceRequest($customerId, $providerId, $data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO service_requests
                        (customer_id, provider_id, service_id, title, category, description, location, budget, status)
                    SELECT :customer_id, sp.id, :request_service_id, :title, :category, :description, :location, :budget, 'new'
                FROM service_providers sp
                JOIN services s ON s.provider_id = sp.id
                WHERE sp.id = :provider_id AND s.id = :provider_service_id AND sp.status = 'approved'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':customer_id' => $customerId,
                ':provider_id' => $providerId,
                ':request_service_id' => $data['service_id'],
                ':provider_service_id' => $data['service_id'],
                ':title' => $data['title'],
                ':category' => $data['category'],
                ':description' => $data['description'],
                ':location' => $data['location'],
                ':budget' => $data['budget']
            ]);

            if ($stmt->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $requestId = (int) $this->db->lastInsertId();
            $booking = $this->db->prepare(
                "INSERT INTO bookings (user_id, service_request_id, service_id, booking_date, status)
                 VALUES (:user_id, :request_id, :service_id, :booking_date, 'pending')"
            );
            $booking->execute([
                ':user_id' => $customerId,
                ':request_id' => $requestId,
                ':service_id' => $data['service_id'],
                ':booking_date' => $data['booking_date']
            ]);

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }

        return true;
    }

    public function createJobApplicationRequest($customerId, $applicationId, $data) {
        $stmt = $this->db->prepare(
            "SELECT ja.provider_id, j.title, j.category, j.description, j.location, ja.proposed_price
             FROM job_applications ja
             JOIN jobs j ON j.id = ja.job_id
             JOIN service_providers sp ON sp.id = ja.provider_id
             WHERE ja.id = :application_id AND j.customer_id = :customer_id
               AND ja.status = 'pending' AND sp.status = 'approved'"
        );
        $stmt->execute([
            ':application_id' => $applicationId,
            ':customer_id' => $customerId
        ]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $request = $this->db->prepare(
                "INSERT INTO service_requests
                       (customer_id, provider_id, job_application_id, title, category, description, location, budget, status)
                   VALUES (:customer_id, :provider_id, :application_id, :title, :category, :description, :location, :budget, 'new')"
            );
            $request->execute([
                ':customer_id' => $customerId,
                ':provider_id' => $application['provider_id'],
                ':application_id' => $applicationId,
                ':title' => $application['title'],
                ':category' => $application['category'],
                ':description' => $data['description'] ?: $application['description'],
                ':location' => $application['location'],
                ':budget' => $application['proposed_price']
            ]);

            $booking = $this->db->prepare(
                "INSERT INTO bookings (user_id, service_request_id, booking_date, status)
                 VALUES (:user_id, :request_id, :booking_date, 'pending')"
            );
            $booking->execute([
                ':user_id' => $customerId,
                ':request_id' => $this->db->lastInsertId(),
                ':booking_date' => $data['booking_date']
            ]);
            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }

        return true;
    }

   
    public function createBooking($userId, $serviceId, $bookingDate) {
        $sql = "INSERT INTO bookings (user_id, service_id, booking_date, status)
                VALUES (:user_id, :service_id, :booking_date, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':service_id' => $serviceId,
            ':booking_date' => $bookingDate
        ]);
    }

    public function createJob($customerId, $data) {
        $stmt = $this->db->prepare(
            "INSERT INTO jobs (customer_id, title, category, description, location, budget, status)
             VALUES (:customer_id, :title, :category, :description, :location, :budget, 'open')"
        );

        return $stmt->execute([
            ':customer_id' => $customerId,
            ':title' => $data['title'],
            ':category' => $data['category'],
            ':description' => $data['description'],
            ':location' => $data['location'],
            ':budget' => $data['budget']
        ]);
    }

   
    public function getCustomerBookings($userId) {
        $sql = "SELECT b.*, COALESCE(s.service_name, sr.title) AS service_name,
                   COALESCE(s.description, sr.description) AS description,
                   COALESCE(s.price, sr.budget) AS price,
                   sp.profession, sp.affiliate, u.name AS provider_name,
                   sr.status AS request_status
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN service_requests sr ON sr.id = b.service_request_id
            LEFT JOIN service_providers sp ON sp.id = sr.provider_id
            LEFT JOIN users u ON u.id = sp.user_id
                WHERE b.user_id = :user_id
                AND b.status <> 'cancelled'
                AND NOT EXISTS (
                    SELECT 1 FROM payments p
                    WHERE p.booking_id = b.id
                    AND p.status = 'paid'
                )
                ORDER BY b.booking_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function submitReview($bookingId, $userId, $serviceId, $rating, $comment) {
        $stmt = $this->db->prepare(
            "SELECT id FROM bookings
             WHERE id = :booking_id AND user_id = :user_id
             AND service_id = :service_id
             AND status IN ('confirmed', 'completed')"
        );
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId,
            ':service_id' => $serviceId
        ]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException('Reviews are available only after the booking is accepted.');
        }

        $sql = "INSERT INTO reviews (booking_id, user_id, service_id, rating, comment)
                VALUES (:booking_id, :user_id, :service_id, :rating, :comment)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':booking_id'  => $bookingId,
            ':user_id' => $userId,
            ':service_id' => $serviceId,
            ':rating'      => $rating,
            ':comment'     => $comment
        ]);
    }

    public function cancelBooking($bookingId, $userId) {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET status = 'cancelled'
             WHERE id = :booking_id AND user_id = :user_id
             AND status IN ('pending', 'confirmed')"
        );
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId
        ]);
        return $stmt->rowCount() > 0;
    }

    public function createPayment($bookingId, $userId) {
        $stmt = $this->db->prepare(
            "SELECT b.id, s.price
             FROM bookings b
             JOIN services s ON b.service_id = s.id
             WHERE b.id = :booking_id
             AND b.user_id = :user_id
             AND b.status IN ('confirmed', 'completed')"
        );
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId
        ]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new RuntimeException('Booking was not found or has been cancelled.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO payments (booking_id, user_id, amount, status)
             VALUES (:booking_id, :user_id, :amount, 'paid')"
        );
        return $stmt->execute([
            ':booking_id' => $booking['id'],
            ':user_id' => $userId,
            ':amount' => $booking['price']
        ]);
    }

    public function getPaidPayments($userId) {
        $stmt = $this->db->prepare(
            "SELECT p.id AS payment_id, p.amount, p.paid_at,
                    b.id AS booking_id, s.service_name
             FROM payments p
             JOIN bookings b ON p.booking_id = b.id
             JOIN services s ON b.service_id = s.id
             WHERE p.user_id = :user_id AND p.status = 'paid'
             ORDER BY p.paid_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function requestRefund($paymentId, $userId, $reason) {
        $stmt = $this->db->prepare(
            "INSERT INTO refund_requests (payment_id, user_id, reason, status)
             SELECT p.id, p.user_id, :reason, 'requested'
             FROM payments p
             WHERE p.id = :payment_id AND p.user_id = :user_id AND p.status = 'paid'
             AND NOT EXISTS (
                 SELECT 1 FROM refund_requests r WHERE r.payment_id = p.id
             )"
        );
        $stmt->execute([
            ':payment_id' => $paymentId,
            ':user_id' => $userId,
            ':reason' => $reason
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getUser($userId) {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, first_name, last_name, address, phone, profile_photo
             FROM users WHERE id = :user_id"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateProfile($userId, $firstName, $lastName, $address, $profilePhoto = null) {
        $sql = "UPDATE users
                SET first_name = :first_name, last_name = :last_name, address = :address";
        $params = [
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':address' => $address,
            ':user_id' => $userId
        ];

        if ($profilePhoto !== null) {
            $sql .= ", profile_photo = :profile_photo";
            $params[':profile_photo'] = $profilePhoto;
        }

        $sql .= " WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateAccount($userId, $phone, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new RuntimeException('User account was not found.');
        }

        $sql = "UPDATE users SET phone = :phone";
        $params = [':phone' => $phone, ':user_id' => $userId];

        if ($newPassword !== '') {
            $passwordMatches = password_verify($currentPassword, $user['password'])
                || hash_equals((string) $user['password'], $currentPassword);

            if (!$passwordMatches) {
                throw new RuntimeException('Current password is incorrect.');
            }

            $sql .= ", password = :password";
            $params[':password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
?>
