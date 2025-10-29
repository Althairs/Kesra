<?php
include 'koneksi.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - Bagian Kesejahteraan Rakyat</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/struktur-organisasi.css">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="struktur-main">
        <!-- Hero Section -->
        <section class="hero-struktur">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1>Struktur Organisasi</h1>
                    <p>Bagian Kesejahteraan Rakyat (Kesra)</p>
                    <div class="hero-decoration">
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="500"></div>
                        <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="700"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PDF Viewer Section -->
        <section class="pdf-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Struktur Organisasi Kesra</h2>
                    <div class="section-divider"></div>
                    <p>Berikut adalah struktur organisasi Bagian Kesejahteraan Rakyat yang menunjukkan hierarki dan hubungan kerja dalam organisasi.</p>
                </div>

                <div class="pdf-container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-controls">
                            <button class="control-btn" id="zoom-out" title="Zoom Out">
                                <i class="ph ph-minus"></i>
                            </button>
                            <span class="zoom-level" id="zoom-level">100%</span>
                            <button class="control-btn" id="zoom-in" title="Zoom In">
                                <i class="ph ph-plus"></i>
                            </button>
                            <button class="control-btn" id="fullscreen" title="Fullscreen">
                                <i class="ph ph-arrows-out"></i>
                            </button>
                            <a href="img/struktur.pdf" download class="control-btn download-btn" title="Download PDF">
                                <i class="ph ph-download"></i>
                                Unduh
                            </a>
                        </div>
                        
                        <div class="pdf-viewer">
                            <iframe 
                                src="img/struktur.pdf" 
                                id="pdf-frame"
                                width="100%" 
                                height="600"
                                frameborder="0"
                                allowfullscreen>
                                Browser Anda tidak mendukung tampilan PDF. 
                                <a href="img/struktur.pdf" download>Unduh PDF</a> untuk melihatnya.
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="info-grid" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="ph ph-users"></i>
                        </div>
                        <h3>Tim yang Solid</h3>
                        <p>Struktur organisasi yang jelas memastikan setiap anggota tim memahami peran dan tanggung jawabnya.</p>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="ph ph-flow-arrow"></i>
                        </div>
                        <h3>Alur Kerja Efisien</h3>
                        <p>Hierarki yang terorganisir memungkinkan alur komunikasi dan koordinasi yang efektif.</p>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="ph ph-git-fork"></i>
                        </div>
                        <h3>Koordinasi Terpadu</h3>
                        <p>Setiap divisi bekerja secara sinergis untuk mencapai tujuan kesejahteraan masyarakat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Info Section -->
        <section class="contact-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Informasi Kontak</h2>
                    <div class="section-divider"></div>
                    <p>Untuk informasi lebih lanjut mengenai struktur organisasi, hubungi:</p>
                </div>
                <div class="contact-info" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    <div class="contact-item">
                        <i class="ph ph-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>kesra@example.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="ph ph-phone"></i>
                        <div>
                            <h4>Telepon</h4>
                            <p>(021) 1234-5678</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="ph ph-map-pin"></i>
                        <div>
                            <h4>Alamat</h4>
                            <p>Gedung Kantor Bagian Kesra, Jl. Contoh No. 123</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'komponen/footer.php'; ?>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/script.js"></script>
    <script src="js/struktur-organisasi.js"></script>
</body>

</html>
<?php
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>