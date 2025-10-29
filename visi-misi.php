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
    <title>Visi Misi - Bagian Kesejahteraan Rakyat</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/visi-misi.css">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="visi-misi-main">
        <!-- Hero Section -->
        <section class="hero-visi-misi">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1>Visi & Misi</h1>
                    <p>Bagian Kesejahteraan Rakyat (Kesra)</p>
                    <div class="hero-decoration">
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="500"></div>
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="700"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visi Section -->
        <section class="visi-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Visi Kami</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="visi-card" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <div class="visi-icon" data-aos="zoom-in" data-aos-delay="400">
                        <i class="ph ph-target"></i>
                    </div>
                    <div class="visi-content">
                        <h3>Terwujudnya Masyarakat yang Sejahtera, Mandiri, dan Berkeadilan Sosial</h3>
                        <p>Visi Bagian Kesejahteraan Rakyat adalah menciptakan masyarakat yang memiliki kualitas hidup tinggi, mampu berdiri sendiri secara ekonomi, dan mendapatkan hak-hak sosial secara merata melalui program-program pemberdayaan yang berkelanjutan dan berbasis kebutuhan masyarakat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Misi Section -->
        <section class="misi-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Misi Kami</h2>
                    <div class="section-divider"></div>
                    <p>Untuk mewujudkan visi tersebut, Bagian Kesra memiliki beberapa misi utama:</p>
                </div>
                <div class="misi-grid">
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="300">
                            <i class="ph ph-hand-heart"></i>
                        </div>
                        <h3>Pelayanan Kesejahteraan Sosial</h3>
                        <p>Menyelenggarakan pelayanan kesejahteraan sosial yang komprehensif dan tepat sasaran bagi masyarakat, termasuk bantuan sosial, rehabilitasi sosial, dan jaminan sosial.</p>
                    </div>
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="400">
                            <i class="ph ph-mosque"></i>
                        </div>
                        <h3>Pengelolaan Bantuan Hibah</h3>
                        <p>Mengelola dan menyalurkan bantuan hibah untuk lembaga keagamaan, khususnya masjid, melalui sistem proposal online yang transparan dan akuntabel.</p>
                    </div>
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="500">
                            <i class="ph ph-users-three"></i>
                        </div>
                        <h3>Pemberdayaan Masyarakat</h3>
                        <p>Memberdayakan masyarakat melalui program pelatihan keterampilan, pendampingan usaha, dan pengembangan kapasitas untuk menciptakan kemandirian ekonomi.</p>
                    </div>
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="600">
                            <i class="ph ph-scales"></i>
                        </div>
                        <h3>Keadilan dan Transparansi</h3>
                        <p>Menjamin distribusi bantuan dan program sosial dilakukan secara adil, merata, dan transparan kepada seluruh lapisan masyarakat yang membutuhkan.</p>
                    </div>
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="700">
                            <i class="ph ph-handshake"></i>
                        </div>
                        <h3>Kemitraan Strategis</h3>
                        <p>Membangun kemitraan dengan berbagai pihak termasuk pemerintah, swasta, dan masyarakat untuk memperkuat program kesejahteraan masyarakat.</p>
                    </div>
                    <div class="misi-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">
                        <div class="misi-icon" data-aos="flip-left" data-aos-delay="800">
                            <i class="ph ph-chart-line-up"></i>
                        </div>
                        <h3>Pengembangan Program Inovatif</h3>
                        <p>Mengembangkan program-program inovatif yang sesuai dengan kebutuhan masyarakat dan perkembangan zaman untuk meningkatkan efektivitas pelayanan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Layanan Section -->
        <section class="layanan-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Layanan Publik Kami</h2>
                    <div class="section-divider"></div>
                    <p>Bagian Kesra menyediakan berbagai layanan publik untuk kemudahan masyarakat</p>
                </div>

                <div class="layanan-cards">
                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="layanan-icon">
                            <i class="ph ph-file-text"></i>
                        </div>
                        <h3>Pengajuan Proposal Bantuan Hibah</h3>
                        <p>Ajukan proposal bantuan hibah masjid secara online melalui sistem yang mudah dan transparan.</p>
                        <a href="ajukan-proposal.php" class="layanan-btn">Ajukan Proposal</a>
                    </div>

                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="layanan-icon">
                            <i class="ph ph-clipboard-text"></i>
                        </div>
                        <h3>Cek Status Proposal</h3>
                        <p>Pantau status pengajuan proposal Anda secara real-time, apakah diterima, ditolak, atau dalam proses.</p>
                        <a href="status-proposal.php" class="layanan-btn">Cek Status</a>
                    </div>

                    <div class="layanan-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="layanan-icon">
                            <i class="ph ph-folders"></i>
                        </div>
                        <h3>Dokumentasi Kegiatan</h3>
                        <p>Lihat dokumentasi lengkap berbagai kegiatan dan program yang telah dilaksanakan oleh Bagian Kesra.</p>
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

        <!-- Nilai Section -->
        <section class="nilai-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Nilai-Nilai Kami</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="nilai-container">
                    <div class="nilai-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="100">
                        <h4>Integritas</h4>
                        <p>Kami bekerja dengan jujur, transparan, dan bertanggung jawab dalam setiap tindakan dan pengambilan keputusan.</p>
                    </div>
                    <div class="nilai-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
                        <h4>Profesionalisme</h4>
                        <p>Kami memberikan pelayanan terbaik dengan kompetensi, etos kerja yang tinggi, dan standar pelayanan yang optimal.</p>
                    </div>
                    <div class="nilai-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="300">
                        <h4>Empati</h4>
                        <p>Kami memahami dan merespons kebutuhan masyarakat dengan penuh kepedulian dan perhatian.</p>
                    </div>
                    <div class="nilai-item" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
                        <h4>Inovasi</h4>
                        <p>Kami terus berupaya menemukan cara-cara baru dan kreatif untuk meningkatkan kesejahteraan masyarakat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Bersama Membangun Kesejahteraan</h2>
                    <p>Mari berkolaborasi untuk menciptakan masyarakat yang lebih sejahtera dan mandiri melalui program-program Bagian Kesra</p>
                    <div class="cta-buttons">
                        <a href="ajukan-proposal.php" class="btn btn-primary">Ajukan Bantuan Hibah</a>
                        <a href="dokumentasi.php" class="btn btn-secondary">Lihat Kegiatan</a>
                        <a href="struktur-organisasi.php" class="btn btn-outline">Struktur Organisasi</a>
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
if (isset($koneksi)) {
    mysqli_close($koneksi);
}