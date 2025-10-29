<?php
include 'koneksi.php';

$query = "SELECT * FROM berita ORDER BY tanggal_dibuat DESC LIMIT 6";
$result = mysqli_query($koneksi, $query);


$count_query = "SELECT COUNT(*) as total FROM berita";
$count_result = mysqli_query($koneksi, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_berita = $count_row['total'];


// mysqli_close($koneksi);
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
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

</head>

<div style="background: #f8f9fa; padding: 10px; margin: 10px; border-radius: 5px;">
    <small>Debug Info - IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> | Host: <?php echo $_SERVER['HTTP_HOST']; ?></small>
</div>
<body>
    <?php include 'komponen/navbar.php'; ?>

    <main>
        <div class="welcome-section">
            <h1 id="welcome-text"></h1>
            <!-- <p>
                Selamat datang di website profil Bagian Kesejahteraan Rakyat (Kesra).
                Di sini Anda bisa membaca profil, mengajukan bantuan hibah masjid, melihat
                dokumentasi kegiatan, dan memantau kinerja program.
            </p> -->
        </div>

        <section class="berita-section">
            <h2>Berita Terbaru</h2>

            <div class="berita-container">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id_berita = $row['id_berita'];
                        $judul = $row['judul_berita'];
                        $deskripsi = $row['deskripsi_berita'];
                        $gambar = $row['gambar_berita'];
                        $tanggal = $row['tanggal_dibuat'];

                        $tanggal_format = date('d F Y', strtotime($tanggal));

                        $arr_gambar = explode(',', $gambar);
                        $gambar_utama = !empty($arr_gambar[0]) ? $arr_gambar[0] : 'default.jpg';
                        ?>
                        <div class="berita-card">
                            <div class="berita-gambar">
                                <img src="img/berita/<?php echo $gambar_utama; ?>" alt="<?php echo $judul; ?>"
                                    onerror="this.src='img/berita/default.jpg'">
                                <?php if (count($arr_gambar) > 1): ?>
                                    <span class="jumlah-gambar">+<?php echo count($arr_gambar) - 1; ?> gambar</span>
                                <?php endif; ?>
                            </div>

                            <div class="berita-content">
                                <h3><?php echo htmlspecialchars($judul); ?></h3>
                                <p class="berita-tanggal"><?php echo $tanggal_format; ?></p>
                                <p class="berita-deskripsi"><?php echo substr(htmlspecialchars($deskripsi), 0, 150); ?>...</p>
                                <a href="detail_berita.php?id=<?php echo $id_berita; ?>" class="btn-detail">Baca
                                    Selengkapnya</a>
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
    
    <script src="js/script.js"></script>
    <script>
        var typed = new Typed("#welcome-text", {
            strings: ["Selamat Datang Di Website Profil Bagian Kesejahteraan Rakyat (Kesra)."],
            typeSpeed: 40,
            // backSpeed: 40,
            loop: false
        });
    </script>

    <!-- <script>
    const text = "Selamat datang di website profil Bagian Kesejahteraan Rakyat";
    const element = document.getElementById("welcome-text");
    let index = 0;
    let isDeleting = false;

    function typeEffect() {
      if (!isDeleting && index <= text.length) {
        element.textContent = text.slice(0, index++);
        setTimeout(typeEffect, 100);
      } else if (isDeleting && index >= 0) {
        element.textContent = text.slice(0, index--);
        setTimeout(typeEffect, 50);
      } else {
        isDeleting = !isDeleting;
        setTimeout(typeEffect, 1000);
      }
    }

    typeEffect();
  </script> -->
</body>

</html>
<?php

if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>