<?php include("includes/init.php");
$title = 'indexnav'; ?>
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
    <div class="index_container">
        <div class="nav_container">
            <h2> tags: </h2>
            <!-- tag menu -->
            <div class="tag_nav">
                <ul class="tags">
                    <?php
                    $records = exec_sql_query($db, "SELECT * FROM tags")->fetchAll();
                    if (count($records) > 0) {
                        foreach ($records as $record) {
                            $tagdata = array(
                                'tag_id'=> htmlspecialchars($record["id"])
                            );
                            echo "<li><a class=\"taglink\" href=\"tags.php?" . http_build_query($tagdata) . "\">" . htmlspecialchars($record["keyword"]) . "</a></li>";
                        }
                    } else {
                        echo '<p>No tags added yet. Try adding a tag!</p>';
                    }
                    ?>
                </ul>
            </div>

        </div>
        <!-- gallery layout adjusted to only show 3 pictures wide-->
        <div class="gallery_container">
            <ul class="navgallery">
                <?php
                $records = exec_sql_query($db, "SELECT * FROM images")->fetchAll();
                if (count($records) > 0) {
                    foreach ($records as $record) {
                        $imagedata = array(
                            'image_id' => htmlspecialchars($record["id"])
                        );
                        /**Source: (original work) Sophia Oguri for image id's 1-12*/
                        echo "<li><a href=\"details.php?" . http_build_query($imagedata) . "\"><img src=\"uploads/images/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" class = \"gallery_images\" alt=\"" . htmlspecialchars($record["description"]) . "\"></a></li>";
                    }
                } else {
                    echo '<p>No images uploaded yet. Try uploading a image!</p>';
                } ?>
            </ul>
        </div>
    </div>

</body>

</html>
