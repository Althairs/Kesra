// Dashboard Admin JavaScript dengan SweetAlert
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

    // Toggle Sidebar Collapse
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // Filter Status Proposal
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            console.log('Filter by:', filter);
            filterProposals(filter);
        });
    });

    // Form Tambah Admin - Client-side validation only
    const adminForm = document.querySelector('.admin-form');
    if (adminForm) {
        adminForm.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            let errorMessage = '';
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error-color)';
                    errorMessage = 'Harap isi semua field yang wajib diisi!';
                } else {
                    field.style.borderColor = 'var(--border-color)';
                }
            });

            // Validasi konfirmasi password
            const password = this.querySelector('#password');
            const confirmPassword = this.querySelector('#confirm_password');
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                confirmPassword.style.borderColor = 'var(--error-color)';
                errorMessage = 'Konfirmasi password tidak sesuai!';
            }

            // Validasi panjang password
            if (password && password.value.length < 8) {
                isValid = false;
                password.style.borderColor = 'var(--error-color)';
                errorMessage = 'Password harus minimal 8 karakter!';
            }

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                    confirmButtonColor: 'var(--primary-color)'
                });
                return;
            }

            // Jika valid, form akan di-submit secara normal ke PHP controller
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Menambahkan...';
            submitBtn.disabled = true;

            // Biarkan form submit normal, PHP akan handle sisanya
        });
    }

    // Form Buat Berita - Client-side validation only
    const newsForm = document.querySelector('.news-form');
    if (newsForm) {
        newsForm.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            let errorMessage = '';
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error-color)';
                    errorMessage = 'Harap isi semua field yang wajib diisi!';
                } else {
                    field.style.borderColor = 'var(--border-color)';
                }
            });

            // Validasi file gambar
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files[0]) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                const fileType = fileInput.files[0].type;
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                
                if (fileSize > 2) {
                    isValid = false;
                    errorMessage = 'Ukuran file maksimal 2MB';
                    fileInput.style.borderColor = 'var(--error-color)';
                }
                
                if (!validTypes.includes(fileType)) {
                    isValid = false;
                    errorMessage = 'Format file harus JPG, PNG, atau GIF';
                    fileInput.style.borderColor = 'var(--error-color)';
                }
            }

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                    confirmButtonColor: 'var(--primary-color)'
                });
                return;
            }

            // Jika valid, form akan di-submit secara normal ke PHP controller
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Memposting...';
            submitBtn.disabled = true;
        });
    }

    // Preview gambar berita
    const gambarInput = document.getElementById('gambar');
    if (gambarInput) {
        gambarInput.addEventListener('change', function() {
            previewImage(this);
        });
    }

    // Validasi real-time untuk konfirmasi password
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            const password = document.getElementById('password');
            if (password && this.value !== password.value) {
                this.style.borderColor = 'var(--error-color)';
            } else {
                this.style.borderColor = 'var(--border-color)';
            }
        });
    }

    // Validasi real-time untuk strength password
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
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

    // Check for server messages and show SweetAlert
    checkServerMessages();

    // Inisialisasi dashboard admin
    function initDashboard() {
        console.log('Dashboard Admin diinisialisasi');
        
        // Muat data berdasarkan halaman - sekarang data diambil langsung dari PHP/database
        if (window.location.pathname.includes('lihat-pengajuan.php')) {
            updateStats();
        }
    }

    // Mulai dashboard
    initDashboard();
});

// Fungsi untuk memfilter proposal - Data sekarang dari database
function filterProposals(filter) {
    console.log('Filtering proposals with:', filter);
    // Filtering sekarang dilakukan di server side, fungsi ini hanya untuk UI
}

// Fungsi untuk update statistik - Data sekarang dari database
function updateStats() {
    // Statistik akan dihitung dari data database yang sebenarnya
    console.log('Updating stats from database...');
}

// Fungsi untuk preview gambar
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Di aplikasi nyata, di sini akan menampilkan preview gambar
            console.log('Image preview loaded:', e.target.result);
            
            // Create preview element if it doesn't exist
            let previewContainer = input.parentNode.querySelector('.image-preview');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'image-preview';
                previewContainer.style.marginTop = '10px';
                previewContainer.style.textAlign = 'center';
                input.parentNode.appendChild(previewContainer);
            }
            
            previewContainer.innerHTML = `
                <img src="${e.target.result}" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                <p style="font-size: 12px; color: var(--secondary-color); margin-top: 5px;">Preview Gambar</p>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Fungsi untuk mengecek kekuatan password
function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength');
    if (!strengthIndicator) return;

    let strength = 0;
    let feedback = '';

    // Length check
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;

    // Character variety checks
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    // Determine strength level and feedback
    if (password.length === 0) {
        feedback = '';
        strengthIndicator.className = 'password-strength';
    } else if (strength <= 2) {
        feedback = 'Lemah';
        strengthIndicator.className = 'password-strength strength-weak';
    } else if (strength <= 4) {
        feedback = 'Sedang';
        strengthIndicator.className = 'password-strength strength-medium';
    } else {
        feedback = 'Kuat';
        strengthIndicator.className = 'password-strength strength-strong';
    }

    strengthIndicator.textContent = feedback;
}

// Fungsi untuk menyimpan draft berita
function saveDraft() {
    const judul = document.getElementById('judul');
    if (!judul || !judul.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Judul berita harus diisi untuk menyimpan draft!',
            confirmButtonColor: 'var(--primary-color)'
        });
        return;
    }

    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.value = 'draft';
    }

    // Submit form secara normal
    const newsForm = document.querySelector('.news-form');
    if (newsForm) {
        newsForm.dispatchEvent(new Event('submit'));
    }
}

// Fungsi untuk mengecek pesan dari server (success/error)
function checkServerMessages() {
    // Cek alert messages dari PHP
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: alert.textContent.trim(),
                confirmButtonColor: 'var(--primary-color)',
                timer: 3000,
                showConfirmButton: false
            });
        } else if (alert.classList.contains('alert-error')) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: alert.textContent.trim(),
                confirmButtonColor: 'var(--primary-color)'
            });
        }
        
        // Hapus alert setelah ditampilkan
        setTimeout(() => {
            alert.remove();
        }, 100);
    });
}

// Fungsi global untuk admin dengan SweetAlert
async function editProfile() {
    const { value: formValues } = await Swal.fire({
        title: 'Edit Profil',
        html:
            '<input id="swal-input1" class="swal2-input" placeholder="Nama Lengkap">' +
            '<input id="swal-input2" class="swal2-input" placeholder="Email">' +
            '<input id="swal-input3" class="swal2-input" placeholder="Telepon">',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        confirmButtonColor: 'var(--primary-color)',
        preConfirm: () => {
            return [
                document.getElementById('swal-input1').value,
                document.getElementById('swal-input2').value,
                document.getElementById('swal-input3').value
            ]
        }
    });

    if (formValues) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Profil berhasil diperbarui!',
            confirmButtonColor: 'var(--primary-color)'
        });
    }
}

async function changePassword() {
    const { value: formValues } = await Swal.fire({
        title: 'Ubah Password',
        html:
            '<input id="swal-input1" class="swal2-input" placeholder="Password Saat Ini" type="password">' +
            '<input id="swal-input2" class="swal2-input" placeholder="Password Baru" type="password">' +
            '<input id="swal-input3" class="swal2-input" placeholder="Konfirmasi Password Baru" type="password">',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Ubah Password',
        cancelButtonText: 'Batal',
        confirmButtonColor: 'var(--primary-color)',
        preConfirm: () => {
            const password = document.getElementById('swal-input2').value;
            const confirmPassword = document.getElementById('swal-input3').value;
            
            if (password !== confirmPassword) {
                Swal.showValidationMessage('Konfirmasi password tidak sesuai');
                return false;
            }
            
            if (password.length < 8) {
                Swal.showValidationMessage('Password minimal 8 karakter');
                return false;
            }
            
            return [
                document.getElementById('swal-input1').value,
                password,
                confirmPassword
            ]
        }
    });

    if (formValues) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Password berhasil diubah!',
            confirmButtonColor: 'var(--primary-color)'
        });
    }
}

function viewProposal(proposalId) {
    Swal.fire({
        title: `Detail Proposal`,
        html: `
            <div style="text-align: left;">
                <p>Fitur detail proposal akan tersedia segera.</p>
            </div>
        `,
        width: 500,
        confirmButtonColor: 'var(--primary-color)'
    });
}

function reviewProposal(proposalId) {
    Swal.fire({
        title: `Review Proposal`,
        html: `
            <div style="text-align: left;">
                <p>Fitur review proposal akan tersedia segera.</p>
            </div>
        `,
        confirmButtonColor: 'var(--primary-color)'
    });
}

async function deleteProposal(proposalId) {
    const result = await Swal.fire({
        title: 'Hapus Proposal?',
        text: "Anda tidak akan dapat mengembalikan data ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--error-color)',
        cancelButtonColor: 'var(--secondary-color)',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: `Proposal berhasil dihapus.`,
            confirmButtonColor: 'var(--primary-color)',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

function viewNews(newsId) {
    Swal.fire({
        title: `Detail Berita`,
        html: `
            <div style="text-align: left;">
                <p>Fitur detail berita akan tersedia segera.</p>
            </div>
        `,
        width: 500,
        confirmButtonColor: 'var(--primary-color)'
    });
}

function editNews(newsId) {
    Swal.fire({
        title: `Edit Berita`,
        html: `
            <div style="text-align: left;">
                <p>Fitur edit berita akan tersedia segera.</p>
            </div>
        `,
        confirmButtonColor: 'var(--primary-color)'
    });
}

async function deleteNews(newsId) {
    const result = await Swal.fire({
        title: 'Hapus Berita?',
        text: "Anda tidak akan dapat mengembalikan data ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--error-color)',
        cancelButtonColor: 'var(--secondary-color)',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        // AJAX call untuk hapus berita
        try {
            const response = await fetch(`../controller/berita-controller.php?action=delete&id=${newsId}`);
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: 'Berita berhasil dihapus.',
                    confirmButtonColor: 'var(--primary-color)',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menghapus berita: ' + error.message,
                confirmButtonColor: 'var(--primary-color)'
            });
        }
    }
}

function viewAdmin(adminId) {
    Swal.fire({
        title: `Detail Admin`,
        html: `
            <div style="text-align: left;">
                <p>Fitur detail admin akan tersedia segera.</p>
            </div>
        `,
        width: 500,
        confirmButtonColor: 'var(--primary-color)'
    });
}

function editAdmin(adminId) {
    Swal.fire({
        title: `Edit Admin`,
        html: `
            <div style="text-align: left;">
                <p>Fitur edit admin akan tersedia segera.</p>
            </div>
        `,
        confirmButtonColor: 'var(--primary-color)'
    });
}

async function deleteAdmin(adminId) {
    const result = await Swal.fire({
        title: 'Hapus Admin?',
        text: "Anda tidak akan dapat mengembalikan data ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--error-color)',
        cancelButtonColor: 'var(--secondary-color)',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        // AJAX call untuk hapus admin
        try {
            const response = await fetch(`../controller/admin-controller.php?action=delete&id=${adminId}`);
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: 'Admin berhasil dihapus.',
                    confirmButtonColor: 'var(--primary-color)',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menghapus admin: ' + error.message,
                confirmButtonColor: 'var(--primary-color)'
            });
        }
    }
}

// Password strength indicator element
function createPasswordStrengthIndicator() {
    const passwordInput = document.getElementById('password');
    if (passwordInput && !document.getElementById('password-strength')) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.id = 'password-strength';
        strengthIndicator.className = 'password-strength';
        strengthIndicator.style.marginTop = '5px';
        strengthIndicator.style.fontSize = '0.875rem';
        passwordInput.parentNode.appendChild(strengthIndicator);
    }
}

// Initialize password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    createPasswordStrengthIndicator();
});