<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/Customer.php';

class CustomerController 
{
    private $customerModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->customerModel = new Customer($db);
    }

  
    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once __DIR__ . '/../View/Customer/customer_dashboard.php';
    }

    public function accountSettings() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (($_POST['settings_section'] ?? '') === 'account') {
                    $phone = trim($_POST['phone'] ?? '');

                    if (!preg_match('/^[0-9]{11}$/', $phone)) {
                        throw new RuntimeException('Phone number must contain exactly 11 digits.');
                    }

                    $this->customerModel->updateAccount(
                        $userId,
                        $phone,
                        $_POST['current_password'] ?? '',
                        $_POST['new_password'] ?? ''
                    );
                } else {
                    $profilePhoto = null;
                    $uploadedFile = $_FILES['profile_photo'] ?? null;
                    if ($uploadedFile && $uploadedFile['error'] !== UPLOAD_ERR_NO_FILE) {
                        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                            throw new RuntimeException('The profile photo upload failed (error code ' . $uploadedFile['error'] . ').');
                        }

                        if (!is_uploaded_file($uploadedFile['tmp_name'])) {
                            throw new RuntimeException('The selected profile photo is not a valid upload.');
                        }

                        if ($uploadedFile['size'] > 5 * 1024 * 1024) {
                            throw new RuntimeException('The profile photo must be 5 MB or smaller.');
                        }

                        $imageType = @exif_imagetype($uploadedFile['tmp_name']);
                        $extensionsByImageType = [
                            IMAGETYPE_JPEG => 'jpg',
                            IMAGETYPE_PNG => 'png',
                            IMAGETYPE_WEBP => 'webp',
                        ];
                        if (!isset($extensionsByImageType[$imageType])) {
                            throw new RuntimeException('Please upload a valid JPG, PNG, or WEBP image.');
                        }

                        // Keep user-uploaded files outside the view assets directory.
                        // This is also the shared writable upload location used by provider profiles.
                        $directory = __DIR__ . '/../uploads/profile_pictures';
                        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                            throw new RuntimeException('The profile-photo upload directory could not be created.');
                        }
                        if (!is_writable($directory)) {
                            throw new RuntimeException('The profile-photo upload directory is not writable by the web server.');
                        }

                        $filename = 'user_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extensionsByImageType[$imageType];
                        if (!move_uploaded_file($uploadedFile['tmp_name'], $directory . '/' . $filename)) {
                            throw new RuntimeException('The profile photo could not be saved.');
                        }

                        $profilePhoto = '/FixLine/uploads/profile_pictures/' . $filename;
                    }

                    $this->customerModel->updateProfile(
                        $userId,
                        trim($_POST['first_name'] ?? ''),
                        trim($_POST['last_name'] ?? ''),
                        trim($_POST['address'] ?? ''),
                        $profilePhoto
                    );
                }

                $_SESSION['settings_message'] = 'Changes saved successfully.';
            } catch (Throwable $exception) {
                $_SESSION['settings_error'] = $exception->getMessage();
            }

            $tab = ($_POST['settings_section'] ?? 'profile') === 'account' ? 'account' : 'profile';
            header("Location: /FixLine/index.php?action=account_settings&tab={$tab}");
            exit();
        }

        $user = $this->customerModel->getUser($userId);
        require_once __DIR__ . '/../View/Customer/account_settings.php';
    }

   
    public function search() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $category = $_GET['category'] ?? null;
        $keyword  = $_GET['search'] ?? null;

        $services = $this->customerModel->searchServices($category, $keyword);
        $jobApplications = $this->customerModel->getJobApplicationsForCustomer(
            (int) ($_SESSION['user_id'] ?? 0),
            $category
        );
        require_once __DIR__ . '/../View/Customer/search_dashboard.php';
    }

    public function requestService() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $services = $this->customerModel->getServiceCatalog();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = $_SESSION['user_id'] ?? 0;
            $providerId = (int) ($_POST['provider_id'] ?? 0);
            $serviceId = (int) ($_POST['service_id'] ?? 0);
            $applicationId = (int) ($_POST['application_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $budgetInput = trim($_POST['budget'] ?? '');
            $budget = $budgetInput === '' ? null : (float) $budgetInput;
            $bookingDate = trim($_POST['booking_date'] ?? '');
            $returnCategory = trim($_POST['return_category'] ?? '');
            $returnSearch = trim($_POST['return_search'] ?? '');
            $returnUrl = '/FixLine/index.php?action=search';

            if ($returnCategory !== '') {
                $returnUrl .= '&category=' . rawurlencode($returnCategory);
            }
            if ($returnSearch !== '') {
                $returnUrl .= '&search=' . rawurlencode($returnSearch);
            }

            if ($description === '' && $title !== '') {
                $description = 'Customer requested: ' . $title;
            }

            if ($customerId <= 0) {
                $_SESSION['booking_error'] = 'Please log in before requesting a service.';
            } elseif ($providerId <= 0 || ($serviceId <= 0 && $applicationId <= 0) || $title === '' || $category === '' || $bookingDate === '') {
                $_SESSION['booking_error'] = 'Please complete all required request fields.';
            } elseif ($budget !== null && $budget < 0) {
                $_SESSION['booking_error'] = 'Budget cannot be negative.';
            } elseif (!DateTime::createFromFormat('Y-m-d', $bookingDate) || $bookingDate < date('Y-m-d')) {
                $_SESSION['booking_error'] = 'Please choose a valid future service date.';
            } else {
                try {
                    $requestData = [
                        'service_id' => $serviceId,
                        'title' => $title,
                        'category' => $category,
                        'description' => $description,
                        'location' => $location,
                        'budget' => $budget,
                        'booking_date' => $bookingDate
                    ];
                    $created = $applicationId > 0
                        ? $this->customerModel->createJobApplicationRequest($customerId, $applicationId, $requestData)
                        : $this->customerModel->createServiceRequest($customerId, $providerId, $requestData);

                    if ($created) {
                        $_SESSION['booking_message'] = 'Request sent';
                        header('Location: ' . $returnUrl);
                        exit();
                    } else {
                        $_SESSION['booking_error'] = 'The selected provider is not available.';
                    }
                } catch (Throwable $exception) {
                    $_SESSION['booking_error'] = 'Unable to send the service request.';
                }
            }

            header('Location: ' . $returnUrl);
            exit();
        }

        require_once __DIR__ . '/../View/Customer/request_service.php';
    }

    public function postJob() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = (int) ($_SESSION['user_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $budgetInput = trim($_POST['budget'] ?? '');
            $budget = $budgetInput === '' ? null : (float) $budgetInput;

            if ($customerId <= 0) {
                $_SESSION['job_error'] = 'Please log in before posting a job.';
            } elseif ($title === '' || $category === '' || $description === '') {
                $_SESSION['job_error'] = 'Please complete all required job fields.';
            } elseif ($budget !== null && $budget < 0) {
                $_SESSION['job_error'] = 'Budget cannot be negative.';
            } else {
                try {
                    $this->customerModel->createJob($customerId, [
                        'title' => $title,
                        'category' => $category,
                        'description' => $description,
                        'location' => $location,
                        'budget' => $budget
                    ]);
                    $_SESSION['job_message'] = 'Job posted successfully.';
                } catch (Throwable $exception) {
                    $_SESSION['job_error'] = 'Unable to post the job.';
                }
            }

            header('Location: /FixLine/index.php?action=post_job');
            exit();
        }

        require_once __DIR__ . '/../View/Customer/post_job.php';
    }

    
    public function book() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId          = $_SESSION['user_id'] ?? 1;
            $serviceId       = $_POST['service_id'];
            $bookingDate     = $_POST['booking_date'];
            $category        = $_POST['category'] ?? '';

            try {
                $bookingCreated = $this->customerModel->createBooking($userId, $serviceId, $bookingDate);

                if ($bookingCreated) {
                    $_SESSION['booking_message'] = 'Booking successful';
                }
            } catch (PDOException $exception) {
                $_SESSION['booking_error'] = $exception->getMessage();
            }

            $categoryQuery = $category !== '' ? '&category=' . rawurlencode($category) : '';
            header("Location: /FixLine/index.php?action=search{$categoryQuery}");
            exit();
        }
    }

  
    public function myBookings() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId   = $_SESSION['user_id'] ?? 1;
        $bookings = $this->customerModel->getCustomerBookings($userId);
        require_once __DIR__ . '/../View/Customer/my_bookings.php';
    }

    public function review() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId  = $_POST['booking_id'];
            $userId     = $_SESSION['user_id'] ?? 1;
            $serviceId  = $_POST['service_id'];
            $rating     = $_POST['rating'];
            $comment    = $_POST['comment'];

            try {
                $reviewSubmitted = $this->customerModel->submitReview($bookingId, $userId, $serviceId, $rating, $comment);

                if ($reviewSubmitted) {
                    $_SESSION['booking_message'] = 'Review submitted successfully';
                }
            } catch (Throwable $exception) {
                $_SESSION['booking_error'] = $exception->getMessage();
            }

            header("Location: /FixLine/index.php?action=my_bookings");
            exit();
        }
    }

    public function cancelBooking() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 1;
            $bookingId = (int) ($_POST['booking_id'] ?? 0);

            if ($this->customerModel->cancelBooking($bookingId, $userId)) {
                $_SESSION['booking_message'] = 'Booking cancelled.';
            } else {
                $_SESSION['booking_error'] = 'This booking cannot be cancelled.';
            }
        }

        header('Location: /FixLine/index.php?action=my_bookings');
        exit();
    }

    public function payment() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? 1;
        $bookingId = (int) ($_POST['booking_id'] ?? 0);

        try {
            if ($this->customerModel->createPayment($bookingId, $userId)) {
                $_SESSION['booking_message'] = 'Payment successful';
            }
        } catch (Throwable $exception) {
            $_SESSION['booking_error'] = $exception->getMessage();
        }

        header('Location: /FixLine/index.php?action=my_bookings');
        exit();
    }

    public function refunds() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentId = (int) ($_POST['payment_id'] ?? 0);
            $reason = trim($_POST['reason'] ?? '');

            if ($reason === '') {
                $_SESSION['refund_error'] = 'Please enter a reason for the refund.';
            } elseif ($this->customerModel->requestRefund($paymentId, $userId, $reason)) {
                $_SESSION['refund_message'] = 'Refund request submitted successfully.';
            } else {
                $_SESSION['refund_error'] = 'This payment is not available for a refund request.';
            }

            header('Location: /FixLine/index.php?action=refunds');
            exit();
        }

        $payments = $this->customerModel->getPaidPayments($userId);
        require_once __DIR__ . '/../View/Customer/refunds.php';
    }

}

?>
