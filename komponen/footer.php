<?php
// Gunakan koneksi yang sama dari index.php, jangan buat koneksi baru
// Asumsikan $koneksi sudah tersedia dari index.php

// Hitung statistik pengunjung hanya jika koneksi masih terbuka
if (isset($koneksi)) {
    $query_hari_ini = "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung WHERE tanggal_kunjungan = CURDATE()";
    $result_hari_ini = mysqli_query($koneksi, $query_hari_ini);
    $hari_ini = $result_hari_ini ? mysqli_fetch_assoc($result_hari_ini)['total'] : 0;

    $query_minggu_ini = "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung WHERE YEARWEEK(tanggal_kunjungan, 1) = YEARWEEK(CURDATE(), 1)";
    $result_minggu_ini = mysqli_query($koneksi, $query_minggu_ini);
    $minggu_ini = $result_minggu_ini ? mysqli_fetch_assoc($result_minggu_ini)['total'] : 0;

    $query_bulan_ini = "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung WHERE MONTH(tanggal_kunjungan) = MONTH(CURDATE()) AND YEAR(tanggal_kunjungan) = YEAR(CURDATE())";
    $result_bulan_ini = mysqli_query($koneksi, $query_bulan_ini);
    $bulan_ini = $result_bulan_ini ? mysqli_fetch_assoc($result_bulan_ini)['total'] : 0;

    $query_tahun_ini = "SELECT COUNT(DISTINCT ip_address) as total FROM pengunjung WHERE YEAR(tanggal_kunjungan) = YEAR(CURDATE())";
    $result_tahun_ini = mysqli_query($koneksi, $query_tahun_ini);
    $tahun_ini = $result_tahun_ini ? mysqli_fetch_assoc($result_tahun_ini)['total'] : 0;
} else {
    // Default values jika koneksi tidak tersedia
    $hari_ini = $minggu_ini = $bulan_ini = $tahun_ini = 0;
}
?>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-map">
            <h3>Lokasi Kami</h3>
            <div class="embed-map-responsive">
                <div class="embed-map-container">
                    <iframe 
                        class="embed-map-frame" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="https://maps.google.com/maps?width=600&height=400&hl=en&q=kantor%20walikota%20gorontalo&t=&z=20&ie=UTF8&iwloc=B&output=embed">
                    </iframe>
                    <a href="#" style="font-size:2px!important;color:gray!important;position:absolute;bottom:0;left:0;z-index:1;max-height:1px;overflow:hidden">source</a>
                </div>
                <style>
                    .embed-map-responsive {
                        position: relative;
                        text-align: right;
                        width: 100%;
                        height: 0;
                        padding-bottom: 66.6666666667%;
                    }
                    .embed-map-container {
                        overflow: hidden;
                        background: none !important;
                        width: 100%;
                        height: 100%;
                        position: absolute;
                        top: 0;
                        left: 0;
                    }
                    .embed-map-frame {
                        width: 100% !important;
                        height: 100% !important;
                        position: absolute;
                        top: 0;
                        left: 0;
                    }
                </style>
            </div>
        </div>
        
        <div class="footer-stats">
            <h3>Statistik Pengunjung</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $hari_ini; ?></span>
                    <span class="stat-label">Hari Ini</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $minggu_ini; ?></span>
                    <span class="stat-label">Minggu Ini</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $bulan_ini; ?></span>
                    <span class="stat-label">Bulan Ini</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $tahun_ini; ?></span>
                    <span class="stat-label">Tahun Ini</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-copyright">
        <p>&copy; <?php echo date('Y'); ?> Bagian Kesejahteraan Rakyat (Kesra) Bone Bolango. All rights reserved.</p>
    </div>
</footer>

<!-- Catat pengunjung -->
<?php 
if (isset($koneksi)) {
    include 'catat_pengunjung.php'; 
}
?>
