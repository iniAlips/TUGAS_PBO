<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style="background-color: gray;">


<?php

$nilai = readline("Masukan nilai: ");



if ($nilai < 0){
    echo "angka ini bernilai minus";

}elseif ($nilai < 10){
    echo "Angka ini kurang dari 10";
}elseif ($nilai < 50){
    echo "Angka ini kurang dari 50 dan lebih dari 10";

}elseif ($nilai < 100){
    echo "angka ini kurang dari 100 dan lebih dari 50";

}elseif ($nilai > 100){
    echo "angka ini lebih dari 100";

}else{
    echo "Angka $nilai tidak diketaui";
}



?>



    
</body>
</html>
