<?php
/**
 * FixLine - Controller
 * -----------------------------------
 * ServiceProviderController.php
 *
 * Front controller for every Service Provider screen.
 * Routed as:  service_provider.php?action=dashboard|profile|requests|apply_job|earnings
 * Plus one AJAX-only endpoint: action=upload_picture (returns JSON).
 */

session_start();

require_once __DIR__ . '/../Model/ServiceProvider.php';

class ServiceProviderController
{
    private ServiceProvider $model;
    private int $providerId;

    public function __construct()
    {
        $this->model = new ServiceProvider();
        $this->providerId = $this->resolveProviderId();
    }

    private function resolveProviderId(): int
    {
        if (!empty($_SESSION['provider_id'])) {
            return (int) $_SESSION['provider_id'];
        }

        if (!empty($_SESSION['user_id'])) {
            $providerId = $this->model->getProviderIdByUserId((int) $_SESSION['user_id']);
            if ($providerId !== null) {
                $_SESSION['provider_id'] = $providerId;
                return $providerId;
            }
        }

        // Fallback for local demo/testing only.
        return 1;
    }

    // Simple internal router based on ?action=
    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'dashboard';

        switch ($action) {
            case 'dashboard':
                $this->dashboard();
                break;
            case 'profile':
                $this->profile();
                break;
            case 'upload_picture':
                $this->uploadProfilePicture(); // AJAX / JSON only
                break;
            case 'requests':
                $this->requests();
                break;
            case 'apply_job':
                $this->applyJob();
                break;
            case 'earnings':
                $this->earnings();
                break;
            default:
                $this->dashboard();
        }
    }

    /* =========================================================
     *  DASHBOARD
     * ========================================================= */
    private function dashboard(): void
    {
        $provider = $this->model->getProfile($this->providerId);
        $summary  = $this->model->getEarningsSummary($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/dashboard.php';
    }

    /* =========================================================
     *  PROFILE  (view/edit info + profile picture upload trigger)
     * ========================================================= */
    private function profile(): void
    {
        $message = '';

        // Handle the text-field part of the profile form (normal POST, no AJAX)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $data = [
                'profession' => trim($_POST['profession'] ?? ''),
                'affiliate'  => trim($_POST['affiliate'] ?? ''),
                'experience' => trim($_POST['experience'] ?? ''),
                'bio'        => trim($_POST['bio'] ?? ''),
            ];

            if ($data['profession'] === '') {
                $message = 'Profession is required.';
            } else {
                $this->model->updateProfile($this->providerId, $data);
                $message = 'Profile updated successfully.';
            }
        }

        $provider = $this->model->getProfile($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/profile.php';
    }

    /* =========================================================
     *  PROFILE PICTURE UPLOAD  (AJAX endpoint -> JSON response)
     *  This is the ONE place in the module that uses JSON + AJAX,
     *  because an instant preview/save without a full page reload
     *  is genuinely needed here.
     * ========================================================= */
    private function uploadProfilePicture(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['profile_picture'])) {
            echo json_encode(['success' => false, 'message' => 'No file received.']);
            return;
        }

        $file = $_FILES['profile_picture'];

        // --- Basic validation ---
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSizeBytes = 2 * 1024 * 1024; // 2 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error. Please try again.']);
            return;
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes, true)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG or WEBP images are allowed.']);
            return;
        }

        if ($file['size'] > $maxSizeBytes) {
            echo json_encode(['success' => false, 'message' => 'Image must be smaller than 2MB.']);
            return;
        }

        // --- Build a unique filename and move the upload ---
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename  = 'provider_' . $this->providerId . '_' . time() . '.' . $extension;

        // uploads/ lives at the project root, one level up from Controller/
        $uploadDir    = __DIR__ . '/../uploads/profile_pictures/';
        $uploadPathFs = $uploadDir . $filename;
        $relativePath = 'uploads/profile_pictures/' . $filename; // root-relative: stored in DB + used as <img src="">

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPathFs)) {
            echo json_encode(['success' => false, 'message' => 'Could not save the uploaded file.']);
            return;
        }

        // Remove the old picture from disk (skip the default placeholder)
        $oldPath = $this->model->getProfilePicturePath($this->providerId);
        if ($oldPath && strpos($oldPath, 'default-avatar') === false) {
            $oldFullPath = __DIR__ . '/../' . $oldPath;
            if (is_file($oldFullPath)) {
                unlink($oldFullPath);
            }
        }

        $this->model->updateProfilePicture($this->providerId, $relativePath);

        echo json_encode([
            'success'  => true,
            'message'  => 'Profile picture updated.',
            'imageUrl' => $relativePath, // root-relative, works directly as <img src="">
        ]);
    }

    /* =========================================================
     *  SERVICE REQUESTS INBOX
     * ========================================================= */
    private function requests(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['accept_service_request'])) {
                $requestId = (int) ($_POST['request_id'] ?? 0);

                if ($requestId <= 0) {
                    $message = 'No request selected.';
                } else {
                    $updated = $this->model->updateServiceRequestStatus($this->providerId, $requestId, 'accepted');
                    $message = $updated
                        ? 'The customer request was accepted successfully.'
                        : 'This request is no longer available.';
                }
            } elseif (isset($_POST['reject_service_request'])) {
                $requestId = (int) ($_POST['request_id'] ?? 0);

                if ($requestId <= 0) {
                    $message = 'No request selected.';
                } else {
                    $updated = $this->model->updateServiceRequestStatus($this->providerId, $requestId, 'rejected');
                    $message = $updated
                        ? 'The customer request was rejected.'
                        : 'This request is no longer available.';
                }
            }
        }

        $provider = $this->model->getProfile($this->providerId);
        $incomingRequests = $this->model->getIncomingServiceRequests($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/requests.php';
    }

    /* =========================================================
     *  APPLY FOR A JOB  (form submission, plain POST)
     * ========================================================= */
    private function applyJob(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
            $jobId         = (int) ($_POST['job_id'] ?? 0);
            $coverNote     = trim($_POST['cover_note'] ?? '');
            $proposedPrice = $_POST['proposed_price'] !== '' ? (float) $_POST['proposed_price'] : null;

            $job = $jobId ? $this->model->getJobById($jobId) : null;

            if (!$job) {
                $message = 'Selected job could not be found.';
            } elseif ($coverNote === '') {
                $message = 'Please write a short note explaining why you are a good fit.';
            } else {
                $applied = $this->model->applyForJob($this->providerId, $jobId, $coverNote, $proposedPrice);
                $message = $applied
                    ? 'Application submitted successfully!'
                    : 'You may have already applied for this job.';
            }
        }

        $provider    = $this->model->getProfile($this->providerId);
        $jobs        = $this->model->getAvailableJobs($this->providerId, $provider['profession'] ?? null);
        $appliedJobs = $this->model->getAppliedJobs($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/apply_job.php';
    }

    /* =========================================================
     *  EARNINGS
     * ========================================================= */
    private function earnings(): void
    {
        $provider = $this->model->getProfile($this->providerId);
        $summary  = $this->model->getEarningsSummary($this->providerId);
        $earnings = $this->model->getEarnings($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/earnings.php';
    }
}

// ---- Bootstrap ----
$controller = new ServiceProviderController();
$controller->handleRequest();
