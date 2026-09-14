<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        if (!empty($_SESSION['user_id'])) {
            $providerId = $this->model->getProviderIdByUserId((int) $_SESSION['user_id']);
            if ($providerId !== null) {
                $_SESSION['provider_id'] = $providerId;
                return $providerId;
            }
        }

        return 1;
    }

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
                $this->uploadProfilePicture();
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

    private function dashboard(): void
    {
        $provider = $this->model->getProfile($this->providerId);
        $provider = $this->withProfilePictureUrl($provider);
        $summary  = $this->model->getEarningsSummary($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/dashboard.php';
    }

    private function profile(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
            $serviceId = (int) ($_POST['service_id'] ?? 0);
            $serviceName = trim($_POST['service_name'] ?? '');
            $category = strtolower(trim($_POST['category'] ?? ''));
            $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
            $allowedCategories = ['plumber', 'electrician', 'painter', 'repairer'];

            if ($serviceId <= 0 || $serviceName === '' || !in_array($category, $allowedCategories, true) || $price === false || $price < 0) {
                $message = 'Select an existing service, choose a valid category, and enter a valid cost.';
            } else {
                $this->model->saveService($this->providerId, [
                    'id' => $serviceId,
                    'service_name' => $serviceName,
                    'category' => $category,
                    'description' => trim($_POST['service_description'] ?? ''),
                    'price' => $price
                ]);
                $message = 'Service saved successfully.';
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
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
        $provider = $this->withProfilePictureUrl($provider);
        $providerServices = $this->model->getServices($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/profile.php';
    }

    private function uploadProfilePicture(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['profile_picture'])) {
            echo json_encode(['success' => false, 'message' => 'No file received.']);
            return;
        }

        $file = $_FILES['profile_picture'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSizeBytes = 2 * 1024 * 1024;

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error. Please try again.']);
            return;
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes, true)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG or WEBP images are allowed.']);
            return;
        }

        if ($file['size'] > $maxSizeBytes) {
            echo json_encode(['success' => false, 'message' => 'Image must be smaller than 2MB.']);
            return;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename  = 'provider_' . $this->providerId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

        $uploadDir    = __DIR__ . '/../uploads/profile_pictures/';
        $uploadPathFs = $uploadDir . $filename;
        $relativePath = 'uploads/profile_pictures/' . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if (is_dir($uploadDir) && !is_writable($uploadDir)) {
            @chmod($uploadDir, 0777);
        }

        if (!is_writable($uploadDir)) {
            echo json_encode(['success' => false, 'message' => 'Upload folder is not writable. Please check XAMPP folder permissions.']);
            return;
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPathFs)) {
            echo json_encode(['success' => false, 'message' => 'Could not save the uploaded file.']);
            return;
        }

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
            'imageUrl' => '/FixLine/' . $relativePath,
        ]);
    }

    private function withProfilePictureUrl(?array $provider): ?array
    {
        if (!$provider) {
            return $provider;
        }

        $picture = $provider['profile_picture'] ?? '';
        if ($picture === '') {
            $provider['profile_picture'] = '/FixLine/View/images/plumber.png';
        } elseif (strpos($picture, '/') === 0) {
            $provider['profile_picture'] = $picture;
        } elseif (strpos($picture, 'uploads/') === 0 || strpos($picture, 'View/') === 0) {
            $provider['profile_picture'] = '/FixLine/' . $picture;
        } else {
            $provider['profile_picture'] = '/FixLine/View/' . ltrim($picture, '/');
        }

        return $provider;
    }

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
            } elseif (isset($_POST['complete_service_request'])) {
                $requestId = (int) ($_POST['request_id'] ?? 0);

                if ($requestId <= 0) {
                    $message = 'No request selected.';
                } else {
                    $completed = $this->model->completeServiceRequest($this->providerId, $requestId);
                    $message = $completed
                        ? 'The customer request was marked as completed successfully.'
                        : 'Only an accepted request can be completed.';
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

    private function applyJob(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
            $jobId         = (int) ($_POST['job_id'] ?? 0);
            $serviceId     = (int) ($_POST['service_id'] ?? 0);
            $coverNote     = trim($_POST['cover_note'] ?? '');

            $job = $jobId ? $this->model->getJobById($jobId) : null;
            $service = $serviceId ? $this->model->getService($this->providerId, $serviceId) : null;

            if (!$job) {
                $message = 'Selected job could not be found.';
            } elseif (!$service) {
                $message = 'Please choose one of the services in your profile.';
            } elseif ($coverNote === '') {
                $message = 'Please write a short note explaining why you are a good fit.';
            } else {
                $applied = $this->model->applyForJob(
                    $this->providerId,
                    $jobId,
                    $service['service_name'],
                    $coverNote,
                    (float) $service['price']
                );
                $message = $applied
                    ? 'Application submitted successfully!'
                    : 'You may have already applied for this job.';
            }
        }

        $provider    = $this->model->getProfile($this->providerId);
        $providerServices = $this->model->getServices($this->providerId);
        $jobs        = $this->model->getAvailableJobs($this->providerId, $provider['profession'] ?? null);
        $appliedJobs = $this->model->getAppliedJobs($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/apply_job.php';
    }

    private function earnings(): void
    {
        $provider = $this->model->getProfile($this->providerId);
        $summary  = $this->model->getEarningsSummary($this->providerId);
        $earnings = $this->model->getEarnings($this->providerId);

        require __DIR__ . '/../View/ServiceProvider/earnings.php';
    }
}
