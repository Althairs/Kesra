// Dashboard Admin JavaScript - Data Dinamis
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Admin Script Loaded');

    // Elemen DOM
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Toggle Sidebar di Mobile
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            console.log('Sidebar toggled');
        });
    }

    // Toggle Sidebar Collapse
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Tutup sidebar ketika klik di luar pada mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar && 
            !sidebar.contains(e.target) && 
            mobileToggle && 
            !mobileToggle.contains(e.target) && 
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    // Handle perubahan ukuran window
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('active');
        }
    });

    // Inisialisasi dashboard
    function initDashboard() {
        console.log('Dashboard Admin diinisialisasi - Data dinamis');
        
        // Load data statistik dan aktivitas
        loadInitialData();
        
        // Setup auto refresh
        setupAutoRefresh();
    }

    // Load data awal
    function loadInitialData() {
        // Data statistik sudah di-load oleh PHP, tapi bisa refresh jika perlu
        console.log('Initial data loaded from server');
    }

    // Setup auto refresh
    function setupAutoRefresh() {
        // Auto refresh stats setiap 5 menit
        setInterval(() => {
            refreshStats();
        }, 300000);
    }

    // Fungsi untuk refresh statistik
    function refreshStats() {
        fetch('../controller/admin-dashboard-controller.php?action=get_stats')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateStatsDisplay(data.data);
                    console.log('Stats auto-refreshed');
                }
            })
            .catch(error => {
                console.error('Error auto-refreshing stats:', error);
            });
    }

    // Update tampilan statistik
    function updateStatsDisplay(stats) {
        document.getElementById('totalProposals').textContent = stats.total_proposals;
        document.getElementById('pendingProposals').textContent = stats.pending_proposals;
        document.getElementById('totalAdmins').textContent = stats.total_admins;
        document.getElementById('totalNews').textContent = stats.total_news;
    }

    // Mulai dashboard
    initDashboard();
});

// Fungsi global untuk menampilkan alert
function showAlert(message, type = 'info') {
    // Hapus alert existing
    const existingAlert = document.querySelector('.global-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `global-alert alert alert-${type}`;
    alertDiv.innerHTML = `
        <div class="alert-content">
            <i class="fas fa-${getAlertIcon(type)}"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

// Fungsi helper untuk icon alert
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Tambahkan style untuk alert jika belum ada
if (!document.querySelector('#alert-styles')) {
    const alertStyle = document.createElement('style');
    alertStyle.id = 'alert-styles';
    alertStyle.textContent = `
        .global-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            max-width: 500px;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #7f1d1d;
        }
        .alert-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        .alert-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
        }
        .alert-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
        }
    `;
    document.head.appendChild(alertStyle);
}

console.log('Dashboard admin JavaScript loaded successfully');