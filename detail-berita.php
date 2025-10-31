<?php
include 'koneksi.php';

// Ambil ID berita dari parameter URL
$id_berita = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data berita
$query = "SELECT * FROM berita WHERE id_berita = $id_berita AND status_berita = 'publish'";
$result = mysqli_query($koneksi, $query);

// Cek apakah berita ditemukan
if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$berita = mysqli_fetch_assoc($result);

// Query berita terkait (berdasarkan kategori)
$kategori = $berita['kategori_berita'];
$query_terkait = "SELECT * FROM berita 
                  WHERE kategori_berita = '$kategori' 
                  AND id_berita != $id_berita 
                  AND status_berita = 'publish' 
                  ORDER BY tanggal_dibuat DESC 
                  LIMIT 3";
$result_terkait = mysqli_query($koneksi, $query_terkait);

// Format tanggal
$tanggal = date('d F Y', strtotime($berita['tanggal_dibuat']));
$waktu = date('H:i', strtotime($berita['tanggal_dibuat']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul_berita']); ?> - Kesra</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/berita.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/detail-berita.css">
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
</head>
<body>
    <?php include 'komponen/navbar.php'; ?>

    <main class="detail-berita-container">
        <!-- Breadcrumb Navigation -->
        <!-- <nav class="breadcrumb">
            <div class="container">
                <a href="index.php">Beranda</a>
                <span class="separator">/</span>
                <a href="dokumentasi.php">Berita</a>
                <span class="separator">/</span>
                <span class="current">Detail Berita</span>
            </div>
        </nav> -->

        <article class="berita-detail">
            <div class="container">
                <!-- Header Berita -->
                <header class="berita-header">
                    <span class="kategori-badge"><?php echo ucfirst($berita['kategori_berita'] ?? 'Berita'); ?></span>
                    <h1><?php echo htmlspecialchars($berita['judul_berita']); ?></h1>
                    
                    <div class="berita-meta">
                        <div class="meta-item">
                            <i class="ph ph-calendar"></i>
                            <span><?php echo $tanggal; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="ph ph-clock"></i>
                            <span><?php echo $waktu; ?> WIB</span>
                        </div>
                    </div>
                </header>

                <!-- Slider Gambar Utama -->
                <?php
                $gambar_array = explode(',', $berita['gambar_berita']);
                $gambar_utama = !empty($gambar_array[0]) ? $gambar_array[0] : null;
                
                if ($gambar_utama): 
                ?>
                <div class="berita-gambar-utama">
                    <div class="image-slider" id="imageSlider">
                        <!-- Slide Gambar -->
                        <?php foreach ($gambar_array as $index => $gambar): ?>
                            <?php if (!empty($gambar)): ?>
                            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 data-index="<?php echo $index; ?>">
                                <img src="img/berita/<?php echo $gambar; ?>" 
                                     alt="<?php echo htmlspecialchars($berita['judul_berita']); ?> - Gambar <?php echo $index + 1; ?>"
                                     onerror="this.src='img/berita/default.jpg'">
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <!-- Navigasi Panah (hanya tampil jika ada lebih dari 1 gambar) -->
                        <?php if (count($gambar_array) > 1): ?>
                        <button class="slider-nav prev" onclick="changeSlide(-1)">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <button class="slider-nav next" onclick="changeSlide(1)">
                            <i class="ph ph-caret-right"></i>
                        </button>
                        
                        <!-- Indikator Dot -->
                        <div class="slider-dots">
                            <?php foreach ($gambar_array as $index => $gambar): ?>
                                <?php if (!empty($gambar)): ?>
                                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                      onclick="currentSlide(<?php echo $index; ?>)"></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Deskripsi Singkat Berita -->
                <div class="berita-deskripsi">
                    <p><?php echo htmlspecialchars($berita['deskripsi_berita']); ?></p>
                </div>

                <!-- Konten Berita Lengkap -->
                <div class="berita-konten">
                    <?php echo nl2br(htmlspecialchars($berita['konten_berita'])); ?>
                </div>

                <!-- Galeri Gambar (jika ada lebih dari 1 gambar) -->
                <?php if (count($gambar_array) > 1): ?>
                <div class="berita-galeri">
                    <h3>Galeri Foto</h3>
                    <div class="galeri-grid">
                        <?php 
                        // Tampilkan semua gambar termasuk yang pertama
                        foreach ($gambar_array as $index => $gambar): 
                            if (!empty($gambar)):
                        ?>
                        <div class="galeri-item">
                            <img src="img/berita/<?php echo $gambar; ?>" 
                                 alt="Galeri <?php echo $index + 1; ?> - <?php echo htmlspecialchars($berita['judul_berita']); ?>"
                                 onclick="openModal('<?php echo $gambar; ?>')">
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tombol Bagikan Berita -->
                <div class="berita-share">
                    <h4>Bagikan Berita:</h4>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="share-btn facebook">
                            <i class="ph ph-facebook-logo"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($berita['judul_berita']); ?>" 
                           target="_blank" class="share-btn twitter">
                            <i class="ph ph-twitter-logo"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($berita['judul_berita'] . ' - http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" class="share-btn whatsapp">
                            <i class="ph ph-whatsapp-logo"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <!-- Section Berita Terkait -->
        <?php if (mysqli_num_rows($result_terkait) > 0): ?>
        <section class="berita-terkait">
            <div class="container">
                <h2>Berita Terkait</h2>
                <div class="terkait-grid">
                    <?php while ($berita_terkait = mysqli_fetch_assoc($result_terkait)): 
                        $gambar_terkait = explode(',', $berita_terkait['gambar_berita']);
                        $gambar_utama_terkait = !empty($gambar_terkait[0]) ? $gambar_terkait[0] : 'default.jpg';
                    ?>
                    <article class="terkait-card">
                        <div class="terkait-gambar">
                            <img src="img/berita/<?php echo $gambar_utama_terkait; ?>" 
                                 alt="<?php echo htmlspecialchars($berita_terkait['judul_berita']); ?>"
                                 onerror="this.src='img/berita/default.jpg'">
                        </div>
                        <div class="terkait-content">
                            <h3>
                                <a href="detail_berita.php?id=<?php echo $berita_terkait['id_berita']; ?>">
                                    <?php echo htmlspecialchars($berita_terkait['judul_berita']); ?>
                                </a>
                            </h3>
                            <p class="terkait-tanggal"><?php echo date('d F Y', strtotime($berita_terkait['tanggal_dibuat'])); ?></p>
                            <p class="terkait-deskripsi"><?php echo substr(htmlspecialchars($berita_terkait['deskripsi_berita']), 0, 100); ?>...</p>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Modal untuk menampilkan gambar dalam ukuran besar -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
        <div class="modal-caption" id="modalCaption"></div>
    </div>

    <?php include 'komponen/footer.php'; ?>

    <script src="js/detail-berita.js"></script>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($koneksi);
?>