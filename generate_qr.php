<?php
require "vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// URL for QR code
$redirectUrl = "https://chanda360.github.io/eyesight-engineering-limited/";

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

// Validate PNG
if (substr($qrPng, 0, 8) !== "\x89PNG\x0D\x0A\x1A\x0A") {
    die("QR code is NOT valid PNG data.");
}

// Create QR image from raw PNG
$qrImage = imagecreatefromstring($qrPng);
if (!$qrImage) {
    die("Failed to create QR GD image");
}

// Save file
$savePath = __DIR__ . "/qr_no_logo.png";
imagepng($qrImage, $savePath);

// Output to browser
header("Content-Type: image/png");
imagepng($qrImage);

// Cleanup
imagedestroy($qrImage);
?>
