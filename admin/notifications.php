<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil jumlah notifikasi yang belum dibaca
$user_id = $_SESSION['user_id'];
$unread_stmt = $koneksi->prepare("
    SELECT COUNT(*) as unread_count 
    FROM notifications 
    WHERE user_id = ? AND is_read = 0
");
$unread_stmt->bind_param("i", $user_id);
$unread_stmt->execute();
$unread_result = $unread_stmt->get_result();
$unread_data = $unread_result->fetch_assoc();
$unread_count = $unread_data['unread_count'] ?? 0;
$unread_stmt->close();

$page_title = 'Notifikasi - Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .notification-badge {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            position: absolute;
            right: 1rem;
            animation: pulse 2s infinite;
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
            display:
                <?php echo $unread_count > 0 ? 'inline-flex' : 'none'; ?>
            ;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Tambahkan CSS untuk halaman notifikasi dari kode sebelumnya */
        /* (Copy semua CSS dari file notifications.php yang sebelumnya) */
        :root {
            --notification-primary: #3B82F6;
            --notification-success: #10B981;
            --notification-warning: #F59E0B;
            --notification-danger: #EF4444;
            --notification-info: #8B5CF6;
        }

        .notifications-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .notifications-header {
            background: linear-gradient(135deg, var(--notification-primary), #1D4ED8);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .notification-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background: #f9fafb;
        }

        .notification-item.unread {
            background: #f0f9ff;
            border-left: 4px solid var(--notification-primary);
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .notification-icon.pengajuan_baru {
            background: linear-gradient(135deg, var(--notification-success), #059669);
            color: white;
        }

        .notification-icon.status_update {
            background: linear-gradient(135deg, var(--notification-info), #7C3AED);
            color: white;
        }

        .notification-icon.system {
            background: linear-gradient(135deg, var(--notification-warning), #D97706);
            color: white;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.25rem;
        }

        .notification-message {
            color: #6B7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .notification-time {
            color: #9CA3AF;
            font-size: 0.75rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .notification-item:hover .notification-actions {
            opacity: 1;
        }

        .notification-action-btn {
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.3s ease;
        }

        .notification-action-btn:hover {
            color: #374151;
            background: #f3f4f6;
        }

        .notification-action-btn.delete:hover {
            color: #EF4444;
            background: #FEE2E2;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6B7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #D1D5DB;
        }

        .empty-state h3 {
            margin: 0 0 0.5rem 0;
            color: #374151;
            font-size: 1.25rem;
        }

        .empty-state p {
            margin: 0 0 1.5rem 0;
        }

        .load-more {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #f3f4f6;
        }

        .load-more-btn {
            background: #f3f4f6;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: #374151;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .load-more-btn:hover {
            background: #e5e7eb;
        }

        .load-more-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6B7280;
        }

        .loading i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        /* Toast Notification */
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease;
            border-left: 4px solid var(--notification-primary);
            cursor: pointer;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-content {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .toast-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            background: var(--notification-primary);
        }

        .toast-message {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .toast-text {
            color: #6B7280;
            font-size: 0.75rem;
            margin: 0;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 0.25rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: #374151;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .notifications-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .header-actions {
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .action-btn {
                flex: 1;
                min-width: 120px;
                justify-content: center;
            }

            .notification-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .notification-actions {
                opacity: 1;
                align-self: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'komponen/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Notifikasi</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </header>

            <!-- Toast Container -->
            <div id="toastContainer"></div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="section-header">
                    <h2>Notifikasi</h2>
                    <p>Lihat semua notifikasi dan pembaruan terkini</p>
                </div>

                <div class="notifications-container">
                    <div class="notifications-header">
                        <h2>Notifikasi</h2>
                        <div class="header-actions">
                            <button class="action-btn" onclick="markAllAsRead()">
                                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                            </button>
                            <button class="action-btn" onclick="refreshNotifications()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="notification-list" id="notificationList">
                        <!-- Notifikasi akan dimuat di sini -->
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Memuat notifikasi...</p>
                        </div>
                    </div>

                    <div class="load-more" style="display: none;" id="loadMoreContainer">
                        <button class="load-more-btn" onclick="loadMoreNotifications()" id="loadMoreBtn">
                            <i class="fas fa-plus"></i> Muat Lebih Banyak
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Audio untuk notifikasi -->
    <audio id="notificationSound" preload="auto">
        <source src="../komponen/sounds/notification.mp3" type="audio/mpeg">
    </audio>

    <script src="../js/dashboard-admin.js"></script>
    <script>
        let currentOffset = 0;
        let isLoading = false;
        let hasMore = true;
        let notifications = [];
        let unreadCount = <?php echo $unread_count; ?>;
        let lastCheckTime = new Date().toISOString();

        document.addEventListener('DOMContentLoaded', function () {
            loadNotifications();
            startNotificationPolling();
            setupNotificationSound();
        });

        function loadNotifications() {
            if (isLoading) return;

            isLoading = true;

            fetch(`../controller/notification-controller.php?action=get_notifications&offset=${currentOffset}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (currentOffset === 0) {
                            notifications = data.notifications;
                            renderNotifications();
                        } else {
                            notifications = [...notifications, ...data.notifications];
                            appendNotifications(data.notifications);
                        }

                        hasMore = data.has_more;
                        updateLoadMoreButton();
                        updateUnreadCount();
                    } else {
                        showError('Gagal memuat notifikasi');
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    showError('Gagal memuat notifikasi');
                })
                .finally(() => {
                    isLoading = false;
                });
        }

        function renderNotifications() {
            const container = document.getElementById('notificationList');

            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="far fa-bell-slash"></i>
                        <h3>Tidak Ada Notifikasi</h3>
                        <p>Belum ada notifikasi untuk Anda</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = notifications.map(notification => `
                <div class="notification-item ${notification.is_read ? 'read' : 'unread'}" 
                     data-id="${notification.id}"
                     onclick="viewNotification(${notification.id}, ${notification.related_id || 'null'})">
                    <div class="notification-icon ${notification.type}">
                        <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${escapeHtml(notification.title)}</div>
                        <div class="notification-message">${escapeHtml(notification.message)}</div>
                        ${notification.nama_lembaga ? `
                        <div class="notification-message">
                            <small><strong>Lembaga:</strong> ${escapeHtml(notification.nama_lembaga)}</small>
                        </div>
                        ` : ''}
                        <div class="notification-meta">
                            <div class="notification-time" title="${notification.formatted_date}">
                                <i class="far fa-clock"></i> ${notification.time_ago}
                            </div>
                            <div class="notification-actions">
                                ${!notification.is_read ? `
                                <button class="notification-action-btn" 
                                        onclick="event.stopPropagation(); markAsRead(${notification.id})"
                                        title="Tandai telah dibaca">
                                    <i class="far fa-check-circle"></i>
                                    <span>Tandai Dibaca</span>
                                </button>
                                ` : ''}
                                <button class="notification-action-btn delete" 
                                        onclick="event.stopPropagation(); deleteNotification(${notification.id})"
                                        title="Hapus notifikasi">
                                    <i class="far fa-trash-alt"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function appendNotifications(newNotifications) {
            const container = document.getElementById('notificationList');

            if (container.querySelector('.empty-state')) {
                renderNotifications();
                return;
            }

            newNotifications.forEach(notification => {
                const element = document.createElement('div');
                element.className = `notification-item ${notification.is_read ? 'read' : 'unread'}`;
                element.dataset.id = notification.id;
                element.onclick = () => viewNotification(notification.id, notification.related_id);

                element.innerHTML = `
                    <div class="notification-icon ${notification.type}">
                        <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${escapeHtml(notification.title)}</div>
                        <div class="notification-message">${escapeHtml(notification.message)}</div>
                        <div class="notification-meta">
                            <div class="notification-time" title="${notification.formatted_date}">
                                <i class="far fa-clock"></i> ${notification.time_ago}
                            </div>
                            <div class="notification-actions">
                                ${!notification.is_read ? `
                                <button class="notification-action-btn" 
                                        onclick="event.stopPropagation(); markAsRead(${notification.id})"
                                        title="Tandai telah dibaca">
                                    <i class="far fa-check-circle"></i>
                                </button>
                                ` : ''}
                                <button class="notification-action-btn delete" 
                                        onclick="event.stopPropagation(); deleteNotification(${notification.id})"
                                        title="Hapus notifikasi">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(element);
            });
        }

        function getNotificationIcon(type) {
            const icons = {
                'pengajuan_baru': 'file-import',
                'status_update': 'sync-alt',
                'system': 'cog'
            };
            return icons[type] || 'bell';
        }

        function viewNotification(notificationId, proposalId) {
            // Tandai sebagai dibaca
            if (document.querySelector(`.notification-item[data-id="${notificationId}"]`).classList.contains('unread')) {
                markAsRead(notificationId);
            }

            // Jika terkait proposal, arahkan ke halaman lihat pengajuan
            if (proposalId) {
                window.location.href = `lihat-pengajuan.php#proposal-${proposalId}`;
            }
        }

        function loadMoreNotifications() {
            if (isLoading || !hasMore) return;

            currentOffset += 20;
            loadNotifications();
        }

        function updateLoadMoreButton() {
            const container = document.getElementById('loadMoreContainer');
            const button = document.getElementById('loadMoreBtn');

            if (hasMore && notifications.length > 0) {
                container.style.display = 'block';
                button.disabled = isLoading;
            } else {
                container.style.display = 'none';
            }
        }

        function markAsRead(notificationId) {
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            fetch('../controller/notification-controller.php?action=mark_as_read', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                        if (item) {
                            item.classList.remove('unread');
                            item.classList.add('read');
                            const markBtn = item.querySelector('.notification-action-btn .fa-check-circle');
                            if (markBtn) {
                                markBtn.closest('.notification-action-btn').remove();
                            }

                            // Update counts
                            unreadCount = Math.max(0, unreadCount - 1);
                            updateUnreadCount();
                        }
                    } else {
                        console.error('Failed to mark as read:', data.error);
                        // Optional: Show error message to user
                        showToast('Gagal menandai sebagai dibaca: ' + (data.error || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error marking as read:', error);
                    showToast('Terjadi kesalahan saat menandai sebagai dibaca', 'error');
                });
        }

        function markAllAsRead() {
            if (!confirm('Tandai semua notifikasi sebagai telah dibaca?')) return;

            fetch('../controller/notification-controller.php?action=mark_all_read', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            item.classList.add('read');
                            const markBtn = item.querySelector('.notification-action-btn .fa-check-circle');
                            if (markBtn) {
                                markBtn.closest('.notification-action-btn').remove();
                            }
                        });

                        // Update counts
                        unreadCount = 0;
                        updateUnreadCount();
                        showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
                    }
                })
                .catch(error => console.error('Error marking all as read:', error));
        }

        function deleteNotification(notificationId) {
            if (!confirm('Hapus notifikasi ini?')) return;

            const formData = new FormData();
            formData.append('notification_id', notificationId);

            fetch('../controller/notification-controller.php?action=delete_notification', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                        if (item) {
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                item.remove();
                                notifications = notifications.filter(n => n.id != notificationId);

                                // Update UI if empty
                                if (notifications.length === 0) {
                                    renderNotifications();
                                }

                                // Update counts
                                unreadCount = notifications.filter(n => !n.is_read).length;
                                updateUnreadCount();
                            }, 300);
                        }
                    }
                })
                .catch(error => console.error('Error deleting notification:', error));
        }

        function refreshNotifications() {
            currentOffset = 0;
            loadNotifications();
            showToast('Notifikasi diperbarui', 'info');
        }

        function updateUnreadCount() {
            // Update sidebar badge
            const badges = document.querySelectorAll('.notification-badge');
            if (unreadCount > 0) {
                badges.forEach(badge => {
                    badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badge.style.display = 'inline-flex';
                });
            } else {
                badges.forEach(badge => {
                    badge.style.display = 'none';
                });
            }
        }

        function startNotificationPolling() {
            // Check for new notifications every 30 seconds
            setInterval(checkNewNotifications, 30000);

            // Check when page becomes visible
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    checkNewNotifications();
                }
            });

            // Check when window gets focus
            window.addEventListener('focus', checkNewNotifications);
        }

        function checkNewNotifications() {
            fetch(`../controller/notification-controller.php?action=get_latest_notifications&last_check=${encodeURIComponent(lastCheckTime)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notifications.length > 0) {
                        // Show toast for each new notification
                        data.notifications.forEach(notification => {
                            showNotificationToast(notification);
                        });

                        // Update unread count
                        unreadCount += data.notifications.length;
                        updateUnreadCount();

                        // Play sound
                        playNotificationSound();
                    }
                    lastCheckTime = data.last_check || lastCheckTime;
                })
                .catch(error => console.error('Error checking new notifications:', error));
        }

        function showNotificationToast(notification) {
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.onclick = () => viewNotification(notification.id, notification.related_id);

            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-icon">
                        <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                    </div>
                    <div class="toast-message">
                        <div class="toast-title">${escapeHtml(notification.title)}</div>
                        <div class="toast-text">${escapeHtml(notification.message)}</div>
                    </div>
                    <button class="toast-close" onclick="event.stopPropagation(); this.parentElement.parentElement.remove()">
                        &times;
                    </button>
                </div>
            `;

            document.getElementById('toastContainer').appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        function setupNotificationSound() {
            // Preload sound
            const sound = document.getElementById('notificationSound');
            if (sound) {
                sound.load();
            }
        }

        function playNotificationSound() {
            const sound = document.getElementById('notificationSound');
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(e => console.log('Audio play failed:', e));
            }
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = 'notification-toast';

            const icon = type === 'success' ? 'check-circle' :
                type === 'error' ? 'exclamation-triangle' : 'info-circle';
            const color = type === 'success' ? '#10B981' :
                type === 'error' ? '#EF4444' : '#3B82F6';

            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-icon" style="background: ${color}">
                        <i class="fas fa-${icon}"></i>
                    </div>
                    <div class="toast-message">
                        <div class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                        <div class="toast-text">${message}</div>
                    </div>
                    <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                        &times;
                    </button>
                </div>
            `;

            document.getElementById('toastContainer').appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }

        function showError(message) {
            showToast(message, 'error');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>