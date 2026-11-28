<?php
declare(strict_types=1);
require_once 'StudentPresenter.php';

$view = new StudentPresenter();
echo $view->showData('Ahmad') . PHP_EOL;
echo $view->showData('2026001', 'Ahmad') . PHP_EOL;

// Uji negatif: tangkap exception agar program tidak berhenti
$kasus = [
    'tanpa argumen' => fn() => $view->showData(),
    'bilangan'      => fn() => $view->showData(123),
    'method lain'   => fn() => $view->ubahData('x'),
];
foreach ($kasus as $label => $aksi) {
    try {
        $aksi();
    } catch (Throwable $e) {
        echo $label . ' => ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    }
}
