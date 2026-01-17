<?php
include 'koneksi.php';

// Pagination
$limit = 9; // Jumlah berita per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Query untuk mengambil berita dengan pagination
$query = "SELECT * FROM berita WHERE status_berita = 'publish' ORDER BY tanggal_dibuat DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

// Query untuk total berita
$count_query = "SELECT COUNT(*) as total FROM berita WHERE status_berita = 'publish'";
$count_result = mysqli_query($koneksi, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_berita = $count_row['total'];
$total_pages = ceil($total_berita / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Berita - Kesra</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/berita.css">
    <link rel="stylesheet" href="css/footer.css">
    <!-- <link rel="stylesheet" href="css/detail-berita.css"> -->
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
</head>
<body>
    <?php include 'komponen/navbar.php'; ?>

    <main>
        <section class="berita-section">
            <div class="container" style="margin-top: 100px; text-align: center;">
                <h1>Semua Berita</h1>
                <p class="section-subtitle">Informasi terbaru dari Bagian Kesejahteraan Rakyat</p>

                <div class="berita-container">
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
                            $arr_gambar = explode(',', $gambar);
                            $gambar_utama = !empty($arr_gambar[0]) ? $arr_gambar[0] : 'default.jpg';
                            ?>
                            <div class="berita-card">
                                <div class="berita-gambar">
                                    <img src="img/berita/<?php echo $gambar_utama; ?>" 
                                         alt="<?php echo htmlspecialchars($judul); ?>"
                                         onerror="this.src='img/berita/default.jpg'">
                                    <?php if (!empty($kategori)): ?>
                                        <span class="kategori-badge"><?php echo ucfirst($kategori); ?></span>
                                    <?php endif; ?>
                                    <?php if (count($arr_gambar) > 1): ?>
                                        <span class="jumlah-gambar">+<?php echo count($arr_gambar) - 1; ?> gambar</span>
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

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="dokumentasi.php?page=<?php echo $page - 1; ?>" class="page-link prev">Sebelumnya</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="dokumentasi.php?page=<?php echo $i; ?>" 
                           class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="dokumentasi.php?page=<?php echo $page + 1; ?>" class="page-link next">Selanjutnya</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'komponen/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>

<?php
mysqli_close($koneksi);
?>