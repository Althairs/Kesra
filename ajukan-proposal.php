<?php
include 'koneksi.php';

$query = "SELECT * FROM berita ORDER BY tanggal_dibuat DESC LIMIT 3";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelayanan Kesra - Kabupaten Gorontalo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/ajukan-proposal.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="pelayanan-main">
        <!-- Hero Section -->
        <section class="hero-pelayanan">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1>Selamat Datang di Website Pelayanan Kesra</h1>
                    <h2>Sistem Pelayanan Bagian Kesra</h2>
                    <div class="hero-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>KABUPATEN GORONTALO</span>
                    </div>
                    <p>Layanan pengajuan bantuan hibah masjid secara online yang cepat, transparan, dan akuntabel</p>
                    <a href="login.php" class="btn-login-hero">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                </div>
                <div class="hero-decoration">
                    <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="500"></div>
                    <div class="decoration-circle" data-aos="zoom-in" data-aos-delay="700"></div>
                </div>
            </div>
        </section>

        <!-- Tata Cara Section -->
        <section class="tata-cara-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Tata Cara Permohonan Bantuan</h2>
                    <div class="section-divider"></div>
                    <p>Berikut ini adalah tatacara dalam membuat pengajuan bantuan melalui website bantuan</p>
                </div>

                <div class="tata-cara-steps">
                    <div class="step-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3>ISI FORMULIR REGISTRASI</h3>
                        <p>Akses menu registrasi pilih sesuai dengan kelembagaan masing masing, satu lembaga hanya boleh satu akun, di filter berdasarkan nomor izin (IJOP)</p>
                    </div>

                    <div class="step-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h3>LOGIN MENGGUNAKAN USERNAME DAN PASSWORD</h3>
                        <p>Setelah registrasi berhasil silahkan login dengan username dan password yang sudah didaftarkan</p>
                    </div>

                    <div class="step-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                        <h3>PERIKSA PENGAJUAN BANTUAN ANDA</h3>
                        <p>Periksa kembali kelengkapan pengajuan bantuan Anda apakah telah sesuai dengan kriteria dan semua dokumen sebelum di resume</p>
                    </div>

                    <div class="step-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <i class="fas fa-binoculars"></i>
                        </div>
                        <h3>PANTAU PENGAJUAN BANTUAN</h3>
                        <p>Pantau pengajuan bantuan dengan "Log In" pada akun anda dan melihat status pengajuan apakah sudah di proses atau di tolak melalui di setiap pengajuan</p>
                    </div>
                </div>

                <div class="cta-tata-cara" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                    <h3>Siap Mengajukan Bantuan?</h3>
                    <p>Daftar sekarang dan ajukan proposal bantuan hibah masjid Anda</p>
                    <div class="cta-buttons">
                        <a href="login.php" class="btn btn-primary">Daftar Sekarang</a>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Berita Section -->
        <section class="berita-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Berita Informasi</h2>
                    <div class="section-divider"></div>
                    <p>Informasi terbaru seputar kegiatan dan program Bagian Kesra Kabupaten Gorontalo</p>
                </div>

                <div class="berita-grid">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $judul = $row['judul_berita'];
                            $deskripsi = $row['deskripsi_berita'];
                            $gambar = $row['gambar_berita'];
                            $tanggal = date('d M Y', strtotime($row['tanggal_dibuat']));
                            
                            $arr_gambar = explode(',', $gambar);
                            $gambar_utama = !empty($arr_gambar[0]) ? $arr_gambar[0] : 'default.jpg';
                            ?>
                            <div class="berita-card" data-aos="fade-up" data-aos-duration="800">
                                <div class="berita-image">
                                    <img src="img/berita/<?php echo $gambar_utama; ?>" alt="<?php echo $judul; ?>"
                                        onerror="this.src='img/berita/default.jpg'">
                                </div>
                                <div class="berita-content">
                                    <span class="berita-date"><?php echo $tanggal; ?></span>
                                    <h3><?php echo htmlspecialchars($judul); ?></h3>
                                    <p><?php echo substr(htmlspecialchars($deskripsi), 0, 120); ?>...</p>
                                    <a href="detail_berita.php?id=<?php echo $row['id_berita']; ?>" class="berita-link">Baca Selengkapnya</a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-berita">Tidak ada berita untuk ditampilkan.</div>';
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Statistik Section -->
        <section class="statistik-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Kelembagaan Yang Bergabung</h2>
                    <div class="section-divider"></div>
                </div>

                <div class="statistik-grid">
                    <div class="statistik-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="100">
                        <div class="statistik-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="statistik-number">743</div>
                        <div class="statistik-label">Yayasan</div>
                    </div>

                    <div class="statistik-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
                        <div class="statistik-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="statistik-number">1.078</div>
                        <div class="statistik-label">Lembaga Formal & Non Formal</div>
                    </div>

                    <div class="statistik-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="300">
                        <div class="statistik-icon">
                            <i class="fas fa-mosque"></i>
                        </div>
                        <div class="statistik-number">1</div>
                        <div class="statistik-label">Lembaga Ibadah</div>
                    </div>

                    <div class="statistik-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
                        <div class="statistik-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="statistik-number">214</div>
                        <div class="statistik-label">Kelompok Masyarakat</div>
                    </div>

                    <div class="statistik-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="500">
                        <div class="statistik-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="statistik-number">0</div>
                        <div class="statistik-label">Universitas</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up" data-aos-duration="800">
                    <h2>Frequently Asked Questions</h2>
                    <div class="section-divider"></div>
                    <p>Berikut ini adalah daftar pertanyaan yang sering ditanyakan</p>
                </div>

                <div class="faq-container">
                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800">
                        <div class="faq-question">
                            <h3>Apakah manfaat dari Aplikasi Pemohonan Bantuan Kesra?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Aplikasi Pemohonan Bantuan Kesra memberikan kemudahan dalam mengajukan proposal bantuan hibah masjid secara online, proses yang transparan, dan pemantauan status pengajuan secara real-time.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <div class="faq-question">
                            <h3>Berapa lama respon atas Pengajuan Bantuan akan di proses?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Proses verifikasi dan peninjauan proposal membutuhkan waktu sekitar 5-10 hari kerja. Anda dapat memantau status pengajuan melalui akun Anda secara real-time.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <div class="faq-question">
                            <h3>Apakah bentuk respon yang diberikan atas pengajuan bantuan?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Respon yang diberikan berupa status pengajuan (Diterima, Ditolak, atau Perlu Revisi) disertai dengan catatan dan alasan dari tim verifikasi Bagian Kesra.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="faq-question">
                            <h3>Apakah setiap melakukan pengajuan bantuan harus membuat dan register username baru?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Tidak perlu. Satu akun dapat digunakan untuk melakukan multiple pengajuan bantuan. Anda hanya perlu mendaftar sekali dan dapat menggunakan akun yang sama untuk pengajuan selanjutnya.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                        <div class="faq-question">
                            <h3>Apakah pengajuan yang saya berikan akan selalu mendapatkan respon?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, setiap pengajuan yang masuk akan mendapatkan respon berupa status update yang dapat dilihat melalui akun Anda dalam waktu 5-10 hari kerja.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                        <div class="faq-question">
                            <h3>Saya sudah mengirimkan pengajuan bantuan namun di kemudian hari saya ingin merubah/menambahkan data terkait pengajuan yang saya lakukan, apa yang harus saya lakukan? Apakah harus membuat pengajuan baru?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Jika pengajuan masih dalam status "Menunggu Review", Anda dapat melakukan perubahan melalui menu edit proposal. Jika sudah diproses, silakan hubungi admin melalui kontak yang tersedia untuk konsultasi lebih lanjut.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="final-cta-section">
            <div class="container">
                <div class="final-cta-content" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Mulai Ajukan Bantuan Anda Sekarang</h2>
                    <p>Daftar dan login untuk mengakses layanan pengajuan bantuan hibah masjid secara online</p>
                    <div class="cta-buttons">
                        <a href="login.php" class="btn btn-primary">Daftar Akun</a>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'komponen/footer.php'; ?>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/script.js"></script>
    <script src="js/ajukan-proposal.js"></script>
</body>

</html>
<?php
if (isset($koneksi)) {
    mysqli_close($koneksi);
}