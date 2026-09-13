<?php
class Customer {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

   
    public function searchServices($category = null, $keyword = null) {
        $sql = "SELECT * FROM services WHERE 1=1";
        $params = [];

        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }

        if (!empty($keyword)) {
            $sql .= " AND (service_name LIKE :keyword_name OR description LIKE :keyword_description)";
            $keywordValue = "%" . $keyword . "%";
            $params[':keyword_name'] = $keywordValue;
            $params[':keyword_description'] = $keywordValue;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

   
    public function getCustomerBookings($userId) {
        $sql = "SELECT b.*, s.service_name, s.description, s.price
                FROM bookings b
                JOIN services s ON b.service_id = s.id
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