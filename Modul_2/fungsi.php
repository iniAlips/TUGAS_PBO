<?php 

function hitungLuas($panjang, $lebar) { 
return $panjang * $lebar; 
} 

// cek apakah form sudah dikirim (readline() diganti $_POST)
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    $panjang = $_POST['panjang'] ?? 0;
    $lebar = $_POST['lebar'] ?? 0;
    $hasil = hitungLuas($panjang, $lebar); 
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Luas</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 20px;
        }

        .card {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 36px 32px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 26px;
        }

        .subtitle {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin: 6px 0 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 2px solid #e2e2e2;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s, opacity 0.15s;
        }

        button:hover {
            transform: translateY(-2px);
            opacity: 0.92;
        }

        .result {
            margin-top: 24px;
            padding: 20px;
            background: #f5f6ff;
            border-left: 5px solid #667eea;
            border-radius: 10px;
            color: #333;
            font-size: 17px;
            line-height: 1.9;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Kalkulator Luas</h1>
        <p class="subtitle">Hitung luas persegi panjang dengan mudah</p>

        <form action="" method="post">
            <label for="panjang">Masukan panjang</label>
            <input type="number" step="any" id="panjang" name="panjang" placeholder="Contoh: 10"
                   value="<?= $submitted ? htmlspecialchars($panjang) : '' ?>" required>

            <label for="lebar">Masukan lebar</label>
            <input type="number" step="any" id="lebar" name="lebar" placeholder="Contoh: 5"
                   value="<?= $submitted ? htmlspecialchars($lebar) : '' ?>" required>

            <button type="submit">Hitung Luas</button>
        </form>

        <?php if ($submitted): ?>
        <div class="result">
<?php
echo "panjang = $panjang <br>";
echo "lebar = $lebar <br>";
echo "Luas = " . $hasil; 
?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
