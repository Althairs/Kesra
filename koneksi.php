<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "kesra";

$koneksi = mysqli_connect($host, $username, $password, $database);
if(!$koneksi){
    die('koneksi gagal dilakukan'.mysqli_connect_error());
}