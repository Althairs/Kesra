// Dashboard User JavaScript - Versi Multi-halaman
document.addEventListener('DOMContentLoaded', function() {
    // Elemen DOM
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Toggle Sidebar di Mobile
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Toggle Sidebar Collapse (untuk sidebar yang bisa disempitkan)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Filter Status Proposal (jika ada di halaman)
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Hapus class active dari semua tombol filter
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Tambah class active ke tombol yang diklik
            this.classList.add('active');
            
            // Di sini Anda akan memfilter daftar proposal
            const filter = this.getAttribute('data-filter');
            console.log('Filter by:', filter);
            
            // Tampilkan/sembunyikan item proposal berdasarkan filter
            filterProposals(filter);
        });
    });

    // Submit Form Proposal
    const proposalForm = document.querySelector('.proposal-form');
    if (proposalForm) {
        proposalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi form dasar
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error-color)';
                } else {
                    field.style.borderColor = 'var(--border-color)';
                }
            });
            
            // Validasi ukuran file jika file dipilih
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files[0]) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    isValid = false;
                    alert('Ukuran file maksimal 5MB');
                    fileInput.style.borderColor = 'var(--error-color)';
                }
            }
            
            if (!isValid) {
                alert('Harap isi semua field yang wajib diisi dan pastikan file tidak lebih dari 5MB!');
                return;
            }
            
            // Simulasi pengiriman form
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Mengajukan...';
            submitBtn.disabled = true;
            
            // Simulasi panggilan API
            setTimeout(() => {
                alert('Proposal berhasil diajukan! Kami akan memprosesnya secepatnya.');
                this.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Redirect ke halaman status
                window.location.href = 'status-proposal.php';
            }, 2000);
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

    // Inisialisasi data contoh untuk demonstrasi
    function initSampleData() {
        // Data proposal contoh 
        const sampleProposals = [
            {
                id: 1,
                namaMasjid: 'Masjid Al-Ikhlas',
                jenisBantuan: 'Renovasi Bangunan',
                jumlahDiajukan: 10000000,
                tanggal: '2024-01-15',
                status: 'pending'
            },
            {
                id: 2,
                namaMasjid: 'Masjid Nurul Huda',
                jenisBantuan: 'Sarana Ibadah',
                jumlahDiajukan: 5000000,
                tanggal: '2024-01-10',
                status: 'review'
            }
        ];

        // Simpan di localStorage untuk keperluan demo
        if (!localStorage.getItem('userProposals')) {
            localStorage.setItem('userProposals', JSON.stringify(sampleProposals));
        }
    }

    // Fungsi untuk memfilter proposal
    function filterProposals(filter) {
        const proposalList = document.querySelector('.proposal-list');
        if (!proposalList) return;

        const proposals = JSON.parse(localStorage.getItem('userProposals') || '[]');
        
        if (proposals.length === 0) {
            // Tampilkan state kosong
            proposalList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Proposal</h3>
                    <p>Anda belum mengajukan proposal apapun. <a href="ajukan-proposal.php">Ajukan proposal pertama Anda</a></p>
                </div>
            `;
            return;
        }

        // Filter proposal berdasarkan filter yang dipilih
        const filteredProposals = filter === 'all' 
            ? proposals 
            : proposals.filter(p => p.status === filter);

        if (filteredProposals.length === 0) {
            proposalList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Tidak Ada Proposal</h3>
                    <p>Tidak ada proposal dengan status "${getStatusText(filter)}"</p>
                </div>
            `;
            return;
        }

        // Generate item proposal
        proposalList.innerHTML = filteredProposals.map(proposal => `
            <div class="proposal-item">
                <div class="proposal-header">
                    <h3>${proposal.namaMasjid}</h3>
                    <span class="status-badge status-${proposal.status}">
                        ${getStatusText(proposal.status)}
                    </span>
                </div>
                <div class="proposal-details">
                    <p><strong>Jenis:</strong> ${proposal.jenisBantuan}</p>
                    <p><strong>Diajukan:</strong> Rp ${proposal.jumlahDiajukan.toLocaleString()}</p>
                    <p><strong>Tanggal:</strong> ${formatDate(proposal.tanggal)}</p>
                </div>
                <div class="proposal-actions">
                    <button class="btn btn-outline btn-sm" onclick="viewProposal(${proposal.id})">
                        Lihat Detail
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Fungsi helper untuk mendapatkan teks status
    function getStatusText(status) {
        const statusMap = {
            'pending': 'Menunggu',
            'review': 'Dalam Review',
            'approved': 'Disetujui',
            'rejected': 'Ditolak'
        };
        return statusMap[status] || status;
    }

    // Fungsi helper untuk memformat tanggal
    function formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    // Inisialisasi dashboard
    function initDashboard() {
        console.log('Dashboard User diinisialisasi - Versi multi-halaman');
        initSampleData();
        
        // Jika berada di halaman status, muat proposal
        if (window.location.pathname.includes('status-proposal.php')) {
            filterProposals('all');
        }
    }

    // Mulai dashboard
    initDashboard();
});

// Fungsi global untuk halaman profil
function editProfile() {
    alert('Fitur edit profil akan segera tersedia!');
}

function changePassword() {
    alert('Fitur ubah password akan segera tersedia!');
}

// Fungsi global untuk melihat detail proposal
function viewProposal(proposalId) {
    alert(`Detail proposal #${proposalId} akan ditampilkan di sini.`);
}