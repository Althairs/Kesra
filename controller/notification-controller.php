<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_unread_count':
        getUnreadCount();
        break;
    case 'get_notifications':
        getNotifications();
        break;
    case 'mark_as_read':
        markAsRead();
        break;
    case 'mark_all_read':
        markAllAsRead();
        break;
    case 'delete_notification':
        deleteNotification();
        break;
    case 'delete_all_read':
        deleteAllRead();
        break;
    case 'get_latest_notifications':
        getLatestNotifications();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Action tidak valid']);
        break;
}

function getUnreadCount() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    try {
        // Gunakan function yang sudah ada
        $result = $koneksi->query("SELECT GetUnreadNotificationCount($user_id) as unread_count");
        $data = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'unread_count' => $data['unread_count'] ?? 0
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function getNotifications() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $limit = intval($_GET['limit'] ?? 20);
    $offset = intval($_GET['offset'] ?? 0);
    $filter = $_GET['filter'] ?? 'all';
    
    try {
        // Gunakan procedure atau query langsung
        $query = "
            SELECT n.*, 
                   p.nama_lembaga,
                   p.status as proposal_status,
                   u.username as sender_username,
                   u.nama_lengkap as sender_name,
                   DATE_FORMAT(n.created_at, '%d %b %Y %H:%i') as formatted_date,
                   TIMESTAMPDIFF(MINUTE, n.created_at, NOW()) as minutes_ago
            FROM notifications n
            LEFT JOIN proposals p ON n.related_id = p.id
            LEFT JOIN users u ON n.sender_id = u.id
            WHERE n.user_id = ?
        ";
        
        // Tambahkan filter
        if ($filter === 'unread') {
            $query .= " AND n.is_read = 0";
        } elseif ($filter !== 'all') {
            $query .= " AND n.type = ?";
        }
        
        $query .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $koneksi->prepare($query);
        
        if ($filter !== 'all') {
            if ($filter === 'unread') {
                $stmt->bind_param("iii", $user_id, $limit, $offset);
            } else {
                $stmt->bind_param("isii", $user_id, $filter, $limit, $offset);
            }
        } else {
            $stmt->bind_param("iii", $user_id, $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            // Format waktu relatif
            $row['time_ago'] = formatTimeAgo($row['minutes_ago']);
            $notifications[] = $row;
        }
        $stmt->close();
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM notifications WHERE user_id = ?";
        if ($filter === 'unread') {
            $countQuery .= " AND is_read = 0";
        } elseif ($filter !== 'all') {
            $countQuery .= " AND type = ?";
        }
        
        $countStmt = $koneksi->prepare($countQuery);
        if ($filter !== 'all') {
            if ($filter === 'unread') {
                $countStmt->bind_param("i", $user_id);
            } else {
                $countStmt->bind_param("is", $user_id, $filter);
            }
        } else {
            $countStmt->bind_param("i", $user_id);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'] ?? 0;
        $countStmt->close();
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'total' => $total,
            'has_more' => ($offset + count($notifications)) < $total
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function getLatestNotifications() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $limit = intval($_GET['limit'] ?? 5);
    $last_check = $_GET['last_check'] ?? date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    try {
        $stmt = $koneksi->prepare("
            SELECT n.*, 
                   p.nama_lembaga,
                   DATE_FORMAT(n.created_at, '%d %b %Y %H:%i') as formatted_date,
                   TIMESTAMPDIFF(SECOND, n.created_at, NOW()) as seconds_ago
            FROM notifications n
            LEFT JOIN proposals p ON n.related_id = p.id
            WHERE n.user_id = ? 
            AND n.created_at > ?
            AND n.is_read = 0
            ORDER BY n.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("isi", $user_id, $last_check, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $row['time_ago'] = formatTimeAgo(floor($row['seconds_ago'] / 60));
            $notifications[] = $row;
        }
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'last_check' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function markAsRead() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $notification_id = intval($_POST['notification_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    if ($notification_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid notification ID']);
        return;
    }
    
    try {
        // OPTION 1: Gunakan query langsung (lebih sederhana)
        $stmt = $koneksi->prepare("
            UPDATE notifications 
            SET is_read = 1, 
                read_at = NOW(),
                updated_at = NOW()
            WHERE id = ? 
            AND user_id = ?
            AND is_read = 0
        ");
        $stmt->bind_param("ii", $notification_id, $user_id);
        
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Notification marked as read',
                'affected_rows' => $affected_rows
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Notification not found or already read'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function markAllAsRead() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $koneksi->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW()
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($koneksi->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function deleteNotification() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $notification_id = intval($_POST['notification_id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $koneksi->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($koneksi->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function deleteAllRead() {
    global $koneksi;
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $koneksi->prepare("DELETE FROM notifications WHERE user_id = ? AND is_read = 1");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($koneksi->error);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function formatTimeAgo($minutes) {
    if ($minutes < 1) {
        return 'Baru saja';
    } elseif ($minutes < 60) {
        return $minutes . ' menit yang lalu';
    } elseif ($minutes < 1440) {
        $hours = floor($minutes / 60);
        return $hours . ' jam yang lalu';
    } else {
        $days = floor($minutes / 1440);
        return $days . ' hari yang lalu';
    }
}

// Helper function untuk membuat notifikasi (tambahan)
function createNotification($user_id, $type, $title, $message, $related_id = null, $sender_id = null) {
    global $koneksi;
    
    try {
        // Gunakan procedure yang sudah ada
        $stmt = $koneksi->prepare("CALL CreateNotification(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssii", $user_id, $type, $title, $message, $related_id, $sender_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $notification_id = $data['notification_id'] ?? null;
            $stmt->close();
            return $notification_id;
        }
        return false;
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}
?>