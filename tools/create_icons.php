<?php
$dir = __DIR__ . '/public/icons';
is_dir($dir) || mkdir($dir, 0755, true);
foreach ([192, 512] as $s) {
    $img = imagecreatetruecolor($s, $s);
    imagefill($img, 0, 0, imagecolorallocate($img, 26, 95, 42));
    imagestring($img, 5, (int)(($s - 18) / 2), (int)(($s - 15) / 2), 'CV', imagecolorallocate($img, 255, 255, 255));
    imagepng($img, "$dir/icon-$s.png");
    imagedestroy($img);
}
echo "ok\n";
