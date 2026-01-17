<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil data proposal
$proposal = null;
if (isset($_GET['id'])) {
    $id_proposal = $_GET['id'];
    $id_user = $_SESSION['user_id'];
    
    // Gunakan prepared statement untuk keamanan
    $query = "SELECT * FROM proposals WHERE id = ? AND id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $id_proposal, $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $proposal = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error_messages'] = ["Proposal tidak ditemukan!"];
        header('Location: status-proposal.php');
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error_messages'] = ["ID Proposal tidak valid!"];
    header('Location: status-proposal.php');
    exit();
}

// Fungsi untuk mendapatkan teks status
function getStatusText($status) {
    $statusMap = [
        'pending' => 'Menunggu Review',
        'review' => 'Dalam Review',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak'
    ];
    return $statusMap[$status] ?? $status;
}

// Fungsi untuk mendapatkan class CSS status
function getStatusClass($status) {
    $statusClassMap = [
        'pending' => 'status-pending',
        'review' => 'status-review',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected'
    ];
    return $statusClassMap[$status] ?? 'status-pending';
}

// Fungsi untuk mendapatkan teks jenis bantuan
function getJenisBantuanText($jenis) {
    $jenisMap = [
        'renovasi' => 'Renovasi Bangunan',
        'sarana' => 'Sarana Ibadah',
        'pendidikan' => 'Pendidikan Agama',
        'lainnya' => 'Lainnya'
    ];
    return $jenisMap[$jenis] ?? $jenis;
}

// Fungsi untuk mendapatkan icon bank
function getBankIcon($bank) {
    $bankIcons = [
        'BRI' => 'fas fa-university',
        'BNI' => 'fas fa-landmark',
        'BSG' => 'fas fa-piggy-bank',
        'Mandiri' => 'fas fa-building',
        'BCA' => 'fas fa-chart-line'
    ];
    return $bankIcons[$bank] ?? 'fas fa-credit-card';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Proposal - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .alert {
            position: relative;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .alert-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
        }
        .proposal-detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .detail-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 2rem;
            position: relative;
        }
        .detail-header h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .status-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.9);
            color: white;
        }
        .status-review {
            background: rgba(59, 130, 246, 0.9);
            color: white;
        }
        .status-approved {
            background: rgba(16, 185, 129, 0.9);
            color: white;
        }
        .status-rejected {
            background: rgba(239, 68, 68, 0.9);
            color: white;
        }
        .detail-grid {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .detail-item label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-item span {
            color: #1f2937;
            font-size: 1rem;
        }
        .detail-item.full-width {
            grid-column: 1 / -1;
        }
        .amount {
            font-size: 1.25rem !important;
            font-weight: 700;
            color: #059669 !important;
        }
        .admin-notes {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            color: #92400e;
            font-style: italic;
        }
        .proposal-description {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            line-height: 1.6;
            white-space: pre-line;
        }
        .detail-actions {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
        }
        .file-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .timeline {
            margin-top: 1rem;
            padding-left: 1rem;
            border-left: 2px solid #e5e7eb;
        }
        .timeline-item {
            margin-bottom: 1rem;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
        }
        .timeline-date {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .timeline-content {
            color: #1f2937;
        }
        .bank-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .bank-icon {
            width: 40px;
            height: 40px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }
        .bank-details {
            flex: 1;
        }
        .bank-name {
            font-weight: 600;
            color: #1f2937;
        }
        .account-number {
            color: #6b7280;
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .detail-header {
                padding: 1.5rem;
            }
            .detail-grid {
                padding: 1.5rem;
                grid-template-columns: 1fr;
            }
            .detail-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }
            .status-badge {
                position: static;
                display: inline-block;
                margin-top: 0.5rem;
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
                    <h1>Detail Proposal</h1>
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
                <!-- Tampilkan pesan error jika ada -->
                <?php if (isset($_SESSION['error_messages'])): ?>
                    <div class="alert alert-error">
                        <div class="alert-content">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div style="flex: 1;">
                                <strong>Terjadi kesalahan:</strong>
                                <ul>
                                    <?php foreach ($_SESSION['error_messages'] as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_messages']); ?>
                <?php endif; ?>

                <!-- Tampilkan pesan sukses jika ada -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <div class="alert-content">
                            <i class="fas fa-check-circle"></i>
                            <span style="flex: 1;"><?php echo htmlspecialchars($_SESSION['success_message']); ?></span>
                            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                        </div>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <div class="section-header">
                    <h2>Detail Proposal Bantuan</h2>
                    <p>Informasi lengkap proposal yang diajukan</p>
                </div>

                <div class="proposal-detail-card">
                    <div class="detail-header">
                        <h3><?php echo htmlspecialchars($proposal['nama_lembaga']); ?></h3>
                        <p>ID Proposal: #<?php echo $proposal['id']; ?></p>
                        <span class="status-badge <?php echo getStatusClass($proposal['status']); ?>">
                            <?php echo getStatusText($proposal['status']); ?>
                        </span>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Tanggal Pengajuan</label>
                            <span><?php echo date('d F Y H:i', strtotime($proposal['created_at'])); ?></span>
                        </div>

                        <div class="detail-item">
                            <label>Terakhir Diupdate</label>
                            <span>
                                <?php 
                                if ($proposal['updated_at'] && $proposal['updated_at'] != '0000-00-00 00:00:00') {
                                    echo date('d F Y H:i', strtotime($proposal['updated_at']));
                                } else {
                                    echo 'Belum pernah diupdate';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="detail-item full-width">
                            <label>Alamat Lembaga</label>
                            <span><?php echo nl2br(htmlspecialchars($proposal['alamat'])); ?></span>
                        </div>

                        <div class="detail-item">
                            <label>Jenis Bantuan</label>
                            <span><?php echo getJenisBantuanText($proposal['jenis_bantuan']); ?></span>
                        </div>

                        <div class="detail-item">
                            <label>Jumlah Diajukan</label>
                            <span class="amount">Rp <?php echo number_format($proposal['jumlah_diajukan'], 0, ',', '.'); ?></span>
                        </div>

                        <div class="detail-item">
                            <label>Informasi Bank</label>
                            <div class="bank-info">
                                <div class="bank-icon">
                                    <i class="<?php echo getBankIcon($proposal['bank']); ?>"></i>
                                </div>
                                <div class="bank-details">
                                    <div class="bank-name"><?php echo htmlspecialchars($proposal['bank']); ?></div>
                                    <div class="account-number"><?php echo htmlspecialchars($proposal['nomor_rekening']); ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($proposal['dokumen'])): ?>
                        <div class="detail-item">
                            <label>Dokumen Pendukung</label>
                            <div>
                                <a href="../<?php echo $proposal['dokumen']; ?>" target="_blank" class="btn btn-outline btn-sm">
                                    <i class="fas fa-download"></i> Download Dokumen
                                </a>
                                <div class="file-info">
                                    <i class="fas fa-info-circle"></i>
                                    <span>File: <?php echo basename($proposal['dokumen']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($proposal['catatan_admin'])): ?>
                        <div class="detail-item full-width">
                            <label>Catatan Admin</label>
                            <div class="admin-notes">
                                <i class="fas fa-sticky-note"></i>
                                <?php echo nl2br(htmlspecialchars($proposal['catatan_admin'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="detail-item full-width">
                            <label>Deskripsi Proposal</label>
                            <div class="proposal-description">
                                <?php echo (htmlspecialchars($proposal['deskripsi'])); ?>
                            </div>
                        </div>

                        <!-- Timeline Status -->
                        <div class="detail-item full-width">
                            <label>Timeline Status</label>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <?php echo date('d F Y H:i', strtotime($proposal['created_at'])); ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>Proposal Diajukan</strong>
                                        <p>Proposal berhasil diajukan dan menunggu review</p>
                                    </div>
                                </div>
                                
                                <?php if ($proposal['status'] === 'review'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <?php 
                                        $review_date = $proposal['updated_at'] && $proposal['updated_at'] != '0000-00-00 00:00:00' 
                                            ? $proposal['updated_at'] 
                                            : date('Y-m-d H:i:s');
                                        echo date('d F Y H:i', strtotime($review_date)); 
                                        ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>Dalam Review</strong>
                                        <p>Proposal sedang dalam proses review oleh admin</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($proposal['status'] === 'approved'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <?php 
                                        $approved_date = $proposal['updated_at'] && $proposal['updated_at'] != '0000-00-00 00:00:00' 
                                            ? $proposal['updated_at'] 
                                            : date('Y-m-d H:i:s');
                                        echo date('d F Y H:i', strtotime($approved_date)); 
                                        ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>Disetujui</strong>
                                        <p>Proposal telah disetujui oleh admin</p>
                                        <?php if (!empty($proposal['catatan_admin'])): ?>
                                            <p><em>Catatan: <?php echo htmlspecialchars($proposal['catatan_admin']); ?></em></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($proposal['status'] === 'rejected'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <?php 
                                        $rejected_date = $proposal['updated_at'] && $proposal['updated_at'] != '0000-00-00 00:00:00' 
                                            ? $proposal['updated_at'] 
                                            : date('Y-m-d H:i:s');
                                        echo date('d F Y H:i', strtotime($rejected_date)); 
                                        ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>Ditolak</strong>
                                        <p>Proposal tidak disetujui oleh admin</p>
                                        <?php if (!empty($proposal['catatan_admin'])): ?>
                                            <p><em>Alasan: <?php echo htmlspecialchars($proposal['catatan_admin']); ?></em></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="detail-actions">
                        <div>
                            <a href="status-proposal.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <?php if ($proposal['status'] == 'pending'): ?>
                            <a href="edit-proposal.php?id=<?php echo $proposal['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Proposal
                            </a>
                            <?php endif; ?>
                            
                            <!-- <?php if (!empty($proposal['dokumen'])): ?>
                            <a href="../<?php echo $proposal['dokumen']; ?>" target="_blank" class="btn btn-outline">
                                <i class="fas fa-print"></i> download Dokumen
                            </a>
                            <?php endif; ?> -->
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="section-header">
                    <h3>Informasi Penting</h3>
                </div>

                <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <i class="fas fa-clock" style="color: #f59e0b; font-size: 1.25rem;"></i>
                            <div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #1f2937;">Proses Review</h4>
                                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                                    Proses review membutuhkan waktu 3-7 hari kerja
                                </p>
                            </div>
                        </div>
                        <!-- <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <i class="fas fa-envelope" style="color: #10b981; font-size: 1.25rem;"></i>
                            <div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #1f2937;">Notifikasi</h4>
                                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                                    Anda akan mendapat notifikasi ketika status berubah
                                </p>
                            </div>
                        </div> -->
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <i class="fas fa-question-circle" style="color: #3b82f6; font-size: 1.25rem;"></i>
                            <div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #1f2937;">Butuh Bantuan?</h4>
                                <p style="margin: 0; color: #6b7280; font-size: 0.875rem;">
                                    Hubungi admin melalui menu bantuan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
    <script>
        // Fungsi untuk print halaman
        function printPage() {
            window.print();
        }

        // Fungsi untuk share informasi proposal
        function shareProposal() {
            if (navigator.share) {
                navigator.share({
                    title: 'Detail Proposal - <?php echo htmlspecialchars($proposal['nama_lembaga']); ?>',
                    text: 'Lihat detail proposal bantuan yang saya ajukan',
                    url: window.location.href
                })
                .then(() => console.log('Berhasil dibagikan'))
                .catch((error) => console.log('Error sharing:', error));
            } else {
                // Fallback untuk browser yang tidak support Web Share API
                alert('Fitur share tidak didukung di browser ini. Anda bisa copy link manual.');
            }
        }

        // Tambahkan event listener untuk tombol cetak
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('[onclick="printPage()"]');
            if (printBtn) {
                printBtn.addEventListener('click', printPage);
            }

            // Tambahkan animasi untuk timeline
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                item.style.transition = `all 0.5s ease ${index * 0.2}s`;
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 100);
            });

            console.log('Detail proposal page loaded successfully');
        });

        // Fungsi untuk download dokumen dengan konfirmasi
        function downloadDocument(url) {
            if (confirm('Apakah Anda ingin mendownload dokumen ini?')) {
                window.open(url, '_blank');
            }
        }
    </script>
</body>
</html>