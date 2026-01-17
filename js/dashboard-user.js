// Dashboard User JavaScript - Simple Working Version

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard User Script Loaded');

    // Elemen DOM
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileOverlay = document.getElementById('mobileOverlay');

    // Toggle Sidebar di Mobile - SIMPLE VERSION
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            console.log('Mobile toggle clicked');
            sidebar.classList.toggle('active');
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('active');
            }
        });
    }

    // Tutup sidebar ketika klik overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            console.log('Overlay clicked');
            sidebar.classList.remove('active');
            mobileOverlay.classList.remove('active');
        });
    }

    // Tutup sidebar ketika klik di luar pada mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && 
            sidebar && 
            sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) && 
            mobileToggle && 
            !mobileToggle.contains(e.target)) {
            console.log('Clicked outside, closing sidebar');
            sidebar.classList.remove('active');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
        }
    });

    // Handle perubahan ukuran window
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('active');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
            }
        }
    });

    // Filter Status Proposal (jika ada di halaman)
    const filterBtns = document.querySelectorAll('.filter-btn');
    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Hapus class active dari semua tombol filter
                filterBtns.forEach(b => b.classList.remove('active'));
                
                // Tambah class active ke tombol yang diklik
                this.classList.add('active');
                
                // Filter daftar proposal
                const filter = this.getAttribute('data-filter');
                console.log('Filter by:', filter);
                
                // Tampilkan/sembunyikan item proposal berdasarkan filter
                filterProposals(filter);
            });
        });
    }

    // Inisialisasi dashboard
    function initDashboard() {
        console.log('Dashboard User diinisialisasi');
        
        // Tampilkan pesan sukses/error jika ada
        showFlashMessages();
    }

    // Fungsi untuk menampilkan pesan flash
    function showFlashMessages() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            showAlert('Proposal berhasil diajukan!', 'success');
        }
        if (urlParams.has('error')) {
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
        }
    }

    // Mulai dashboard
    initDashboard();
});

// Fungsi untuk memfilter proposal (Client-side)
function filterProposals(filter) {
    const proposalItems = document.querySelectorAll('.proposal-item');
    let visibleCount = 0;

    proposalItems.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        
        if (filter === 'all' || itemStatus === filter) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Tampilkan pesan jika tidak ada data
    const proposalList = document.querySelector('.proposal-list');
    const existingMessage = document.querySelector('.no-data-message');
    
    if (existingMessage) {
        existingMessage.remove();
    }

    if (visibleCount === 0 && proposalItems.length > 0) {
        const filterNames = {
            'all': 'Semua',
            'pending': 'Menunggu',
            'review': 'Dalam Review',
            'approved': 'Disetujui',
            'rejected': 'Ditolak'
        };
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'no-data-message';
        messageDiv.innerHTML = `
            <i class="fas fa-search"></i>
            <h3>Tidak Ada Proposal</h3>
            <p>Tidak ada proposal dengan status "${filterNames[filter]}"</p>
        `;
        
        if (proposalList) {
            proposalList.appendChild(messageDiv);
        }
    }
}

// Fungsi global untuk melihat detail proposal
function viewProposal(proposalId) {
    window.location.href = `detail-proposal.php?id=${proposalId}`;
}

// Fungsi untuk edit proposal
function editProposal(proposalId) {
    window.location.href = `edit-proposal.php?id=${proposalId}`;
}

// Fungsi untuk menghapus proposal
function deleteProposal(proposalId) {
    if (confirm('Apakah Anda yakin ingin menghapus proposal ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Tampilkan loading
        const proposalItem = document.querySelector(`[onclick="deleteProposal(${proposalId})"]`).closest('.proposal-item');
        const originalContent = proposalItem.innerHTML;
        proposalItem.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Menghapus proposal...</p>
            </div>
        `;

        fetch(`../controller/pengajuan-controller.php?action=delete&id=${proposalId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('Proposal berhasil dihapus!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.error || 'Delete failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Kembalikan konten asli
                proposalItem.innerHTML = originalContent;
                showAlert('Gagal menghapus proposal: ' + error.message, 'error');
            });
    }
}

// Fungsi untuk menampilkan alert
function showAlert(message, type = 'info') {
    // Hapus alert existing
    const existingAlert = document.querySelector('.global-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `global-alert alert-${type}`;
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
            color: inherit;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
            border: 1px solid #ef4444;
        }
        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
        }
        .loading-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }
        .loading-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .no-data-message {
            text-align: center;
            padding: 2rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #e5e7eb;
            color: #6b7280;
            margin: 1rem 0;
        }
        .no-data-message i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }
    `;
    document.head.appendChild(alertStyle);
}

console.log('Dashboard user JavaScript loaded successfully');
