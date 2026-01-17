<?php
session_start();
require_once '../koneksi.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

class ProposalController
{
    private $koneksi;
    private $user_id;
    private $role;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
        $this->user_id = $_SESSION['user_id'] ?? null;
        $this->role = $_SESSION['role'] ?? null;
        
        $this->logAccess();
    }

    private function logAccess()
    {
        error_log("=== PENGJUAN CONTROLLER ACCESSED ===");
        error_log("Time: " . date('Y-m-d H:i:s'));
        error_log("Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("GET Data: " . print_r($_GET, true));
        error_log("POST Data: " . print_r($_POST, true));
        error_log("FILES Data: " . print_r($_FILES, true));
        error_log("Session User ID: " . ($this->user_id ?? 'none'));
        error_log("Session Role: " . ($this->role ?? 'none'));
    }

    public function handleRequest()
    {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->handleUnauthorized();
            return;
        }

        error_log("User authenticated: " . $this->user_id);

        // Route requests based on action and form detection
        $action = $_GET['action'] ?? '';
        
        // Debug: Check what type of request this is
        error_log("Action from GET: " . $action);
        error_log("POST data keys: " . implode(', ', array_keys($_POST)));
        
        // Determine the type of request
        if ($action === 'delete') {
            $this->deleteProposal();
        } elseif ($this->isEditRequest()) {
            error_log("Detected EDIT request");
            $this->handleProposalEdit();
        } elseif ($this->isNewProposalRequest()) {
            error_log("Detected NEW PROPOSAL request");
            $this->handleProposalSubmission();
        } else {
            error_log("Unknown request type - redirecting to status page");
            $_SESSION['error_messages'] = ['Jenis request tidak dikenali'];
            header('Location: ../user/status-proposal.php');
            exit();
        }
    }

    private function isEditRequest()
    {
        // Check if this is an edit submission
        $hasProposalId = isset($_POST['proposal_id']) && !empty($_POST['proposal_id']);
        $hasUpdateButton = isset($_POST['update_proposal']);
        $hasFormData = isset($_POST['nama_lembaga']) && isset($_POST['alamat']);
        
        error_log("Edit request check:");
        error_log(" - hasProposalId: " . ($hasProposalId ? 'YES' : 'NO'));
        error_log(" - hasUpdateButton: " . ($hasUpdateButton ? 'YES' : 'NO'));
        error_log(" - hasFormData: " . ($hasFormData ? 'YES' : 'NO'));
        
        return $hasProposalId && ($hasUpdateButton || $hasFormData);
    }

    private function isNewProposalRequest()
    {
        // Check if this is a new proposal submission
        $hasSubmitButton = isset($_POST['ajukan_proposal']);
        $hasFormData = isset($_POST['nama_lembaga']) && isset($_POST['alamat']);
        $noProposalId = !isset($_POST['proposal_id']) || empty($_POST['proposal_id']);
        
        error_log("New proposal request check:");
        error_log(" - hasSubmitButton: " . ($hasSubmitButton ? 'YES' : 'NO'));
        error_log(" - hasFormData: " . ($hasFormData ? 'YES' : 'NO'));
        error_log(" - noProposalId: " . ($noProposalId ? 'YES' : 'NO'));
        
        return ($hasSubmitButton || $hasFormData) && $noProposalId;
    }

    private function isAuthenticated()
    {
        return isset($this->user_id) && $this->role === 'user';
    }

    private function handleUnauthorized()
    {
        error_log("USER NOT LOGGED IN OR WRONG ROLE");
        
        if ($this->isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        
        $_SESSION['error_messages'] = ['Anda harus login sebagai user untuk mengajukan proposal'];
        header('Location: ../login.php');
        exit();
    }

    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function deleteProposal()
    {
        $proposal_id = $_GET['id'] ?? 0;
        
        error_log("Deleting proposal: $proposal_id for user: {$this->user_id}");
        
        if (!$proposal_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Proposal ID required']);
            exit();
        }
        
        // Check if proposal exists and belongs to user
        $proposal = $this->getUserProposal($proposal_id);
        if (!$proposal) {
            error_log("Proposal not found or not owned by user");
            http_response_code(404);
            echo json_encode(['error' => 'Proposal tidak ditemukan']);
            exit();
        }
        
        // Only allow deletion of pending proposals
        if ($proposal['status'] !== 'pending') {
            error_log("Cannot delete proposal with status: " . $proposal['status']);
            http_response_code(403);
            echo json_encode(['error' => 'Hanya proposal dengan status Menunggu yang dapat dihapus']);
            exit();
        }
        
        // Delete document file if exists
        $this->deleteProposalFile($proposal['dokumen']);
        
        // Delete proposal from database
        if ($this->deleteProposalFromDB($proposal_id)) {
            $this->logActivity('delete_proposal', "Menghapus proposal ID: " . $proposal_id);
            error_log("Proposal deleted successfully");
            echo json_encode(['success' => true, 'message' => 'Proposal berhasil dihapus']);
        } else {
            $error_msg = "Failed to delete proposal: " . mysqli_error($this->koneksi);
            error_log($error_msg);
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menghapus proposal: ' . $error_msg]);
        }
        exit();
    }

    private function handleProposalEdit()
    {
        // Check if form is submitted with POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("INVALID METHOD: " . $_SERVER['REQUEST_METHOD']);
            $_SESSION['error_messages'] = ['Akses tidak valid.'];
            header('Location: ../user/status-proposal.php');
            exit();
        }

        // Get proposal ID
        $proposal_id = $_POST['proposal_id'] ?? 0;

        error_log("Editing proposal ID: " . $proposal_id . " for user: " . $this->user_id);

        if (!$proposal_id) {
            error_log("PROPOSAL ID NOT PROVIDED");
            $_SESSION['error_messages'] = ['ID Proposal tidak valid.'];
            header('Location: ../user/status-proposal.php');
            exit();
        }

        // Check if proposal exists and belongs to user
        $existing_proposal = $this->getUserProposalFull($proposal_id);
        if (!$existing_proposal) {
            error_log("PROPOSAL NOT FOUND OR NOT OWNED BY USER");
            $_SESSION['error_messages'] = ['Proposal tidak ditemukan.'];
            header('Location: ../user/status-proposal.php');
            exit();
        }

        // Check if proposal is still in pending status
        if ($existing_proposal['status'] !== 'pending') {
            error_log("PROPOSAL CANNOT BE EDITED - STATUS: " . $existing_proposal['status']);
            $_SESSION['error_messages'] = ['Hanya proposal dengan status Menunggu yang dapat diedit.'];
            header('Location: ../user/status-proposal.php');
            exit();
        }

        error_log("Proposal found and editable - proceeding with update");

        // Validate and process the update
        $this->processProposalUpdate($proposal_id, $existing_proposal);
    }

    private function handleProposalSubmission()
    {
        // Check if form is submitted with POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("INVALID METHOD: " . $_SERVER['REQUEST_METHOD']);
            $_SESSION['error_messages'] = ['Akses tidak valid. Silakan gunakan form untuk mengajukan proposal.'];
            header('Location: ../user/ajukan-proposal.php');
            exit();
        }

        error_log("FORM SUBMISSION VALID - PROCESSING NEW PROPOSAL...");
        
        // Validate and process new proposal
        $this->processNewProposal();
    }

    private function processProposalUpdate($proposal_id, $existing_proposal)
    {
        // Validate input
        $errors = $this->validateProposalData();
        $form_data = $this->getFormData();

        // Handle file upload
        $dokumen_path = $existing_proposal['dokumen']; // Keep existing file by default
        $file_errors = $this->handleFileUpload($dokumen_path, $existing_proposal['dokumen']);
        $errors = array_merge($errors, $file_errors);

        // If there are errors, redirect back with error messages
        if (!empty($errors)) {
            error_log("VALIDATION ERRORS: " . implode(", ", $errors));
            $_SESSION['error_messages'] = $errors;
            $_SESSION['form_data'] = $form_data;
            header('Location: ../user/edit-proposal.php?id=' . $proposal_id);
            exit();
        }

        // If no errors, update database
        error_log("No validation errors - updating database");
        $this->updateProposalInDB($proposal_id, $form_data, $dokumen_path);
    }

    private function processNewProposal()
    {
        // Validate input
        $errors = $this->validateProposalData();
        $form_data = $this->getFormData();

        // Handle file upload
        $dokumen_path = null;
        $file_errors = $this->handleFileUpload($dokumen_path);
        $errors = array_merge($errors, $file_errors);

        // If there are errors, redirect back with error messages
        if (!empty($errors)) {
            error_log("VALIDATION ERRORS: " . implode(", ", $errors));
            $_SESSION['error_messages'] = $errors;
            $_SESSION['form_data'] = $form_data;
            header('Location: ../user/ajukan-proposal.php');
            exit();
        }

        // If no errors, save to database
        error_log("No validation errors - saving to database");
        $this->saveProposalToDB($form_data, $dokumen_path);
    }

    private function validateProposalData()
    {
        $errors = [];
        $data = $this->getFormData();

        // Validate required fields
        if (empty(trim($data['nama_lembaga']))) $errors[] = "Nama lembaga harus diisi";
        if (empty(trim($data['alamat']))) $errors[] = "Alamat harus diisi";
        if (empty($data['jenis_bantuan'])) $errors[] = "Jenis bantuan harus dipilih";
        if (empty($data['jumlah_diajukan']) || $data['jumlah_diajukan'] <= 0) $errors[] = "Jumlah yang diajukan harus diisi dengan nilai yang valid";
        if (empty(trim($data['deskripsi']))) $errors[] = "Deskripsi proposal harus diisi";
        if (empty($data['bank'])) $errors[] = "Bank harus dipilih";
        if (empty(trim($data['nomor_rekening']))) $errors[] = "Nomor rekening harus diisi";

        // Validate account number format
        if (!empty($data['nomor_rekening']) && !is_numeric($data['nomor_rekening'])) {
            $errors[] = "Nomor rekening harus berupa angka";
        }

        return $errors;
    }

    private function getFormData()
    {
        return [
            'nama_lembaga' => mysqli_real_escape_string($this->koneksi, $_POST['nama_lembaga'] ?? ''),
            'alamat' => mysqli_real_escape_string($this->koneksi, $_POST['alamat'] ?? ''),
            'jenis_bantuan' => mysqli_real_escape_string($this->koneksi, $_POST['jenis_bantuan'] ?? ''),
            'jumlah_diajukan' => mysqli_real_escape_string($this->koneksi, $_POST['jumlah_diajukan'] ?? ''),
            'deskripsi' => mysqli_real_escape_string($this->koneksi, $_POST['deskripsi'] ?? ''),
            'bank' => mysqli_real_escape_string($this->koneksi, $_POST['bank'] ?? ''),
            'nomor_rekening' => mysqli_real_escape_string($this->koneksi, $_POST['nomor_rekening'] ?? '')
        ];
    }

    private function handleFileUpload(&$dokumen_path, $existing_file = null)
    {
        $errors = [];

        if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['dokumen'];
            error_log("File upload detected: " . $file['name']);
            
            // Validate file
            $validation_result = $this->validateFile($file);
            if ($validation_result !== true) {
                $errors[] = $validation_result;
                return $errors;
            }
            
            // Upload file
            $upload_result = $this->uploadFile($file, $dokumen_path);
            if ($upload_result !== true) {
                $errors[] = $upload_result;
            } else {
                // Delete old file if new file uploaded successfully
                if ($existing_file && file_exists('../' . $existing_file)) {
                    unlink('../' . $existing_file);
                    error_log("Deleted old file: " . $existing_file);
                }
            }
        } elseif (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = $this->getUploadErrorMessage($_FILES['dokumen']['error']);
        } else {
            error_log("No file uploaded - keeping existing file");
            // Keep existing file path
            $dokumen_path = $existing_file;
        }

        return $errors;
    }

    private function validateFile($file)
    {
        $allowed_types = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
            'image/jpeg', 
            'image/jpg',
            'image/png'
        ];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        error_log("File type detected: " . $file_type);
        error_log("File size: " . $file['size']);
        
        if (!in_array($file_type, $allowed_types)) {
            return "Format file tidak didukung. Hanya PDF, DOC, DOCX, JPG, PNG yang diizinkan. Tipe file: " . $file_type;
        }
        
        if ($file['size'] > $max_size) {
            return "Ukuran file terlalu besar. Maksimal 5MB. Ukuran file: " . round($file['size'] / 1024 / 1024, 2) . "MB";
        }
        
        return true;
    }

    private function uploadFile($file, &$dokumen_path)
    {
        // Create directory if not exists
        $upload_dir = '../berkas/';
        if (!is_dir($upload_dir)) {
            error_log("Creating upload directory: " . $upload_dir);
            if (!mkdir($upload_dir, 0755, true)) {
                return "Gagal membuat folder berkas";
            }
        }
        
        if (!is_dir($upload_dir)) {
            return "Folder upload tidak tersedia";
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'proposal_' . time() . '_' . $this->user_id . '.' . $file_extension;
        $full_path = $upload_dir . $filename;
        
        error_log("Attempting to move file to: " . $full_path);
        
        // Move file
        if (!move_uploaded_file($file['tmp_name'], $full_path)) {
            return "Gagal mengupload dokumen. Pastikan folder berkas memiliki izin write";
        }
        
        // Save relative path to database
        $dokumen_path = 'berkas/' . $filename;
        error_log("File uploaded successfully: " . $dokumen_path);
        
        return true;
    }

    private function getUploadErrorMessage($error_code)
    {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi ukuran maksimal server)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi ukuran maksimal form)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];
        
        return $upload_errors[$error_code] ?? 'Error tidak diketahui saat upload file (Code: ' . $error_code . ')';
    }

    private function updateProposalInDB($proposal_id, $form_data, $dokumen_path)
    {
        // Check database connection
        if (!$this->koneksi) {
            error_log("Database connection failed");
            $_SESSION['error_messages'] = ['Koneksi database gagal'];
            header('Location: ../user/edit-proposal.php?id=' . $proposal_id);
            exit();
        }

        $query = "UPDATE proposals SET 
            nama_lembaga = ?,
            alamat = ?,
            jenis_bantuan = ?,
            jumlah_diajukan = ?,
            deskripsi = ?,
            dokumen = ?,
            bank = ?,
            nomor_rekening = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ? AND id_user = ?";

        error_log("SQL Update Query: " . $query);

        $stmt = mysqli_prepare($this->koneksi, $query);
        if (!$stmt) {
            $this->handleDatabaseError("Gagal mempersiapkan query", $proposal_id);
            return;
        }

        mysqli_stmt_bind_param($stmt, "ssssssssii", 
            $form_data['nama_lembaga'], 
            $form_data['alamat'], 
            $form_data['jenis_bantuan'], 
            $form_data['jumlah_diajukan'],
            $form_data['deskripsi'], 
            $dokumen_path, 
            $form_data['bank'], 
            $form_data['nomor_rekening'],
            $proposal_id, 
            $this->user_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            error_log("Proposal updated successfully. Affected rows: " . $affected_rows);
            
            $this->logActivity('edit_proposal', "Mengedit proposal: " . $form_data['nama_lembaga'] . " (ID: " . $proposal_id . ")");
            
            $_SESSION['success_message'] = "Proposal berhasil diperbarui!";
            mysqli_stmt_close($stmt);
            
            // Clear form data from session
            unset($_SESSION['form_data']);
            
            header('Location: ../user/status-proposal.php?success=1');
            exit();
        } else {
            $this->handleDatabaseError("Gagal memperbarui proposal", $proposal_id, $stmt);
        }
        
        mysqli_stmt_close($stmt);
    }

    private function saveProposalToDB($form_data, $dokumen_path)
    {
        $tanggal_pengajuan = date('Y-m-d H:i:s');

        // Check database connection
        if (!$this->koneksi) {
            error_log("Database connection failed");
            $_SESSION['error_messages'] = ['Koneksi database gagal'];
            header('Location: ../user/ajukan-proposal.php');
            exit();
        }

        $query = "INSERT INTO proposals (
            id_user, nama_lembaga, alamat, jenis_bantuan, jumlah_diajukan, 
            deskripsi, dokumen, bank, nomor_rekening, created_at, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        error_log("SQL Query: " . $query);

        $stmt = mysqli_prepare($this->koneksi, $query);
        if (!$stmt) {
            $this->handleDatabaseError("Gagal mempersiapkan query");
            return;
        }

        mysqli_stmt_bind_param($stmt, "isssisssss", 
            $this->user_id, 
            $form_data['nama_lembaga'], 
            $form_data['alamat'], 
            $form_data['jenis_bantuan'], 
            $form_data['jumlah_diajukan'],
            $form_data['deskripsi'], 
            $dokumen_path, 
            $form_data['bank'], 
            $form_data['nomor_rekening'], 
            $tanggal_pengajuan
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $proposal_id = mysqli_insert_id($this->koneksi);
            error_log("Proposal saved successfully. ID: " . $proposal_id);
            
            $this->logActivity('submit_proposal', "Mengajukan proposal: " . $form_data['nama_lembaga'] . " (ID: " . $proposal_id . ")");
            
            $_SESSION['success_message'] = "Proposal berhasil diajukan! ID Proposal: #" . $proposal_id;
            mysqli_stmt_close($stmt);
            
            // Clear form data from session
            unset($_SESSION['form_data']);
            
            header('Location: ../user/status-proposal.php?success=1');
            exit();
        } else {
            // Delete uploaded file if database save fails
            if ($dokumen_path && file_exists('../' . $dokumen_path)) {
                unlink('../' . $dokumen_path);
                error_log("Deleted uploaded file due to database error");
            }
            
            $this->handleDatabaseError("Gagal menyimpan proposal");
        }
        
        mysqli_stmt_close($stmt);
    }

    private function handleDatabaseError($message, $proposal_id = null, $stmt = null)
    {
        $error_msg = $message . ": " . mysqli_error($this->koneksi);
        error_log($error_msg);
        
        $_SESSION['error_messages'] = [$error_msg];
        
        if ($proposal_id) {
            header('Location: ../user/edit-proposal.php?id=' . $proposal_id);
        } else {
            header('Location: ../user/ajukan-proposal.php');
        }
        exit();
    }

    private function getUserProposal($proposal_id)
    {
        $query = "SELECT id, dokumen, status FROM proposals WHERE id = ? AND id_user = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $proposal_id, $this->user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $proposal = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $proposal;
    }

    private function getUserProposalFull($proposal_id)
    {
        $query = "SELECT * FROM proposals WHERE id = ? AND id_user = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $proposal_id, $this->user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $proposal = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $proposal;
    }

    private function deleteProposalFile($file_path)
    {
        if (!empty($file_path) && file_exists('../' . $file_path)) {
            if (unlink('../' . $file_path)) {
                error_log("Deleted file: " . $file_path);
            } else {
                error_log("Failed to delete file: " . $file_path);
            }
        }
    }

    private function deleteProposalFromDB($proposal_id)
    {
        $query = "DELETE FROM proposals WHERE id = ? AND id_user = ?";
        $stmt = mysqli_prepare($this->koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $proposal_id, $this->user_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    private function logActivity($action, $description = '')
    {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issss", $this->user_id, $action, $description, $ip_address, $user_agent);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            error_log("ACTIVITY LOGGED: User {$this->user_id} - $action - $description");
        }
    }
}

// Initialize and run the controller
try {
    $controller = new ProposalController($koneksi);
    $controller->handleRequest();
} catch (Exception $e) {
    error_log("Controller error: " . $e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan sistem. Silakan coba lagi.";
}
?>