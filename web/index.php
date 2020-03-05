<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ImageShare</title>
    <meta name="viewport" content="initial-scale=1">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <div class="upload-form">
        <p>This tool allows easy transfer of images to another device using QR codes. It uses the <a href="http://imgbb.com" target="_blank">Imgbb API</a>, and images are automatically deleted after five minutes.</p>
        <form action="index.php" enctype="multipart/form-data" method="POST">
            <input name="img" size="35" type="file" />
            <input name="submit" type="submit" value="Upload" />
        </form>
    </div>
    <?php
    if(isset($_POST['submit'])){
      
      // Convert image to base64

      $img=$_FILES['img'];
      $filename = $img['tmp_name'];
      $handle = fopen($filename, "r");
      $data = fread($handle, filesize($filename));
      $base64 = base64_encode($data);

      // Set post fields

      $post = [
        'key' => getenv('API_KEY'),
        'image' => $base64,
      ];
      $ch = curl_init('https://api.imgbb.com/1/upload');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

      // Upload image

      $output = curl_exec($ch);
      curl_close($ch);

      // Print QR code
      $pms = json_decode($output,true);
      $imgurl = $pms['data']['url'];
      $img = '
        <div class="result" align="center">
          <img src="http://chart.googleapis.com/chart?chs=300x300&cht=qr&chld=L|0&chl='.$imgurl.'">
          <p>Scan this code with a QR code reader on another device. The camera apps on some iOS and Android devices can scan QR codes.</p>
        </div>';
      echo $img;
      
    }
  ?>

  <script>
  // Scroll to bottom of page (for dual-screen devices)
  window.scrollTo(0,document.body.scrollHeight);
  </script>

</body>