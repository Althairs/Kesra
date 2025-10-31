<?php 
include 'koneksi.php';

$query = "SELECT * FROM berita ORDER BY tanggal_dibuat DESC LIMIT 6";
$result = mysqli_query($koneksi, $query);

$count_query = "SELECT COUNT(*) as total FROM berita";
$count_result = mysqli_query($koneksi, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_berita = $count_row['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas dan Fungsi - Bagian Kesejahteraan Rakyat</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/tugas-fungsi.css">
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="tugas-fungsi-main">
        <!-- Hero Section -->
        <section class="hero-tugas-fungsi">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1>Tugas dan Fungsi</h1>
                    <p>Bagian Kesejahteraan Rakyat (Kesra)</p>
                    <div class="hero-decoration">
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="500"></div>
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="700"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tugas Section -->
        <section class="tugas-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Tugas Pokok</h2>
                    <div class="section-divider"></div>
                    <p>Berikut adalah tugas pokok Bagian Kesejahteraan Rakyat dalam meningkatkan kesejahteraan masyarakat</p>
                </div>

                <div class="content-grid">
                    <div class="content-card" data-aos="fade-right" data-aos-duration="800" data-aos-delay="100">
                        <div class="card-icon">
                            <i class="ph ph-users-three"></i>
                        </div>
                        <div class="card-content">
                            <h3>Penyiapan Perumusan Kebijakan</h3>
                            <p>Melaksanakan penyiapan perumusan kebijakan Daerah untuk mendukung pengembangan kesejahteraan masyarakat.</p>
                        </div>
                    </div>

                    <div class="content-card" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                        <div class="card-icon">
                            <i class="ph ph-handshake"></i>
                        </div>
                        <div class="card-content">
                            <h3>Pengkoordinasian Kebijakan</h3>
                            <p>Melaksanakan pengoordinasian perumusan kebijakan Daerah dan pengoordinasian pelaksanaan tugas Perangkat Daerah.</p>
                        </div>
                    </div>

                    <div class="content-card" data-aos="fade-right" data-aos-duration="800" data-aos-delay="300">
                        <div class="card-icon">
                            <i class="ph ph-chart-line-up"></i>
                        </div>
                        <div class="card-content">
                            <h3>Pemantauan dan Evaluasi</h3>
                            <p>Melaksanakan pemantauan dan evaluasi pelaksanaan kebijakan Daerah untuk memastikan efektivitas program.</p>
                        </div>
                    </div>

                    <div class="content-card" data-aos="fade-left" data-aos-duration="800" data-aos-delay="400">
                        <div class="card-icon">
                            <i class="ph ph-git-fork"></i>
                        </div>
                        <div class="card-content">
                            <h3>Pembinaan Administrasi</h3>
                            <p>Pelaksanaan pembinaan administrasi di bidang bina mental, kesejahteraan sosial, serta pemberdayaan dan kesejahteraan masyarakat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fungsi Section -->
        <section class="fungsi-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Fungsi Utama</h2>
                    <div class="section-divider"></div>
                    <p>Fungsi-fungsi yang diemban oleh Bagian Kesejahteraan Rakyat dalam menjalankan tugasnya</p>
                </div>

                <div class="fungsi-grid">
                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="100">
                        <div class="fungsi-number">01</div>
                        <h3>Pengkoordinasian Perencanaan Program</h3>
                        <p>pengoordinasian perencanaan program kegiatan Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
                        <div class="fungsi-number">02</div>
                        <h3>Penyiapan Bahan Kebijakan</h3>
                        <p>pengoordinasian dan penyiapan bahan perumusan kebijakan Daerah di bidang bina mental, kesejahteraan sosial, serta pemberdayaan dan kesejahteraan masyarakat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="300">
                        <div class="fungsi-number">03</div>
                        <h3>Pengkoordinasian Pelaksanaan Kebijakan</h3>
                        <p>pengoordinasian pelaksanaan kebijakan Daerah di bidang bina mental, kesejahteraan sosial, serta pemberdayaan dan kesejahteraan masyarakat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
                        <div class="fungsi-number">04</div>
                        <h3>Pengkoordinasian Tugas Perangkat Daerah</h3>
                        <p>pengoordinasian pelaksanaan tugas Perangkat Daerah di bidang bina mental, kesejahteraan sosial, serta pemberdayaan dan kesejahteraan masyarakat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="500">
                        <div class="fungsi-number">05</div>
                        <h3>Pemantauan dan Evaluasi</h3>
                        <p>pemantauan dan evaluasi pelaksanaan kebijakan di bidang bina mental, kesejahteraan sosial, serta pemberdayaan dan kesejahteraan masyarakat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="600">
                        <div class="fungsi-number">06</div>
                        <h3>Pengkoordinasian Reformasi Birokrasi</h3>
                        <p>pengoordinasian pelaksanaan reformasi birokrasi, inovasi, sistem pengendalian internal pemerintah, zona integritas, ketatalaksanaan, dan budaya pemerintahan pada Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="700">
                        <div class="fungsi-number">07</div>
                        <h3>Pengelolaan Kearsipan</h3>
                        <p>pengoordinasian pengelolaan kearsipan dan perpustakaan Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="800">
                        <div class="fungsi-number">08</div>
                        <h3>Pengkoordinasian Evaluasi dan Laporan</h3>
                        <p>pengoordinasian pelaksanaan pemantauan, pengendalian, evaluasi, dan penyusunan laporan Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="900">
                        <div class="fungsi-number">09</div>
                        <h3>Pembinaan Jabatan Fungsional</h3>
                        <p>pembinaan dan pengoordinasian fasilitasi kelompok jabatan fungsional pada Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="1000">
                        <div class="fungsi-number">10</div>
                        <h3>Tindak Lanjut Pemeriksaan</h3>
                        <p>pengoordinasian pelaksanaan tindak lanjut laporan hasil pemeriksaan pada Bagian Kesejahteraan Rakyat.</p>
                    </div>

                    <div class="fungsi-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="1100">
                        <div class="fungsi-number">11</div>
                        <h3>Administrasi Umum</h3>
                        <p>pengoordinasian penyelenggaraan kegiatan administrasi umum, kepegawaian, perencanaan, evaluasi, pelaporan, keuangan, dan aset Bagian Kesejahteraan Rakyat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Layanan Section -->
        <section class="layanan-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Layanan Publik</h2>
                    <div class="section-divider"></div>
                    <p>Bagian Kesra menyediakan berbagai layanan publik untuk kemudahan masyarakat</p>
                </div>

                <div class="layanan-cards">
                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="layanan-icon">
                            <i class="ph ph-file-text"></i>
                        </div>
                        <h3>Pengajuan Proposal Bantuan</h3>
                        <p>Masyarakat dapat mengajukan proposal bantuan hibah masjid secara online melalui sistem yang tersedia.</p>
                        <a href="ajukan-proposal.php" class="layanan-btn">Ajukan Proposal</a>
                    </div>

                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="layanan-icon">
                            <i class="ph ph-clipboard-text"></i>
                        </div>
                        <h3>Cek Status Proposal</h3>
                        <p>Fitur untuk mengecek status pengajuan proposal apakah sudah diterima, ditolak, atau masih dalam proses.</p>
                        <a href="status-proposal.php" class="layanan-btn">Cek Status</a>
                    </div>

                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="layanan-icon">
                            <i class="ph ph-folders"></i>
                        </div>
                        <h3>Dokumentasi Kegiatan</h3>
                        <p>Menyajikan dokumentasi lengkap berbagai kegiatan yang telah dilaksanakan oleh Bagian Kesra.</p>
                        <a href="dokumentasi.php" class="layanan-btn">Lihat Dokumentasi</a>
                    </div>

                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                        <div class="layanan-icon">
                            <i class="ph ph-chart-bar"></i>
                        </div>
                        <h3>Kinerja Bagian Kesra</h3>
                        <p>Informasi tentang capaian kinerja, program yang telah dilaksanakan, dan laporan hasil kerja.</p>
                        <a href="kinerja.php" class="layanan-btn">Lihat Kinerja</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Program Unggulan Section -->
        <section class="program-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Program Unggulan</h2>
                    <div class="section-divider"></div>
                    <p>Beberapa program unggulan yang menjadi fokus Bagian Kesejahteraan Rakyat</p>
                </div>

                <div class="program-cards">
                    <div class="program-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="program-icon">
                            <i class="ph ph-mosque"></i>
                        </div>
                        <h3>Bantuan Hibah Masjid</h3>
                        <ul>
                            <li>Renovasi dan pembangunan masjid</li>
                            <li>Bantuan sarana ibadah</li>
                            <li>Pengembangan pendidikan agama</li>
                            <li>Program kemasyarakatan masjid</li>
                        </ul>
                    </div>

                    <div class="program-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="program-icon">
                            <i class="ph ph-hand-heart"></i>
                        </div>
                        <h3>Bantuan Sosial</h3>
                        <ul>
                            <li>Bantuan untuk keluarga tidak mampu</li>
                            <li>Santunan anak yatim dan dhuafa</li>
                            <li>Bantuan kesehatan masyarakat</li>
                            <li>Program beasiswa pendidikan</li>
                        </ul>
                    </div>

                    <div class="program-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="program-icon">
                            <i class="ph ph-briefcase"></i>
                        </div>
                        <h3>Pemberdayaan Ekonomi</h3>
                        <ul>
                            <li>Pelatihan keterampilan usaha</li>
                            <li>Bantuan modal usaha kecil</li>
                            <li>Pendampingan UMKM</li>
                            <li>Pemasaran produk lokal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Butuh Bantuan atau Informasi?</h2>
                    <p>Kami siap membantu Anda dalam mengakses program kesejahteraan sosial Bagian Kesra</p>
                    <div class="cta-buttons">
                        <a href="ajukan-proposal.php" class="btn btn-primary">Ajukan Bantuan Hibah</a>
                        <a href="status-proposal.php" class="btn btn-secondary">Cek Status Proposal</a>
                        <a href="dokumentasi.php" class="btn btn-outline">Lihat Kegiatan</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'komponen/footer.php'; ?>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>
</body>

</html>
<?php 
if(isset($koneksi)){
    mysqli_close($koneksi);
}
?>