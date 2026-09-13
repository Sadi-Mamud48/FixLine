<?php
/**
 * FixLine - Model
 * -----------------------------------
 * ServiceProvider.php
 *
 * Handles ALL database interaction for the Service Provider role:
 *  - Profile (read / update / profile picture)
 *  - Job listing + job applications
 *  - Earnings
 *
 * The Controller talks to this class only. Views never touch the DB directly.
 */

require_once __DIR__ . '/database/Database.php';

class ServiceProvider
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* =========================================================
     *  PROFILE
     * ========================================================= */

    // Find the provider record tied to a logged-in user
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

    // Fetch the full profile (joined with users table) for one provider
    public function getProfile(int $providerId): ?array
    {
        $sql = "SELECT sp.*, u.name, u.email
                FROM service_providers sp
                INNER JOIN users u ON u.id = sp.user_id
                WHERE sp.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $providerId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    // Update editable profile fields (profession, affiliate, experience, bio)
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

    // Save the new profile picture path (used by the AJAX upload endpoint)
    public function updateProfilePicture(int $providerId, string $relativePath): bool
    {
        $sql = "UPDATE service_providers SET profile_picture = :path WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':path' => $relativePath,
            ':id'   => $providerId,
        ]);
    }

    // Used to delete the old picture file before saving a new one
    public function getProfilePicturePath(int $providerId): ?string
    {
        $sql = "SELECT profile_picture FROM service_providers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $providerId]);
        $row = $stmt->fetch();

        return $row ? $row['profile_picture'] : null;
    }

    /* =========================================================
     *  JOBS / APPLICATIONS
     * ========================================================= */

    // Open jobs that match the provider's profession and haven't been applied to yet
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
            $sql .= " AND j.category = :category";
            $params[':category'] = $profession;
        }

        $sql .= " ORDER BY j.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Customer service requests that arrive in the provider inbox as message-like cards
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
                WHERE sr.provider_id = :provider_id
                ORDER BY sr.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);

        return $stmt->fetchAll();
    }

    public function updateServiceRequestStatus(int $providerId, int $requestId, string $status): bool
    {
        $allowed = ['new', 'read', 'accepted', 'rejected'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = "UPDATE service_requests
                SET status = :status
                WHERE id = :id AND provider_id = :provider_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status'      => $status,
            ':id'          => $requestId,
            ':provider_id' => $providerId,
        ]);
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

    // Single job lookup (used before inserting an application)
    public function getJobById(int $jobId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $jobId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    // Insert a new job application ("apply for job" form)
    public function applyForJob(int $providerId, int $jobId, string $coverNote, ?float $proposedPrice): bool
    {
        $sql = "INSERT INTO job_applications (job_id, provider_id, cover_note, proposed_price, status)
                VALUES (:job_id, :provider_id, :cover_note, :proposed_price, 'pending')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':job_id'         => $jobId,
            ':provider_id'    => $providerId,
            ':cover_note'     => $coverNote,
            ':proposed_price' => $proposedPrice,
        ]);
    }

    // History of jobs this provider has applied to
    public function getAppliedJobs(int $providerId): array
    {
        $sql = "SELECT ja.*, j.title, j.category, j.location, j.budget, j.status AS job_status
                FROM job_applications ja
                INNER JOIN jobs j ON j.id = ja.job_id
                WHERE ja.provider_id = :provider_id
                ORDER BY ja.applied_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':provider_id' => $providerId]);

        return $stmt->fetchAll();
    }

    /* =========================================================
     *  EARNINGS
     * ========================================================= */

    // Every earnings row (paid + pending) for the earnings table view
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

    // Quick totals used on the dashboard + earnings page header cards
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
