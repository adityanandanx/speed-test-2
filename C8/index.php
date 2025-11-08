<?php

$chars = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['img'])) {
  $img_pth = $_FILES['img']['tmp_name'];
  $image_info = getimagesize($img_pth);
  // $img = @imagecreatefrompng($img_pth);
  list($original_width, $original_height, $image_type) = $image_info;

  $source_image = null;
  switch ($image_type) {
    case IMAGETYPE_PNG:
      $source_image = imagecreatefrompng($img_pth);
      break;
    case IMAGETYPE_JPEG:
      $source_image = imagecreatefromjpeg($img_pth);
      break;
    default:
      die();
  }
  if ($source_image) {
    $width = $original_width;
    $height = $original_height;
    $ratio = $original_width / $original_height;

    if ($width > 200) {
      $width = 200;
      $height = floor($width / $ratio);
    }
    if ($height > 80) {
      $height = 80;
      $width = floor($height * $ratio);
    }

    $scaled_image = imagecreate($width, $height);
    imagecopyresized(
      $scaled_image,
      $source_image,
      0,
      0,
      0,
      0,
      $width,
      $height,
      $original_width,
      $original_height
    );

    for ($y = 0; $y < $height; $y++) {
      for ($x = 0; $x < $width; $x++) {
        $rgb = imagecolorat($scaled_image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $brightness = ($r + $g + $b) / 3;
        if ($brightness > 0.1) {
          $chars .= '@';
        } else {
          $chars .= '.';
        }
      }
      $chars .= "\n";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>C8</title>
  <style>
    pre {
      font-family: "Courier New", Courier, monospace;
      font-size: 8px;
      line-height: 0.8;
      letter-spacing: 0.5px;
      background-color: #f4f4f4;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 1rem;
      overflow-x: auto;
      white-space: pre;
      word-wrap: normal;
    }
  </style>
</head>

<body>
  <h1>ASCII Art Converter</h1>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="img" id="img" accept="image/*" required />
    <button>Convert</button>
  </form>
  <h2>Result</h2>
  <pre><?php echo htmlspecialchars($chars); ?></pre>
</body>

</html>