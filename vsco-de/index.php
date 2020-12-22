<?php include("includes/init.php");
$title = 'index';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>SNAP</title>
  <link rel="stylesheet" href="styles/sites.css" />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,600;0,800;1,300&display=swap" rel="stylesheet">
</head>

<body>
  <header><?php include("includes/header.php"); ?></header>

  <!-- gallery layout -->
  <div>
    <ul class="gallery">
      <?php
      $records = exec_sql_query($db, "SELECT * FROM images")->fetchAll();
      if (count($records) > 0) {
        foreach ($records as $record) {
          $data = array(
            'image_id'=> htmlspecialchars($record["id"])
          );
          /**Source: (original work) Sophia Oguri for image id's 1-12*/
          echo "<li><a href=\"details.php?".http_build_query($data)."\"><img src=\"uploads/images/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" class = \"gallery_images\" alt=\"" . htmlspecialchars($record["description"]) . "\"></a></li>";
        }
      } else {
        echo '<p>No images uploaded yet. Try uploading a image!</p>';
      } ?>
    </ul>
  </div>
</body>

</html>
