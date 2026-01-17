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

// Handle different actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_stats':
        getDashboardStats();
        break;
    case 'get_activities':
        getRecentActivities();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action not specified']);
        break;
}

function getDashboardStats() {
    global $koneksi;
    
    try {
        // Total proposals
        $query1 = "SELECT COUNT(*) as total FROM proposals";
        $result1 = mysqli_query($koneksi, $query1);
        $total_proposals = mysqli_fetch_assoc($result1)['total'];

        // Pending proposals
        $query2 = "SELECT COUNT(*) as total FROM proposals WHERE status = 'pending'";
        $result2 = mysqli_query($koneksi, $query2);
        $pending_proposals = mysqli_fetch_assoc($result2)['total'];

        // Total admins
        $query3 = "SELECT COUNT(*) as total FROM users WHERE role = 'admin'";
        $result3 = mysqli_query($koneksi, $query3);
        $total_admins = mysqli_fetch_assoc($result3)['total'];

        // Total news
        $query4 = "SELECT COUNT(*) as total FROM berita WHERE status_berita = 'publish'";
        $result4 = mysqli_query($koneksi, $query4);
        $total_news = mysqli_fetch_assoc($result4)['total'];

        // Total users
        $query5 = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
        $result5 = mysqli_query($koneksi, $query5);
        $total_users = mysqli_fetch_assoc($result5)['total'];

        // Approved proposals
        $query6 = "SELECT COUNT(*) as total FROM proposals WHERE status = 'approved'";
        $result6 = mysqli_query($koneksi, $query6);
        $approved_proposals = mysqli_fetch_assoc($result6)['total'];

        echo json_encode([
            'success' => true,
            'data' => [
                'total_proposals' => (int)$total_proposals,
                'pending_proposals' => (int)$pending_proposals,
                'total_admins' => (int)$total_admins,
                'total_news' => (int)$total_news,
                'total_users' => (int)$total_users,
                'approved_proposals' => (int)$approved_proposals
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch dashboard statistics: ' . $e->getMessage()]);
    }
}

function getRecentActivities() {
    global $koneksi;
    
    $limit = $_GET['limit'] ?? 5;
    
    try {
        $query = "SELECT al.*, u.username 
                 FROM activity_logs al 
                 JOIN users u ON al.user_id = u.id 
                 ORDER BY al.created_at DESC 
                 LIMIT ?";
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $activities = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $activities[] = [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'username' => $row['username'],
                'action' => $row['action'],
                'description' => $row['description'],
                'ip_address' => $row['ip_address'],
                'created_at' => $row['created_at'],
                'formatted_date' => date('d F Y, H:i', strtotime($row['created_at']))
            ];
        }
        
        mysqli_stmt_close($stmt);
        
        echo json_encode([
            'success' => true,
            'data' => $activities
        ]);
        
    } catch (Exception $e) {
        error_log("Error getting recent activities: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch recent activities: ' . $e->getMessage()]);
    }
}
?>