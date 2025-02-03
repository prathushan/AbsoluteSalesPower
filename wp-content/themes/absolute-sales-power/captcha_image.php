<?php
if (!session_id()) {
    session_start();
}

// Generate random CAPTCHA text
$length = 6;
$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$captcha_text = '';
for ($i = 0; $i < $length; $i++) {
    $captcha_text .= $characters[mt_rand(0, strlen($characters) - 1)];
}
$_SESSION['captcha_text'] = $captcha_text;

// Create an image
$image_width = 200;
$image_height = 50;
$image = imagecreate($image_width, $image_height);

// Define colors
$background_color = imagecolorallocate($image, 255, 255, 255); // White
$text_color = imagecolorallocate($image, 0, 0, 0);             // Black
$line_color = imagecolorallocate($image, 64, 64, 64);          // Gray

// Add some noise (random lines)
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand() % $image_height, $image_width, rand() % $image_height, $line_color);
}

// Add the CAPTCHA text
$font_size = 5; // Size of the built-in font
$font_x = ($image_width - imagefontwidth($font_size) * strlen($captcha_text)) / 2;
$font_y = ($image_height - imagefontheight($font_size)) / 2;
imagestring($image, $font_size, $font_x, $font_y, $captcha_text, $text_color);

// Output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
