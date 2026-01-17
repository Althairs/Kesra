<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Proposal - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .dashboard-container {
            background: #f8fafc;
        }

        /* Modern Header */
        .content-header {
            background: white;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }

        /* Stats Grid Modern */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card-modern {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(229, 231, 235, 0.5);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-gradient);
        }

        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .stat-icon.total { background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: white; }
        .stat-icon.pending { background: linear-gradient(135deg, #F59E0B, #D97706); color: white; }
        .stat-icon.review { background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white; }
        .stat-icon.approved { background: linear-gradient(135deg, #10B981, #059669); color: white; }
        .stat-icon.rejected { background: linear-gradient(135deg, #EF4444, #DC2626); color: white; }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6B7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Filter Tabs Modern */
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            background: #F3F4F6;
            color: #6B7280;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-tab:hover {
            background: #E5E7EB;
            color: #374151;
        }

        .filter-tab.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .filter-tab .count {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Table Modern */
        .table-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .table-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
        }

        .table-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .table-header p {
            margin: 0.25rem 0 0 0;
            opacity: 0.9;
            font-size: 0.875rem;
        }

        .table-content {
            padding: 0;
        }

        .proposal-card {
            padding: 1.5rem;
            border-bottom: 1px solid #F3F4F6;
            transition: all 0.3s ease;
        }

        .proposal-card:last-child {
            border-bottom: none;
        }

        .proposal-card.expanded {
            background: #F0FDF9;
            border-left: 4px solid #10B981;
        }

        .proposal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .proposal-info h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.125rem;
            color: #1F2937;
            font-weight: 600;
        }

        .proposal-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6B7280;
            font-size: 0.875rem;
        }

        .meta-item i {
            width: 16px;
            color: #9CA3AF;
        }

        .proposal-actions {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .status-badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-review { background: #E0E7FF; color: #3730A3; }
        .status-approved { background: #D1FAE5; color: #065F46; }
        .status-rejected { background: #FEE2E2; color: #991B1B; }

        /* Expanded Content */
        .proposal-details {
            display: none;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E5E7EB;
        }

        .proposal-card.expanded .proposal-details {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-group h5 {
            margin: 0 0 0.75rem 0;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item {
            margin-bottom: 0.75rem;
        }

        .detail-item label {
            display: block;
            color: #6B7280;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item span {
            display: block;
            color: #1F2937;
            font-weight: 500;
        }

        .amount-highlight {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #059669 !important;
        }

        .description-box {
            background: #F8FAFC;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #3B82F6;
            margin: 1rem 0;
        }

        .admin-notes-box {
            background: #FFFBEB;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #F59E0B;
            margin: 1rem 0;
        }

        .admin-notes-box h6 {
            margin: 0 0 0.5rem 0;
            color: #92400E;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* Action Buttons */
        .action-buttons-modern {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E5E7EB;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-primary-modern {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-outline-modern {
            background: white;
            border: 2px solid #E5E7EB;
            color: #6B7280;
        }

        .btn-outline-modern:hover {
            border-color: #10B981;
            color: #10B981;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: white;
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        /* Status Form */
        .status-form-modern {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px dashed #E5E7EB;
            margin-top: 1.5rem;
        }

        .form-row-modern {
            display: grid;
            grid-template-columns: 1fr 2fr auto;
            gap: 1rem;
            align-items: end;
        }

        .form-group-modern {
            margin-bottom: 0;
        }

        .form-group-modern label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Expand Toggle */
        .expand-toggle {
            background: none;
            border: none;
            color: #6B7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .expand-toggle:hover {
            background: #F3F4F6;
            color: #374151;
        }

        /* No Click Zone */
        .no-click-zone {
            pointer-events: none;
        }

        .clickable {
            cursor: pointer;
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 4rem 2rem;
            color: #6B7280;
        }

        .empty-state-modern i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #D1D5DB;
        }

        .empty-state-modern h4 {
            margin: 0 0 0.5rem 0;
            color: #374151;
            font-size: 1.25rem;
        }

        .empty-state-modern p {
            margin: 0 0 1.5rem 0;
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-spinner {
            font-size: 3rem;
            color: #10B981;
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-tabs {
                flex-direction: column;
            }
            
            .proposal-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .proposal-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row-modern {
                grid-template-columns: 1fr;
            }
            
            .action-buttons-modern {
                flex-direction: column;
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
                    <h1>Pengajuan Proposal</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <div class="section-header">
                    <h2>Kelola Pengajuan Proposal</h2>
                    <p>Review dan kelola semua pengajuan proposal bantuan masjid</p>
                </div>

                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                    <i class="fas fa-spinner fa-spin loading-spinner"></i>
                    <p>Memuat data proposal...</p>
                </div>

                <!-- Global Alert Container -->
                <div id="globalAlert"></div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card-modern">
                        <div class="stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-number" id="totalProposals">0</div>
                        <div class="stat-label">Total Proposal</div>
                    </div>
                    <div class="stat-card-modern">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number" id="pendingProposals">0</div>
                        <div class="stat-label">Menunggu Review</div>
                    </div>
                    <div class="stat-card-modern">
                        <div class="stat-icon review">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="stat-number" id="reviewProposals">0</div>
                        <div class="stat-label">Dalam Review</div>
                    </div>
                    <div class="stat-card-modern">
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number" id="approvedProposals">0</div>
                        <div class="stat-label">Disetujui</div>
                    </div>
                    <div class="stat-card-modern">
                        <div class="stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-number" id="rejectedProposals">0</div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <i class="fas fa-layer-group"></i>
                        Semua Proposal
                        <span class="count" id="countAll">0</span>
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        <i class="fas fa-clock"></i>
                        Menunggu
                        <span class="count" id="countPending">0</span>
                    </button>
                    <button class="filter-tab" data-filter="review">
                        <i class="fas fa-search"></i>
                        Dalam Review
                        <span class="count" id="countReview">0</span>
                    </button>
                    <button class="filter-tab" data-filter="approved">
                        <i class="fas fa-check-circle"></i>
                        Disetujui
                        <span class="count" id="countApproved">0</span>
                    </button>
                    <button class="filter-tab" data-filter="rejected">
                        <i class="fas fa-times-circle"></i>
                        Ditolak
                        <span class="count" id="countRejected">0</span>
                    </button>
                </div>

                <!-- Proposal List -->
                <div class="table-modern">
                    <div class="table-header">
                        <h3>Daftar Pengajuan</h3>
                        <p>Klik pada header proposal untuk melihat detail dan mengelola status</p>
                    </div>
                    <div class="table-content" id="proposalsList">
                        <!-- Proposal cards will be loaded here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentProposals = [];
        let currentFilter = 'all';
        let expandedProposalId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadProposals('all');
            setupFilterButtons();
        });

        function setupFilterButtons() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    currentFilter = filter;
                    loadProposals(filter);
                });
            });
        }

        function loadProposals(filter = 'all') {
            showLoading();
            
            fetch(`../controller/admin-pengajuan.php?action=get_proposals&filter=${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        currentProposals = data.data;
                        renderProposalsList(currentProposals);
                        updateStats(currentProposals);
                        updateFilterCounts(currentProposals);
                    } else {
                        throw new Error(data.error || 'Failed to load proposals');
                    }
                })
                .catch(error => {
                    console.error('Error loading proposals:', error);
                    showAlert('Gagal memuat data proposal: ' + error.message, 'error');
                    renderEmptyState('Gagal memuat data proposal');
                })
                .finally(() => {
                    hideLoading();
                });
        }

        function renderProposalsList(proposals) {
            const container = document.getElementById('proposalsList');
            
            if (proposals.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-modern">
                        <i class="fas fa-inbox"></i>
                        <h4>Tidak Ada Data</h4>
                        <p>Tidak ada proposal dengan status "${getFilterText(currentFilter)}"</p>
                        <button class="btn-modern btn-primary-modern" onclick="loadProposals('all')">
                            <i class="fas fa-refresh"></i> Muat Ulang
                        </button>
                    </div>
                `;
                return;
            }

            container.innerHTML = proposals.map(proposal => `
                <div class="proposal-card ${expandedProposalId === proposal.id ? 'expanded' : ''}" 
                     id="proposal-${proposal.id}">
                    <div class="proposal-header" onclick="toggleProposalDetails(${proposal.id})">
                        <div class="proposal-info">
                            <h4>${proposal.nama_lembaga}</h4>
                            <div class="proposal-meta">
                                <div class="meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    <span>#${proposal.id}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>${proposal.user.nama_lengkap}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>${proposal.created_at_formatted}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>${proposal.jumlah_diajukan_formatted}</span>
                                </div>
                                ${proposal.dokumen ? `
                                <div class="meta-item">
                                    <i class="fas fa-file"></i>
                                    <span>Ada Dokumen</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="proposal-actions">
                            <span class="status-badge-modern status-${proposal.status}">
                                ${proposal.status_text}
                            </span>
                            <button class="expand-toggle" onclick="event.stopPropagation(); toggleProposalDetails(${proposal.id})">
                                <i class="fas fa-chevron-${expandedProposalId === proposal.id ? 'up' : 'down'}"></i>
                            </button>
                        </div>
                    </div>

                    <div class="proposal-details">
                        <div class="details-grid">
                            <div class="detail-group">
                                <h5>Informasi Lembaga</h5>
                                <div class="detail-item">
                                    <label>Nama Lembaga</label>
                                    <span>${proposal.nama_lembaga}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Jenis Bantuan</label>
                                    <span>${proposal.jenis_bantuan_text}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Jumlah Diajukan</label>
                                    <span class="amount-highlight">${proposal.jumlah_diajukan_formatted}</span>
                                </div>
                            </div>

                            <div class="detail-group">
                                <h5>Informasi Pengaju</h5>
                                <div class="detail-item">
                                    <label>Nama Lengkap</label>
                                    <span>${proposal.user.nama_lengkap}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Username</label>
                                    <span>${proposal.user.username}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Email</label>
                                    <span>${proposal.user.email}</span>
                                </div>
                                ${proposal.user.telepon ? `
                                <div class="detail-item">
                                    <label>Telepon</label>
                                    <span>${proposal.user.telepon}</span>
                                </div>
                                ` : ''}
                            </div>

                            <div class="detail-group">
                                <h5>Informasi Bank</h5>
                                <div class="detail-item">
                                    <label>Bank</label>
                                    <span>${proposal.bank}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Nomor Rekening</label>
                                    <span>${proposal.nomor_rekening}</span>
                                </div>
                            </div>

                            <div class="detail-group full-width">
                                <h5>Alamat Lembaga</h5>
                                <div class="description-box">
                                    ${proposal.alamat.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        </div>

                        <div class="detail-group full-width">
                            <h5>Deskripsi Proposal</h5>
                            <div class="description-box">
                                ${proposal.deskripsi.replace(/\n/g, '<br>')}
                            </div>
                        </div>

                        ${proposal.catatan_admin ? `
                        <div class="admin-notes-box">
                            <h6><i class="fas fa-sticky-note"></i> Catatan Admin</h6>
                            <p>${proposal.catatan_admin}</p>
                        </div>
                        ` : ''}

                        ${proposal.dokumen ? `
                        <div class="detail-item">
                            <label>Dokumen Pendukung</label>
                            <a href="../${proposal.dokumen}" target="_blank" class="btn-modern btn-outline-modern" onclick="event.stopPropagation()">
                                <i class="fas fa-download"></i> Download Dokumen
                            </a>
                        </div>
                        ` : ''}

                        <div class="status-form-modern" onclick="event.stopPropagation()">
                            <h5 style="margin-bottom: 1rem; color: #374151;">Update Status Proposal</h5>
                            <form id="statusForm-${proposal.id}" onsubmit="event.preventDefault(); updateProposalStatus(${proposal.id})">
                                <input type="hidden" name="proposal_id" value="${proposal.id}">
                                <div class="form-row-modern">
                                    <div class="form-group-modern">
                                        <label for="status-${proposal.id}">Status Baru</label>
                                        <select id="status-${proposal.id}" name="status" class="form-control-modern" required>
                                            <option value="">Pilih Status</option>
                                            <option value="pending" ${proposal.status === 'pending' ? 'selected' : ''}>Menunggu Review</option>
                                            <option value="review" ${proposal.status === 'review' ? 'selected' : ''}>Dalam Review</option>
                                            <option value="approved" ${proposal.status === 'approved' ? 'selected' : ''}>Disetujui</option>
                                            <option value="rejected" ${proposal.status === 'rejected' ? 'selected' : ''}>Ditolak</option>
                                        </select>
                                    </div>
                                    <div class="form-group-modern">
                                        <label for="catatan-${proposal.id}">Catatan Admin</label>
                                        <input type="text" id="catatan-${proposal.id}" name="catatan_admin" 
                                               class="form-control-modern" placeholder="Berikan catatan..." 
                                               value="${proposal.catatan_admin || ''}">
                                    </div>
                                    <div class="form-group-modern">
                                        <button type="submit" class="btn-modern btn-primary-modern">
                                            <i class="fas fa-save"></i> Update Status
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="action-buttons-modern" onclick="event.stopPropagation()">
                            <button class="btn-modern btn-success-modern" onclick="quickUpdateStatus(${proposal.id}, 'approved')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button class="btn-modern btn-warning-modern" onclick="quickUpdateStatus(${proposal.id}, 'review')">
                                <i class="fas fa-search"></i> Tandai Review
                            </button>
                            <button class="btn-modern btn-danger-modern" onclick="quickUpdateStatus(${proposal.id}, 'rejected')">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function toggleProposalDetails(proposalId) {
            if (expandedProposalId === proposalId) {
                expandedProposalId = null;
            } else {
                expandedProposalId = proposalId;
            }
            renderProposalsList(currentProposals);
        }

        function updateProposalStatus(proposalId) {
            const form = document.getElementById(`statusForm-${proposalId}`);
            const formData = new FormData(form);
            
            showLoading();
            
            fetch('../controller/admin-pengajuan.php?action=update_status', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('Status proposal berhasil diperbarui', 'success');
                    loadProposals(currentFilter);
                } else {
                    throw new Error(data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                showAlert('Gagal memperbarui status: ' + error.message, 'error');
            })
            .finally(() => {
                hideLoading();
            });
        }

        function quickUpdateStatus(proposalId, status) {
            const formData = new FormData();
            formData.append('proposal_id', proposalId);
            formData.append('status', status);
            formData.append('catatan_admin', `Status diubah menjadi ${getStatusText(status)} secara cepat`);
            
            showLoading();
            
            fetch('../controller/admin-pengajuan.php?action=update_status', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert(`Proposal berhasil ditandai sebagai ${getStatusText(status)}`, 'success');
                    loadProposals(currentFilter);
                } else {
                    throw new Error(data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                showAlert('Gagal memperbarui status: ' + error.message, 'error');
            })
            .finally(() => {
                hideLoading();
            });
        }

        function updateStats(proposals) {
            document.getElementById('totalProposals').textContent = proposals.length;
            document.getElementById('pendingProposals').textContent = proposals.filter(p => p.status === 'pending').length;
            document.getElementById('reviewProposals').textContent = proposals.filter(p => p.status === 'review').length;
            document.getElementById('approvedProposals').textContent = proposals.filter(p => p.status === 'approved').length;
            document.getElementById('rejectedProposals').textContent = proposals.filter(p => p.status === 'rejected').length;
        }

        function updateFilterCounts(proposals) {
            document.getElementById('countAll').textContent = proposals.length;
            document.getElementById('countPending').textContent = proposals.filter(p => p.status === 'pending').length;
            document.getElementById('countReview').textContent = proposals.filter(p => p.status === 'review').length;
            document.getElementById('countApproved').textContent = proposals.filter(p => p.status === 'approved').length;
            document.getElementById('countRejected').textContent = proposals.filter(p => p.status === 'rejected').length;
        }

        function getFilterText(filter) {
            const filterMap = {
                'all': 'Semua',
                'pending': 'Menunggu Review',
                'review': 'Dalam Review',
                'approved': 'Disetujui',
                'rejected': 'Ditolak'
            };
            return filterMap[filter] || filter;
        }

        function getStatusText(status) {
            const statusMap = {
                'pending': 'Menunggu Review',
                'review': 'Dalam Review',
                'approved': 'Disetujui',
                'rejected': 'Ditolak'
            };
            return statusMap[status] || status;
        }

        function renderEmptyState(message) {
            const container = document.getElementById('proposalsList');
            container.innerHTML = `
                <div class="empty-state-modern">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4>Terjadi Kesalahan</h4>
                    <p>${message}</p>
                    <button class="btn-modern btn-primary-modern" onclick="loadProposals(currentFilter)">
                        <i class="fas fa-refresh"></i> Coba Lagi
                    </button>
                </div>
            `;
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showAlert(message, type = 'info') {
            const existingAlerts = document.querySelectorAll('.global-alert');
            existingAlerts.forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `global-alert alert alert-${type}`;
            alertDiv.innerHTML = `
                <div class="alert-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
                    <span>${message}</span>
                    <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                </div>
            `;
            
            const globalAlert = document.getElementById('globalAlert');
            globalAlert.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Add global alert styles
        const style = document.createElement('style');
        style.textContent = `
            .global-alert {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                min-width: 300px;
                max-width: 500px;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                animation: slideIn 0.3s ease;
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>