<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Bagian Kesejahteraan Rakyat</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/kontak.css">
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="kontak-main">
        <!-- Hero Section -->
        <section class="hero-kontak">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1>Hubungi Kami</h1>
                    <p>Bagian Kesejahteraan Rakyat (Kesra)</p>
                    <div class="hero-decoration">
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="500"></div>
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="700"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kontak Section -->
        <section class="kontak-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Kontak Kami</h2>
                    <div class="section-divider"></div>
                    <p>Silakan hubungi kami untuk informasi lebih lanjut mengenai program dan layanan Bagian Kesra</p>
                </div>

                <div class="kontak-grid">
                    <div class="kontak-card" data-aos="fade-right" data-aos-duration="800" data-aos-delay="100">
                        <div class="kontak-icon">
                            <i class="ph ph-user-circle"></i>
                        </div>
                        <div class="kontak-info">
                            <h3>Kabag</h3>
                            <p>+62 852-4012-3904</p>
                        </div>
                    </div>

                    <div class="kontak-card" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                        <div class="kontak-icon">
                            <i class="ph ph-user-circle"></i>
                        </div>
                        <div class="kontak-info">
                            <h3>Kasubag</h3>
                            <p>+62 813-5188-7133</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Info Section -->
        <section class="info-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Informasi Layanan</h2>
                    <div class="section-divider"></div>
                    <p>Jam operasional dan informasi penting lainnya</p>
                </div>

                <div class="info-grid">
                    <div class="info-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="info-icon">
                            <i class="ph ph-clock"></i>
                        </div>
                        <h3>Jam Operasional</h3>
                        <p>Senin - Jumat: 08.00 - 16.00 WIB</p>
                        <p>Sabtu: 08.00 - 14.00 WIB</p>
                    </div>

                    <div class="info-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="info-icon">
                            <i class="ph ph-map-pin"></i>
                        </div>
                        <h3>Lokasi Kantor</h3>
                        <p>Jl. Merdeka No. 123, Kota Jasa</p>
                        <p>Gedung Pemerintah Kota Lantai 3</p>
                    </div>

                    <div class="info-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="info-icon">
                            <i class="ph ph-envelope"></i>
                        </div>
                        <h3>Email</h3>
                        <p>kesra@kotajasa.go.id</p>
                        <p>info.kesra@kotajasa.go.id</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Butuh Bantuan Lainnya?</h2>
                    <p>Kami siap membantu Anda dengan berbagai layanan dan program kesejahteraan masyarakat</p>
                    <div class="cta-buttons">
                        <a href="ajukan-proposal.php" class="btn btn-primary">Ajukan Bantuan Hibah</a>
                        <a href="visi-misi.php" class="btn btn-secondary">Tentang Kami</a>
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
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>