<?php

require_once __DIR__ . '/../Config/Database.php';

class ServiceProvider
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getProviderIdByUserId(int $userId): ?int
    {
        $sql = "SELECT id
                FROM service_providers
                WHERE user_id = :user_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? (int) $row['id'] : null;
    }

    public function getProfile(int $providerId): ?array
    {
        $sql = "SELECT sp.*, u.name, u.email,
                       COALESCE((
                           SELECT ROUND(AVG(r.rating), 1)
                           FROM reviews r
                           JOIN services rated_service ON rated_service.id = r.service_id
                           WHERE rated_service.provider_id = sp.id
                       ), sp.rating, 0.0) AS rating
                FROM service_providers sp
                INNER JOIN users u ON u.id = sp.user_id
                WHERE sp.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $providerId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function updateProfile(int $providerId, array $data): bool
    {
        $sql = "UPDATE service_providers
                SET profession = :profession,
                    affiliate  = :affiliate,
                    experience = :experience,
                    bio        = :bio
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':profession' => $data['profession'],
            ':affiliate'  => $data['affiliate'],
            ':experience' => $data['experience'],
            ':bio'        => $data['bio'],
            ':id'         => $providerId,
        ]);
    }

    public function getServices(int $providerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, service_name, category, description, price
             FROM services WHERE provider_id = :provider_id ORDER BY service_name"
        );
        $stmt->execute([
            ':provider_id' => $providerId,
        ]);

        return $stmt->fetchAll();
    }

    public function getService(int $providerId, int $serviceId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, service_name, category, description, price
             FROM services
             WHERE id = :service_id AND provider_id = :provider_id
             LIMIT 1"
        );
        $stmt->execute([
            ':service_id' => $serviceId,
            ':provider_id' => $providerId,
        ]);

        return $stmt->fetch() ?: null;
    }

    public function saveService(int $providerId, array $data): bool
    {
        if ($data['id'] > 0) {
            $stmt = $this->db->prepare(
                "UPDATE services
                 SET service_name = :service_name, category = :category,
                     description = :description, price = :price
                 WHERE id = :id AND provider_id = :provider_id"
            );
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO services
                    (provider_id, service_name, category, description, price)
                 VALUES (:provider_id, :service_name, :category, :description, :price)"
            );
        }

        $params = [
            ':service_name' => $data['service_name'],
            ':category' => $data['category'],
            ':description' => $data['description'],
            ':price' => $data['price']
        ];

        if ($data['id'] > 0) {
            $params[':id'] = $data['id'];
            $params[':provider_id'] = $providerId;
        } else {
            $params[':provider_id'] = $providerId;
        }

        return $stmt->execute($params);
    }

    public function updateProfilePicture(int $providerId, string $relativePath): bool
    {
        $sql = "UPDATE service_providers SET profile_picture = :path WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':path' => $relativePath,
            ':id'   => $providerId,
        ]);
    }

    public function getProfilePicturePath(int $providerId): ?string
    {
        $sql = "SELECT profile_picture FROM service_providers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $providerId]);
        $row = $stmt->fetch();

        return $row ? $row['profile_picture'] : null;
    }

    public function getAvailableJobs(int $providerId, ?string $profession = null): array
    {
        $sql = "SELECT j.*
                FROM jobs j
                WHERE j.status = 'open'
                  AND j.id NOT IN (
                      SELECT job_id FROM job_applications WHERE provider_id = :provider_id
                  )";

        $params = [':provider_id' => $providerId];

        if (!empty($profession)) {
            $sql .= " AND LOWER(j.category) = LOWER(:category)";
            $params[':category'] = $profession;
        }

        $sql .= " ORDER BY j.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function saveServiceRequest(int $customerId, int $providerId, array $data): bool
    {
        $sql = "INSERT INTO service_requests (customer_id, provider_id, title, category, description, location, budget, status)
                VALUES (:customer_id, :provider_id, :title, :category, :description, :location, :budget, 'new')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':customer_id' => $customerId,
            ':provider_id' => $providerId,
            ':title'       => $data['title'],
            ':category'    => $data['category'],
            ':description' => $data['description'],
            ':location'    => $data['location'],
            ':budget'      => $data['budget'],
        ]);
    }

    public function getIncomingServiceRequests(int $providerId): array
    {
        $sql = "SELECT sr.*, u.name AS customer_name
                FROM service_requests sr
                LEFT JOIN users u ON u.id = sr.customer_id
                     WHERE (sr.provider_id = :provider_id
                         OR (sr.provider_id IS NULL AND EXISTS (
                              SELECT 1 FROM service_providers target_provider
                              WHERE target_provider.id = :provider_match_id
                                 AND LOWER(target_provider.profession) = LOWER(sr.category)
                         )))
                  AND sr.status <> 'rejected'
                  AND NOT EXISTS (
                      SELECT 1 FROM bookings b
                      WHERE b.service_request_id = sr.id
                        AND b.status IN ('completed', 'cancelled')
                  )
                ORDER BY sr.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':provider_id' => $providerId,
            ':provider_match_id' => $providerId,
        ]);

        return $stmt->fetchAll();
    }

    public function updateServiceRequestStatus(int $providerId, int $requestId, string $status): bool
    {
        $allowed = ['new', 'accepted', 'applied', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = "UPDATE service_requests sr
            JOIN service_providers provider ON provider.id = :provider_match_id
            SET sr.status = :status, sr.provider_id = provider.id
            WHERE sr.id = :id
              AND sr.status = 'new'
              AND (sr.provider_id = provider.id
                   OR (sr.provider_id IS NULL AND LOWER(provider.profession) = LOWER(sr.category)))";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':status'      => $status,
            ':id'          => $requestId,
            ':provider_match_id' => $providerId,
        ]);

        if ($stmt->rowCount() > 0) {
            $bookingStatus = $status === 'accepted' ? 'confirmed' : 'cancelled';
            $booking = $this->db->prepare(
                "UPDATE bookings
                 SET status = :booking_status
                 WHERE service_request_id = :request_id AND status = 'pending'"
            );
            $booking->execute([
                ':booking_status' => $bookingStatus,
                ':request_id' => $requestId
            ]);

            if ($status === 'accepted') {
                $provider = $this->db->prepare(
                    "UPDATE service_providers
                     SET work_pending = work_pending + 1
                     WHERE id = :provider_id"
                );
                $provider->execute([':provider_id' => $providerId]);
            }
        }

        return $stmt->rowCount() > 0;
    }

    public function completeServiceRequest(int $providerId, int $requestId): bool
    {
        $this->db->beginTransaction();

        try {
            $booking = $this->db->prepare(
                "UPDATE bookings b
                 INNER JOIN service_requests sr ON sr.id = b.service_request_id
                 SET b.status = 'completed', sr.status = 'completed'
                 WHERE b.service_request_id = :request_id
                   AND b.status = 'confirmed'
                   AND sr.provider_id = :provider_id
                   AND sr.status = 'accepted'"
            );
            $booking->execute([
                ':request_id' => $requestId,
                ':provider_id' => $providerId,
            ]);

            if ($booking->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $provider = $this->db->prepare(
                "UPDATE service_providers
                 SET work_completed = work_completed + 1,
                     work_pending = GREATEST(work_pending - 1, 0),
                     work_successful = work_successful + 1
                 WHERE id = :provider_id"
            );
            $provider->execute([':provider_id' => $providerId]);

                        $earnings = $this->db->prepare(
                            "INSERT INTO earnings (provider_id, job_id, amount, status)
                             SELECT :provider_id, j.id, COALESCE(sr.budget, s.price, 0), 'pending'
                                 FROM service_requests sr
                                 LEFT JOIN services s ON s.id = sr.service_id
                             LEFT JOIN job_applications ja ON ja.id = sr.job_application_id
                             LEFT JOIN jobs j ON j.id = ja.job_id
                                 WHERE sr.id = :request_id
                            "
                        );
                        $earnings->execute([
                                ':provider_id' => $providerId,
                                ':request_id' => $requestId,
                        ]);

            $this->db->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getServiceRequestById(int $requestId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM service_requests WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $requestId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function applyToServiceRequest(int $providerId, int $requestId, string $coverNote, ?float $proposedPrice): bool
    {
        $request = $this->getServiceRequestById($requestId);
        if (!$request || (int) $request['provider_id'] !== $providerId) {
            return false;
        }

        $sql = "UPDATE service_requests
                SET status = 'applied'
                WHERE id = :id AND provider_id = :provider_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $requestId,
            ':provider_id' => $providerId,
        ]);
    }

    public function getJobById(int $jobId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $jobId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function applyForJob(int $providerId, int $jobId, string $serviceName, string $coverNote, ?float $proposedPrice): bool
    {
        $job = $this->getJobById($jobId);
        if (!$job) {
            return false;
        }

        $servicePrice = $proposedPrice ?? (float) ($job['budget'] ?? 0);

        $this->db->beginTransaction();
        try {
            $application = $this->db->prepare(
                "INSERT INTO job_applications (job_id, provider_id, cover_note, proposed_price, status)
                 VALUES (:job_id, :provider_id, :cover_note, :proposed_price, 'pending')"
            );
            $application->execute([
                ':job_id' => $jobId,
                ':provider_id' => $providerId,
                ':cover_note' => $coverNote,
                ':proposed_price' => $proposedPrice,
            ]);

            $applicationId = (int) $this->db->lastInsertId();
            $service = $this->db->prepare(
                "INSERT INTO services
                    (provider_id, job_application_id, service_name, category, description, price)
                 VALUES (:provider_id, :application_id, :service_name, :category, :description, :price)"
            );
            $service->execute([
                ':provider_id' => $providerId,
                ':application_id' => $applicationId,
                ':service_name' => $serviceName,
                ':category' => $job['category'],
                ':description' => $job['description'] ?: $coverNote,
                ':price' => $servicePrice,
            ]);

            $this->db->commit();
            return $applicationId > 0;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getAppliedJobs(int $providerId): array
    {
        $sql = "SELECT ja.*, j.title, j.category, j.location, j.budget, j.status AS job_status,
                       s.service_name
                FROM job_applications ja
                INNER JOIN jobs j ON j.id = ja.job_id
                LEFT JOIN services s ON s.job_application_id = ja.id
                WHERE ja.provider_id = :provider_id
                ORDER BY ja.applied_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);

        return $stmt->fetchAll();
    }

    public function getEarnings(int $providerId): array
    {
        $sql = "SELECT e.*, j.title AS job_title
                FROM earnings e
                LEFT JOIN jobs j ON j.id = e.job_id
                WHERE e.provider_id = :provider_id
                ORDER BY e.earned_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);

        return $stmt->fetchAll();
    }

    public function getEarningsSummary(int $providerId): array
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN status = 'paid'    THEN amount END), 0) AS total_paid,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount END), 0) AS total_pending,
                    COALESCE(SUM(amount), 0) AS total_earned
                FROM earnings
                WHERE provider_id = :provider_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);

        return $stmt->fetch() ?: ['total_paid' => 0, 'total_pending' => 0, 'total_earned' => 0];
    }
}
