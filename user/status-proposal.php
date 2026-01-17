<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil data proposal dari database
$user_id = $_SESSION['user_id'];
$proposals = [];

try {
    $query = "SELECT * FROM proposals WHERE id_user = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $proposals[] = $row;
    }
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    error_log("Error fetching proposals: " . $e->getMessage());
}

// Hitung statistik
$total_proposals = count($proposals);
$pending_count = 0;
$review_count = 0;
$approved_count = 0;
$rejected_count = 0;

foreach ($proposals as $proposal) {
    switch ($proposal['status']) {
        case 'pending': $pending_count++; break;
        case 'review': $review_count++; break;
        case 'approved': $approved_count++; break;
        case 'rejected': $rejected_count++; break;
    }
}

$selesai_count = $approved_count + $rejected_count;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Proposal - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #7f1d1d;
        }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #10B981;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #6B7280;
            font-size: 0.875rem;
        }
        .proposal-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6B7280;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #D1D5DB;
        }
        .proposal-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #10B981;
            transition: transform 0.2s ease;
        }
        .proposal-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #F59E0B;
        }
        .status-review {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
        }
        .status-approved {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        .status-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }
        .floating-alert {
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .floating-alert.success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .floating-alert.error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #7f1d1d;
        }
        .floating-alert.warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        .floating-alert.info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
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
        .loading-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }
        .loading-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
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
        .status-filter {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .filter-btn:hover {
            background: #f3f4f6;
        }
        .filter-btn.active {
            background: #10b981;
            color: white;
            border-color: #10b981;
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
                    <h1>Status Proposal</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">User</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <div class="section-header">
                    <h2>Status Proposal Anda</h2>
                    <p>Pantau status pengajuan proposal bantuan Anda</p>
                </div>

                <!-- Pesan Sukses dari Pengajuan -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Pesan Error -->
                <?php if (isset($_SESSION['error_messages'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php 
                        if (is_array($_SESSION['error_messages'])) {
                            foreach ($_SESSION['error_messages'] as $error) {
                                echo $error . "<br>";
                            }
                        } else {
                            echo $_SESSION['error_messages'];
                        }
                        unset($_SESSION['error_messages']); 
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Statistik Proposal -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_proposals; ?></div>
                        <div class="stat-label">Total Proposal</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $pending_count; ?></div>
                        <div class="stat-label">Menunggu</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $review_count; ?></div>
                        <div class="stat-label">Dalam Review</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $selesai_count; ?></div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="status-filter">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="pending">Menunggu</button>
                    <button class="filter-btn" data-filter="review">Dalam Review</button>
                    <button class="filter-btn" data-filter="approved">Disetujui</button>
                    <button class="filter-btn" data-filter="rejected">Ditolak</button>
                    <button class="filter-btn" data-filter="selesai">Selesai</button>
                </div>

                <!-- Daftar Proposal -->
                <div class="proposal-list">
                    <?php if (empty($proposals)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Belum Ada Proposal</h3>
                            <p>Anda belum mengajukan proposal apapun. <a href="ajukan-proposal.php">Ajukan proposal pertama Anda</a></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($proposals as $proposal): 
                            // Tentukan kategori untuk filter
                            $status_kategori = $proposal['status'];
                            if ($proposal['status'] === 'approved' || $proposal['status'] === 'rejected') {
                                $status_kategori = 'selesai';
                            }
                        ?>
                            <div class="proposal-item" data-status="<?php echo $status_kategori; ?>" data-original-status="<?php echo $proposal['status']; ?>">
                                <div class="proposal-header">
                                    <h3><?php echo htmlspecialchars($proposal['nama_lembaga']); ?></h3>
                                    <span class="status-badge status-<?php echo $proposal['status']; ?>">
                                        <?php
                                        $status_text = [
                                            'pending' => 'Menunggu',
                                            'review' => 'Dalam Review',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak'
                                        ];
                                        echo $status_text[$proposal['status']] ?? $proposal['status'];
                                        ?>
                                    </span>
                                </div>
                                <div class="proposal-details">
                                    <p><strong>ID Proposal:</strong> #<?php echo $proposal['id']; ?></p>
                                    <p><strong>Jenis Bantuan:</strong> 
                                        <?php
                                        $jenis_text = [
                                            'renovasi' => 'Renovasi Bangunan',
                                            'sarana' => 'Sarana Ibadah',
                                            'pendidikan' => 'Pendidikan Agama',
                                            'lainnya' => 'Lainnya'
                                        ];
                                        echo $jenis_text[$proposal['jenis_bantuan']] ?? $proposal['jenis_bantuan'];
                                        ?>
                                    </p>
                                    <p><strong>Jumlah Diajukan:</strong> Rp <?php echo number_format($proposal['jumlah_diajukan'], 0, ',', '.'); ?></p>
                                    <p><strong>Tanggal Pengajuan:</strong> <?php echo date('d F Y H:i', strtotime($proposal['created_at'])); ?></p>
                                    <?php if (!empty($proposal['catatan_admin'])): ?>
                                        <p><strong>Catatan Admin:</strong> <?php echo htmlspecialchars($proposal['catatan_admin']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="proposal-actions">
                                    <a href="detail-proposal.php?id=<?php echo $proposal['id']; ?>" class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </a>
                                    <?php if ($proposal['status'] === 'pending'): ?>
                                        <a href="edit-proposal.php?id=<?php echo $proposal['id']; ?>" class="btn btn-outline btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-outline btn-sm btn-danger" onclick="deleteProposal(<?php echo $proposal['id']; ?>)">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    <?php endif; ?>
                                    <?php if (!empty($proposal['dokumen'])): ?>
                                        <a href="../<?php echo $proposal['dokumen']; ?>" target="_blank" class="btn btn-outline btn-sm">
                                            <i class="fas fa-download"></i> Dokumen
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
 <script src="../js/dashboard-user.js"></script>
    <script>
        // Filter proposal berdasarkan status - Client-side filtering
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Status proposal page loaded');
            
            const filterBtns = document.querySelectorAll('.filter-btn');
            const proposalItems = document.querySelectorAll('.proposal-item');
            const proposalList = document.querySelector('.proposal-list');
            
            // Function untuk update tampilan berdasarkan filter
            function updateFilterDisplay(filter) {
                let visibleCount = 0;
                
                proposalItems.forEach(item => {
                    const itemStatus = item.getAttribute('data-status');
                    const originalStatus = item.getAttribute('data-original-status');
                    
                    let shouldShow = false;
                    
                    if (filter === 'all') {
                        shouldShow = true;
                    } else if (filter === 'selesai') {
                        shouldShow = originalStatus === 'approved' || originalStatus === 'rejected';
                    } else {
                        shouldShow = itemStatus === filter || originalStatus === filter;
                    }
                    
                    if (shouldShow) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Tampilkan pesan jika tidak ada data untuk filter tertentu
                showNoDataMessage(filter, visibleCount);
            }
            
            // Function untuk menampilkan pesan tidak ada data
            function showNoDataMessage(filter, visibleCount) {
                // Hapus pesan sebelumnya
                const existingMessage = document.querySelector('.no-data-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Jika tidak ada item yang visible dan ada proposal, tampilkan pesan
                if (visibleCount === 0 && proposalItems.length > 0) {
                    const filterNames = {
                        'all': 'Semua',
                        'pending': 'Menunggu',
                        'review': 'Dalam Review',
                        'approved': 'Disetujui',
                        'rejected': 'Ditolak',
                        'selesai': 'Selesai'
                    };
                    
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'no-data-message';
                    messageDiv.innerHTML = `
                        <i class="fas fa-search"></i>
                        <h3>Tidak Ada Proposal</h3>
                        <p>Tidak ada proposal dengan status "${filterNames[filter]}"</p>
                        <button class="btn btn-outline btn-sm" onclick="resetFilter()">
                            <i class="fas fa-arrow-left"></i> Tampilkan Semua Proposal
                        </button>
                    `;
                    
                    proposalList.appendChild(messageDiv);
                }
            }
            
            // Event listener untuk tombol filter
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('Filter clicked:', this.getAttribute('data-filter'));
                    
                    // Remove active class from all buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    updateFilterDisplay(filter);
                });
            });
            
            // Inisialisasi tampilan awal
            updateFilterDisplay('all');
        });

        // Fungsi untuk reset filter ke semua
        function resetFilter() {
            const allBtn = document.querySelector('.filter-btn[data-filter="all"]');
            if (allBtn) {
                allBtn.click();
            }
        }

        // Fungsi untuk menghapus proposal
        function deleteProposal(proposalId) {
            console.log('Delete proposal:', proposalId);
            
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

                // Gunakan pengajuan-controller.php dengan action delete
                fetch(`../controller/pengajuan-controller.php?action=delete&id=${proposalId}`)
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delete response data:', data);
                        if (data.success) {
                            // Tampilkan pesan sukses dan refresh halaman
                            showAlert('Proposal berhasil dihapus!', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.error || 'Delete failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting proposal:', error);
                        // Kembalikan konten asli
                        proposalItem.innerHTML = originalContent;
                        showAlert('Gagal menghapus proposal: ' + error.message, 'error');
                    });
            }
        }

        // Fungsi untuk menampilkan alert
        function showAlert(message, type = 'info') {
            // Hapus alert existing
            const existingAlert = document.querySelector('.floating-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            const alertDiv = document.createElement('div');
            alertDiv.className = `floating-alert ${type}`;
            
            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-triangle',
                'warning': 'exclamation-circle',
                'info': 'info-circle'
            };
            
            alertDiv.innerHTML = `
                <i class="fas fa-${icons[type] || 'info-circle'}"></i>
                <span style="flex: 1;">${message}</span>
                <button style="background: none; border: none; font-size: 1.2rem; cursor: pointer;" 
                        onclick="this.parentElement.remove()">&times;</button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove setelah 5 detik
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Debug: Log initial state
        console.log('Status proposal script initialized');
        console.log('Proposal items found:', document.querySelectorAll('.proposal-item').length);
    </script>
</body>
</html>