<?php
declare(strict_types=1);
require_once 'Calculator.php';
require_once 'StudentPresenter.php';
require_once 'Student.php';

function uji(string $id, string $skenario, callable $aksi, mixed $harap): void
{
    try {
        $hasil = $aksi();
    } catch (Throwable $e) {
        $hasil = get_class($e);
    }
    $status = ($hasil === $harap) ? 'PASS' : 'FAIL';
    printf("%s | %s | %s | %s%s", $id, $skenario,
        var_export($hasil, true), $status, PHP_EOL);
}

$calc = new Calculator();
$view = new StudentPresenter();

uji('T01', 'add', fn() => $calc->add(10, 20) . ' dan '
    . $calc->add(10, 20, 30), '30 dan 60');
uji('T02', 'showData 1', fn() => $view->showData('Ahmad'),
    'Name: Ahmad');
uji('T03', 'showData 2', fn() => $view->showData('2026001', 'Ahmad'),
    'ID: 2026001, Name: Ahmad');
uji('T04', 'showData kosong', fn() => $view->showData(),
    'InvalidArgumentException');
uji('T05', 'method tidak dikenal', fn() => $view->ubahData('x'),
    'BadMethodCallException');
uji('T06', 'fee reguler',
    fn() => (int) (new RegularStudent('M01', 'Alya', 5000000))
        ->calculateFee(), 5000000);
uji('T07', 'fee beasiswa 40%',
    fn() => (int) (new ScholarshipStudent('M02', 'Bima', 5000000, 40))
        ->calculateFee(), 3000000);
uji('T08', 'diskon -1 dan 101', function () {
    $hasil = [];
    foreach ([-1, 101] as $d) {
        try {
            new ScholarshipStudent('M03', 'Cici', 5000000, (float) $d);
            $hasil[] = 'OK';
        } catch (InvalidArgumentException $e) {
            $hasil[] = 'InvalidArgumentException';
        }
    }
    return implode(', ', $hasil);
}, 'InvalidArgumentException, InvalidArgumentException');
uji('T09', 'summary',
    fn() => (new RegularStudent('M01', 'Alya', 5000000))->summary()
        . ' | '
        . (new ScholarshipStudent('M02', 'Bima', 5000000, 40))->summary(),
    'M01 - Alya | M02 - Bima [Scholarship]');

echo PHP_EOL . "T10: jalankan 'php eksperimen_final.php' secara manual." . PHP_EOL;
