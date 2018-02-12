<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Image upload and generate thumbnail using ajax in PHP</title>
<link href="./css/style.css" rel="stylesheet">
<script src="./js/jquery.min.js"></script>
<script src="./js/jquery.form.js"></script>

</head>

<body>

<br>
<div id="imgContainer">
  <form enctype="multipart/form-data" action="image_upload_demo_submit.php" method="post" name="image_upload_form" id="image_upload_form">
    <div id="imgArea"><img src="./img/default.jpg">
      <div class="progressBar">
        <div class="bar"></div>
        <div class="percent">0%</div>
      </div>
      <div id="imgChange"><span>Change Photo</span>
        <input type="file" accept="image/*" name="image_upload_file" id="image_upload_file">
      </div>
    </div>
  </form>
</div>

<?php
include('./functions.php');
/*defined settings - start*/
ini_set("memory_limit", "99M");
ini_set('post_max_size', '20M');
ini_set('max_execution_time', 600);
define('IMAGE_SMALL_DIR', './uploades/small/');
define('IMAGE_SMALL_SIZE', 50);
define('IMAGE_MEDIUM_DIR', './uploades/medium/');
define('IMAGE_MEDIUM_SIZE', 250);
/*defined settings - end*/

if (isset($_FILES['image_upload_file'])) {
    $output['status'] = FALSE;
    set_time_limit(0);
    $allowedImageType = array(
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/png",
        "image/x-png"
    );
    
    if ($_FILES['image_upload_file']["error"] > 0) {
        $output['error'] = "Error in File";
    } elseif (!in_array($_FILES['image_upload_file']["type"], $allowedImageType)) {
        $output['error'] = "You can only upload JPG, PNG and GIF file";
    } elseif (round($_FILES['image_upload_file']["size"] / 1024) > 4096) {
        $output['error'] = "You can upload file size up to 4 MB";
    } else {
        /*create directory with 777 permission if not exist - start*/
        createDir(IMAGE_SMALL_DIR);
        createDir(IMAGE_MEDIUM_DIR);
        /*create directory with 777 permission if not exist - end*/
        $path[0]     = $_FILES['image_upload_file']['tmp_name'];
        $file        = pathinfo($_FILES['image_upload_file']['name']);
        $fileType    = $file["extension"];
        $desiredExt  = 'jpg';
        $fileNameNew = rand(333, 999) . time() . ".$desiredExt";
        $path[1]     = IMAGE_MEDIUM_DIR . $fileNameNew;
        $path[2]     = IMAGE_SMALL_DIR . $fileNameNew;
        
        if (createThumb($path[0], $path[1], $fileType, IMAGE_MEDIUM_SIZE, IMAGE_MEDIUM_SIZE, IMAGE_MEDIUM_SIZE)) {
            
            if (createThumb($path[1], $path[2], "$desiredExt", IMAGE_SMALL_SIZE, IMAGE_SMALL_SIZE, IMAGE_SMALL_SIZE)) {
                $output['status']       = TRUE;
                $output['image_medium'] = $path[1];
                $output['image_small']  = $path[2];
            }
        }
    }
    echo json_encode($output);
}
?>

</body>
</html>
