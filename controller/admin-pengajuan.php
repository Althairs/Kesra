<?php
session_start();
require_once '../koneksi.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - Admin access required']);
    exit();
}

// Log access for debugging
error_log("=== ADMIN PENGJUAN CONTROLLER ACCESSED ===");
error_log("Time: " . date('Y-m-d H:i:s'));
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("GET Data: " . print_r($_GET, true));
error_log("POST Data: " . print_r($_POST, true));
error_log("Session User ID: " . $_SESSION['user_id']);
error_log("Session Role: " . $_SESSION['role']);

// Handle different actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_proposals':
        getProposals();
        break;
    case 'get_proposal_detail':
        getProposalDetail();
        break;
    case 'update_status':
        updateProposalStatus();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action not specified']);
        break;
}

function getProposals() {
    global $koneksi;
    
    $filter = $_GET['filter'] ?? 'all';
    
    // Base query
    $query = "SELECT p.*, u.username, u.nama_lengkap, u.email 
              FROM proposals p 
              JOIN users u ON p.id_user = u.id 
              WHERE 1=1";
    
    // Add filter condition
    if ($filter !== 'all') {
        $query .= " AND p.status = ?";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    try {
        $stmt = mysqli_prepare($koneksi, $query);
        
        if ($filter !== 'all') {
            mysqli_stmt_bind_param($stmt, "s", $filter);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $proposals = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Format data untuk response
            $proposals[] = [
                'id' => $row['id'],
                'nama_lembaga' => $row['nama_lembaga'],
                'alamat' => $row['alamat'],
                'jenis_bantuan' => $row['jenis_bantuan'],
                'jenis_bantuan_text' => getJenisBantuanText($row['jenis_bantuan']),
                'jumlah_diajukan' => $row['jumlah_diajukan'],
                'jumlah_diajukan_formatted' => 'Rp ' . number_format($row['jumlah_diajukan'], 0, ',', '.'),
                'deskripsi' => $row['deskripsi'],
                'bank' => $row['bank'],
                'nomor_rekening' => $row['nomor_rekening'],
                'dokumen' => $row['dokumen'],
                'status' => $row['status'],
                'status_text' => getStatusText($row['status']),
                'catatan_admin' => $row['catatan_admin'],
                'created_at' => $row['created_at'],
                'created_at_formatted' => date('d F Y H:i', strtotime($row['created_at'])),
                'updated_at' => $row['updated_at'],
                'updated_at_formatted' => $row['updated_at'] ? date('d F Y H:i', strtotime($row['updated_at'])) : null,
                'user' => [
                    'username' => $row['username'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'email' => $row['email']
                ]
            ];
        }
        
        mysqli_stmt_close($stmt);
        
        echo json_encode([
            'success' => true,
            'data' => $proposals,
            'total' => count($proposals)
        ]);
        
    } catch (Exception $e) {
        error_log("Error getting proposals: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch proposals: ' . $e->getMessage()]);
    }
}

function getProposalDetail() {
    global $koneksi;
    
    $proposal_id = $_GET['id'] ?? 0;
    
    if (!$proposal_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Proposal ID required']);
        return;
    }
    
    try {
        $query = "SELECT p.*, u.username, u.nama_lengkap, u.email, u.telepon 
                  FROM proposals p 
                  JOIN users u ON p.id_user = u.id 
                  WHERE p.id = ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $proposal_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $proposal = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (!$proposal) {
            http_response_code(404);
            echo json_encode(['error' => 'Proposal not found']);
            return;
        }
        
        // Format data untuk response
        $formatted_proposal = [
            'id' => $proposal['id'],
            'nama_lembaga' => $proposal['nama_lembaga'],
            'alamat' => $proposal['alamat'],
            'jenis_bantuan' => $proposal['jenis_bantuan'],
            'jenis_bantuan_text' => getJenisBantuanText($proposal['jenis_bantuan']),
            'jumlah_diajukan' => $proposal['jumlah_diajukan'],
            'jumlah_diajukan_formatted' => 'Rp ' . number_format($proposal['jumlah_diajukan'], 0, ',', '.'),
            'deskripsi' => $proposal['deskripsi'],
            'bank' => $proposal['bank'],
            'nomor_rekening' => $proposal['nomor_rekening'],
            'dokumen' => $proposal['dokumen'],
            'status' => $proposal['status'],
            'status_text' => getStatusText($proposal['status']),
            'catatan_admin' => $proposal['catatan_admin'],
            'created_at' => $proposal['created_at'],
            'created_at_formatted' => date('d F Y H:i', strtotime($proposal['created_at'])),
            'updated_at' => $proposal['updated_at'],
            'updated_at_formatted' => $proposal['updated_at'] ? date('d F Y H:i', strtotime($proposal['updated_at'])) : null,
            'user' => [
                'username' => $proposal['username'],
                'nama_lengkap' => $proposal['nama_lengkap'],
                'email' => $proposal['email'],
                'telepon' => $proposal['telepon']
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $formatted_proposal
        ]);
        
    } catch (Exception $e) {
        error_log("Error getting proposal detail: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch proposal details: ' . $e->getMessage()]);
    }
}

function updateProposalStatus() {
    global $koneksi;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $proposal_id = $_POST['proposal_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $catatan_admin = $_POST['catatan_admin'] ?? '';
    
    if (!$proposal_id || !$status) {
        http_response_code(400);
        echo json_encode(['error' => 'Proposal ID and status are required']);
        return;
    }
    
    // Validate status
    $allowed_statuses = ['pending', 'review', 'approved', 'rejected'];
    if (!in_array($status, $allowed_statuses)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status']);
        return;
    }
    
    try {
        // Check if proposal exists
        $check_query = "SELECT id FROM proposals WHERE id = ?";
        $check_stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $proposal_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            mysqli_stmt_close($check_stmt);
            http_response_code(404);
            echo json_encode(['error' => 'Proposal not found']);
            return;
        }
        mysqli_stmt_close($check_stmt);
        
        // Update proposal status
        $update_query = "UPDATE proposals SET 
                        status = ?, 
                        catatan_admin = ?, 
                        reviewed_by = ?, 
                        reviewed_at = NOW(),
                        updated_at = NOW()
                        WHERE id = ?";
        
        $stmt = mysqli_prepare($koneksi, $update_query);
        $admin_id = $_SESSION['user_id'];
        mysqli_stmt_bind_param($stmt, "ssii", $status, $catatan_admin, $admin_id, $proposal_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            
            // Log activity
            logActivity('update_proposal_status', "Mengupdate status proposal ID: $proposal_id menjadi: $status");
            
            echo json_encode([
                'success' => true,
                'message' => 'Status proposal berhasil diperbarui',
                'affected_rows' => $affected_rows
            ]);
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt);
            throw new Exception($error_msg);
        }
        
    } catch (Exception $e) {
        error_log("Error updating proposal status: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update proposal status: ' . $e->getMessage()]);
    }
}

// Helper functions
function getStatusText($status) {
    $status_map = [
        'pending' => 'Menunggu Review',
        'review' => 'Dalam Review',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak'
    ];
    return $status_map[$status] ?? $status;
}

function getJenisBantuanText($jenis) {
    $jenis_map = [
        'renovasi' => 'Renovasi Bangunan',
        'sarana' => 'Sarana Ibadah',
        'pendidikan' => 'Pendidikan Agama',
        'lainnya' => 'Lainnya'
    ];
    return $jenis_map[$jenis] ?? $jenis;
}

function logActivity($action, $description = '') {
    global $koneksi;
    
    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($koneksi, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $action, $description, $ip_address, $user_agent);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        error_log("ACTIVITY LOGGED: User {$user_id} - $action - $description");
    }
}
?>