<?php
require "vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// URL for QR code
$redirectUrl = "https://cornelius98.github.io/eyesight-systems-engineering-limited/";

// QR options
$options = new QROptions([
    'version'    => QRCode::VERSION_AUTO,
    'eccLevel'   => QRCode::ECC_H,
    'scale'      => 8,
    'imageBase64'=> false,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
]);


// Generate RAW PNG data
$qr = new QRCode($options);
$qrPng = $qr->render($redirectUrl);

// 🧪 DEBUG CHECK — is PNG valid?
if (substr($qrPng, 0, 8) !== "\x89PNG\x0D\x0A\x1A\x0A") {
    die("QR code is NOT valid PNG data.");
}

// Create QR image from raw PNG
$qrImage = imagecreatefromstring($qrPng);
if (!$qrImage) {
    die("Failed to create QR GD image");
}

// Load logo
$logo = imagecreatefrompng('logo.png');
$logoW = imagesx($logo);
$logoH = imagesy($logo);

// Resize logo (safe)
$maxLogoSize = 70;
$scale = min($maxLogoSize / $logoW, $maxLogoSize / $logoH);

$newW = (int)($logoW * $scale);
$newH = (int)($logoH * $scale);

$logoResized = imagecreatetruecolor($newW, $newH);
imagealphablending($logoResized, false);
imagesavealpha($logoResized, true);
imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, $newW, $newH, $logoW, $logoH);

// Center logo on QR
$qrW = imagesx($qrImage);
$qrH = imagesy($qrImage);
$dstX = ($qrW - $newW) / 2;
$dstY = ($qrH - $newH) / 2;

// Merge
imagecopy($qrImage, $logoResized, $dstX, $dstY, 0, 0, $newW, $newH);

// Save file
$savePath = __DIR__ . "/qr_with_logo.png";
imagepng($qrImage, $savePath);

// Output to browser
header("Content-Type: image/png");
imagepng($qrImage);

// cleanup
imagedestroy($qrImage);
imagedestroy($logo);
imagedestroy($logoResized);
?>