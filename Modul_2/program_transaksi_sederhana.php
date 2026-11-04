<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="background-color: gray;">

<?php



function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

$barang = readline("Nama barang: ");
$harga  = (float) readline("Harga: ");
$jumlah = (int) readline("Jumlah: ");

$total = $harga * $jumlah;
echo "Total: " . rupiah($total) . "\n";

$bayar = (float) readline("Uang dibayarkan: ");

if ($bayar < $total) {
    echo "Uang kurang " . rupiah($total - $bayar) . "\n";
} else {
    echo "\n===== STRUK =====\n";
    echo "Barang    : $barang\n";
    echo "Harga     : " . rupiah($harga) . " x $jumlah\n";
    echo "Total     : " . rupiah($total) . "\n";
    echo "Bayar     : " . rupiah($bayar) . "\n";
    echo "Kembalian : " . rupiah($bayar - $total) . "\n";
}


?>

    
</body>
</html>
