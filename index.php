<?php
include 'koneksi.php';

// Query untuk mengambil 6 berita terbaru dengan status publish
$query = "SELECT * FROM berita WHERE status_berita = 'publish' ORDER BY tanggal_dibuat DESC LIMIT 6";
$result = mysqli_query($koneksi, $query);

// Query untuk menghitung total berita yang publish
$count_query = "SELECT COUNT(*) as total FROM berita WHERE status_berita = 'publish'";
$count_result = mysqli_query($koneksi, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_berita = $count_row['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesra - Berita Terbaru</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/berita.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
    <!-- ADD AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
</head>

<body>
    <?php include 'komponen/navbar.php'; ?>

    <main>
        
        <div class="welcome-section">
            <h1 id="welcome-text"></h1>
        </div>

        <section class="berita-section">
            <h2>Berita Terbaru</h2>

            <!-- MODIFIED: Added center class and AOS attributes -->
            <div class="berita-container center">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id_berita = $row['id_berita'];
                        $judul = $row['judul_berita'];
                        $deskripsi = $row['deskripsi_berita'];
                        $gambar = $row['gambar_berita'];
                        $tanggal = $row['tanggal_dibuat'];
                        $kategori = $row['kategori_berita'];

                        $tanggal_format = date('d F Y', strtotime($tanggal));

                        // Ambil gambar pertama sebagai sampul
                        $arr_gambar = explode(',', $gambar);
                        $gambar_utama = !empty($arr_gambar[0]) ? $arr_gambar[0] : 'default.jpg';
                        ?>
                        <!-- ADDED: AOS animation -->
                        <div class="berita-card" data-aos="fade-up" data-aos-duration="800">
                            <div class="berita-gambar">
                                <img src="img/berita/<?php echo $gambar_utama; ?>" alt="<?php echo $judul; ?>"
                                    onerror="this.src='img/berita/default.jpg'">
                                <?php if (count($arr_gambar) > 1): ?>
                                    <span class="jumlah-gambar">+<?php echo count($arr_gambar) - 1; ?> gambar</span>
                                <?php endif; ?>
                                <?php if (!empty($kategori)): ?>
                                    <span class="kategori-badge"><?php echo ucfirst($kategori); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="berita-content">
                                <h3><?php echo htmlspecialchars($judul); ?></h3>
                                <p class="berita-tanggal"><?php echo $tanggal_format; ?></p>
                                <p class="berita-deskripsi"><?php echo substr(htmlspecialchars($deskripsi), 0, 150); ?>...</p>
                                <a href="detail-berita.php?id=<?php echo $id_berita; ?>" class="btn-detail">Baca Selengkapnya</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='no-berita'>Tidak ada berita untuk ditampilkan.</p>";
                }
                ?>
            </div>

            <?php if ($total_berita > 6): ?>
                <div class="lihat-semua-container">
                    <a href="dokumentasi.php" class="btn-lihat-semua">Lihat Semua Berita</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include'komponen/footer.php';?>
    
    <!-- ADD AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Initialize AOS
        AOS.init();
        
        var typed = new Typed("#welcome-text", {
            strings: ["Selamat Datang Di Website Profil Bagian Kesejahteraan Rakyat (Kesra)"],
            typeSpeed: 40,
            loop: false
        });
    </script>
</body>

</html>
<?php
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>