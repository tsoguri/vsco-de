<?php include("includes/init.php");
$title = 'tags';
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
    <div class="tagtitle">
        <?php
        /**Display tag keyword as heading of page */
        $tagvar = htmlspecialchars($_GET['tag_id']);
        $tagrecords = exec_sql_query($db, "SELECT * FROM tags WHERE id = $tagvar");
        foreach ($tagrecords as $tagrecord) {
            echo "<h1>\"" . htmlspecialchars($tagrecord["keyword"]) . "\"</h1>";
        }
        ?>
    </div>
    <div>
            <?php
            /**Find all images with that tag id referenced in image_tags table  */
            $imgrecords = exec_sql_query($db, "SELECT images.id, images.file_ext, images.description, image_tags.tag_id FROM image_tags LEFT JOIN images ON image_tags.image_id = images.id WHERE image_tags.tag_id=$tagvar")->fetchAll();
            if (count($imgrecords)==1){
                foreach ($imgrecords as $imgrecord) {
                    $data = array(
                        'image_id' => htmlspecialchars($imgrecord["id"])
                    );
                    echo "<a href=\"details.php?" . http_build_query($data) . "\"><img class = \"single_image\" src=\"uploads/images/" . htmlspecialchars($imgrecord["id"]) . "." . htmlspecialchars($imgrecord["file_ext"]) . "\" alt=\"" . htmlspecialchars($imgrecord["description"]) . "\"></a>";
                }
            }
            if (count($imgrecords) > 1) {
                echo "<ul class=\"gallery\">";
                foreach ($imgrecords as $imgrecord) {
                    $data = array(
                        'image_id' => htmlspecialchars($imgrecord["id"])
                    );
                    echo "<li><a href=\"details.php?" . http_build_query($data) . "\"><img class = \"gallery_images\" src=\"uploads/images/" . htmlspecialchars($imgrecord["id"]) . "." . htmlspecialchars($imgrecord["file_ext"]) . "\" alt=\"" . htmlspecialchars($imgrecord["description"]) . "\"></a></li>";
                }
                echo "</ul>";
            }
            ?>
    </div>
    <?php
    if (count($imgrecords) == 0) {
        echo '<h2 class= \'no_tag\'>No images uploaded yet under this tag. <br> Try uploading an image with this tag or adding this tag to an image!</h2>';
    }
    ?>
</body>

</html>
