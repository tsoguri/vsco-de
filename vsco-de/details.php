<?php include("includes/init.php");
$title = 'details';
$deleteimage = FALSE;
$edittag = FALSE;
$deletetag = FALSE;
$addtag = FALSE;
$alreadytagged = FALSE;
$editFeedback = '';

/** GET request from query string parameters OR from hidden input from the edit tag GET form */
$imagevar = htmlspecialchars($_GET['image_id']);

/** Is there is a POST request from form delete_image*/
if (isset($_POST["delete_image"])) {
    $deleteimage = TRUE;
}

/** Is there a GET request from form edit_tag*/
if (isset($_GET["edit_tag"])) {
    $edittag = TRUE;
}

/** Find all tag records associated to the specific image id*/
if (isset($_POST["tag_keyword"])) {
    $delete_keyword = htmlspecialchars($_POST["tag_keyword"]);
    $deletetagrecords = exec_sql_query($db, "SELECT tags.id, tags.keyword FROM image_tags LEFT JOIN tags ON image_tags.tag_id = tags.id WHERE image_tags.image_id = $imagevar")->fetchAll(PDO::FETCH_ASSOC);
    if (count($deletetagrecords) > 0) {
        foreach ($deletetagrecords as $deletetagrecord) {
            /**Determine if there is a POST request from a delete_tag form named after the tag keyword */
            if ($delete_keyword == htmlspecialchars($deletetagrecord["keyword"])) {
                $deletetag = TRUE;
                $deleted_tag = htmlspecialchars($deletetagrecord["keyword"]);
                $deleted_tagid = htmlspecialchars($deletetagrecord["id"]);

                /**Return successful form feedback */
                $editFeedback = "You have deleted tag \"" . htmlspecialchars($deletetagrecord["keyword"]) . "\"";
                $deletefromimage_tags = exec_sql_query($db, "DELETE FROM image_tags WHERE image_id = $imagevar AND tag_id=$deleted_tagid");

                /*Determine if that tag was unique, if so, delete the tag record from tags table*/
                $find_others = exec_sql_query($db, "SELECT * FROM image_tags WHERE tag_id=$deleted_tagid")->fetchAll(PDO::FETCH_ASSOC);
                if (count($find_others) == 0) {
                    $deletefromtags = exec_sql_query($db, "DELETE FROM tags WHERE id=$deleted_tagid");
                }
            }
        }
    }
}

/**Determine if add_tag POST form was sent and if the add tag value is not empty */
if (isset($_POST["add_tag"]) && !empty($_POST["add_tag"])) {
    $addtag = TRUE;
    $new_tag = filter_input(INPUT_POST, 'add_tag', FILTER_SANITIZE_STRING);
    $new_tag = trim($new_tag);
    $new_tag = strtolower($new_tag);
    $new_tag = str_replace(' ', '', $new_tag);

    /**Find all tags */
    $addtagrecords = exec_sql_query($db, "SELECT * FROM tags")->fetchAll();
    if (count($addtagrecords) > 0) {
        foreach ($addtagrecords as $addtagrecord) {
            /**Determine if the added tag already exists in the tag table */
            if ($addtagrecord["keyword"] == htmlspecialchars($new_tag)) {
                $tagexists = TRUE;
                $add_tagid = htmlspecialchars($addtagrecord["id"]);

                /**Determine if the added tag is already a tag on the image */
                $findtag = exec_sql_query($db, "SELECT * FROM image_tags WHERE image_id = $imagevar AND tag_id = $add_tagid")->fetchAll(PDO::FETCH_ASSOC);
                if (count($findtag) > 0) {
                    $alreadytagged = TRUE;
                }
            }
        }
    }
    /**While the image has not been tagged with that specific tag yet */
    if ($alreadytagged != TRUE) {
        /**Return successful form feedback */
        $editFeedback = "You have added tag \"" . htmlspecialchars($new_tag) . "\"";

        /**If there is not tag of that name, make a new tag */
        if ($tagexists == FALSE) {
            $addtagsql = "INSERT INTO tags(keyword) VALUES (:tag)";
            $addtagparams = array(
                ':tag' => htmlspecialchars($new_tag)
            );
            $tag_insert = exec_sql_query($db, $addtagsql, $addtagparams)->fetchAll(PDO::FETCH_ASSOC);
            $add_tagid = $db->lastInsertId("id");
        }

        /**Make a reference in image_tags table to the image id and tag id */
        $sql = "INSERT INTO image_tags(image_id, tag_id) VALUES (:image_id, :tag_id)";
        $params = array(
            ':image_id' => htmlspecialchars($imagevar),
            ':tag_id' => htmlspecialchars($add_tagid)
        );
        $add = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    } else {
        /**Return form feedback if added tag was already a tag on the image */
        $editFeedback = "It looks like \"" . htmlspecialchars($new_tag) . "\" is already a tag!";
    }
}
/** Return form feedback if added tag value was empty */
if (isset($_POST["add_tag"]) && empty($_POST["add_tag"])) {
    $addtag = TRUE;
    $editFeedback = "Please enter a word as your tag.";
}

$actionvalue = "details.php?image_id=" . $imagevar;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>SNAP - Image</title>
    <link rel="stylesheet" href="styles/sites.css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,600;0,800;1,300&display=swap" rel="stylesheet">
</head>

<body>
    <header><?php include("includes/header.php"); ?></header>
    <div>
        <?php
        if ($editFeedback != '') {
            echo "<h2 class=\"delete_heading\">" . $editFeedback . "</h2>";
        }
        /** Show image details if the image wasn't deleted */
        if (!$deleteimage) {
            $records = exec_sql_query($db, "SELECT * FROM images WHERE id = $imagevar")->fetchAll(PDO::FETCH_ASSOC);
            if (count($records) > 0) {
                foreach ($records as $record) {
                    /**Change formatting of image to include form feedback for editing the image details, if a tag was added or deleted*/
                    if ($deletetag or $addtag) {
                        /**Source: (original work) Sophia Oguri for image id's 1-12*/
                        echo "<img src=\"uploads/images/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" class = \"image_edittag\" alt=\"" . htmlspecialchars($record["description"]) . "\">";
                    } else {
                        echo "<img src=\"uploads/images/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" class = \"image\" alt=\"" . htmlspecialchars($record["description"]) . "\">";
                    }
                }
            } else {
                echo '<p>Uh oh, we couldn\'t find the image you were looking for!</p>';
            }
        ?>
    </div>
    <div class="image_description">
        <?php
            /**Show image details */
            $imagerecords = exec_sql_query($db, "SELECT * FROM images WHERE id = $imagevar")->fetchAll(PDO::FETCH_ASSOC);
            if (count($imagerecords) > 0) {
                foreach ($imagerecords as $imagerecord) {
                    echo "<p>" . htmlspecialchars($imagerecord["description"]) . "</p>";
                    echo "<p>" . htmlspecialchars($imagerecord["date"]) . "</p>";
                }
            } else {
                echo '<p>Uh oh, we couldn\'t find the information you were looking for!</p>';
            }

            /**Show tag details */
            $tagrecords = exec_sql_query($db, "SELECT tags.id, tags.keyword FROM image_tags LEFT JOIN tags ON image_tags.tag_id = tags.id WHERE image_tags.image_id = $imagevar")->fetchAll(PDO::FETCH_ASSOC);
            if (count($tagrecords) > 0) {
                echo "<p class=\"tagdetails\">";
                foreach ($tagrecords as $tagrecord) {
                    /**Create array for query parameters used in tag links*/
                    $tagdata = array(
                        'tag_id' => htmlspecialchars($tagrecord["id"])
                    );
                    /**If the edit tag button was clicked, display delete buttons on each tag, with a specific POST form for each tag with the name set as the specific tag keyword
                     * note: don't need a hidden input for image id because the image_id is in the action attribute but, need a hidden input for keyword name to make sure the right tag is deleted
                     */
                    if ($edittag) {
                        /**Source: (original work) Sophia Oguri*/
                        echo "<form class= \"delete_tag\" name=\"delete_tag\" action=\"" . $actionvalue . "\" method=\"post\"><a class = \"tagdetailslink\" href = \"tags.php?" . http_build_query($tagdata) . "\">#" . htmlspecialchars($tagrecord["keyword"]) . "</a>
                        <input type=\"hidden\" name = \"tag_keyword\" value =\"" . htmlspecialchars($tagrecord["keyword"]) . "\">
                        <button class=\"icon_btn\" type=\"submit\" name=\"" . htmlspecialchars($tagrecord["keyword"]) . "\">
                        <img class=\"tag_icon\" src=\"images/trash.png\" alt=\"Trash Can\">
                        </button></form>";
                    }
                    /**If not clicked, display tags and tag links as normal */
                    else {
                        echo "<a class = \"tagdetailslink\" href = \"tags.php?" . http_build_query($tagdata) . "\">#" . htmlspecialchars($tagrecord["keyword"]) . "</a> ";
                    }
                }
            } else {
                echo '<p>Uh oh, we couldn\'t find any tags! Add some tags</p>';
            }
            /**If edit tag button was clicked, display input for a tag with a specific POST request
             * note: don't need a hidden input because the image_id is in the action attribute
             */
            if ($edittag) {
                echo "<form class=\"delete_tag\" name=\"add_tag\" action=\"" . $actionvalue . "\" method=\"post\">#<input class=\"add_tag\" type=\"text\" name=\"add_tag\" placeholder = \"add tag\"/></form>";
            }
        ?>
    </div>
    <div class="edit_image">
        <!--POST form to delete the image
            note: don't need a hidden input because the image_id is in the action attribute-->
        <form class="edit_form" name="delete" action="<?php echo $actionvalue ?>" method="post">
            <button class="icon_btn" type="submit" name="delete_image">
                <!--Source: (original work) Sophia Oguri-->
                <img class="icon" src="images/trash.png" alt="Trash Can"> </button>
            <label class="icon_caption">Delete Image</label>
        </form>
        <!--GET form to display the editing options with a hidden input indicating the image_id
            note: need hidden inputs to get image id, it is applicable to use a GET request and a form in this case, because there is a user input.
            If I were to instead use a link with http_build_query, I would have had to add a edit=YES query parameter, which I did not want to do.  -->
        <form class="edit_form" name="edit" action="<?php echo $actionvalue ?>" method="get">
            <input type="hidden" name="image_id" value="<?php echo $imagevar ?>">
            <button class="icon_btn" type="submit" name="edit_tag">
                <!--Source: (original work) Sophia Oguri-->
                <img class="icon" src="images/tag.png" alt="Tag"></button>
            <label class="icon_caption">Edit</label>
        </form>
    </div>
<?php }
        /**Delete image*/
        else {
            echo "<h1 class = 'delete_heading'> Sorry you didn't like that image! <br>If you would like, feel free to <a id = \"delete_upload\" href = \"upload.php\" >upload</a> a new photo!</h1>";
            $findimage = exec_sql_query($db, "SELECT * FROM images WHERE id = $imagevar")->fetchAll(PDO::FETCH_ASSOC);
            /**Delete image from database */
            $deleteimage = exec_sql_query($db, "DELETE FROM images WHERE id=$imagevar")->fetchAll(PDO::FETCH_ASSOC);
            /**Delete image from server */
            unlink("uploads/images/" . htmlspecialchars($findimage[0]["id"]) . "." . htmlspecialchars($findimage[0]["file_ext"]));
            /**Delete image references in image-tags*/
            $deletefromimage_tags = exec_sql_query($db, "DELETE FROM image_tags WHERE image_id=$imagevar")->fetchAll(PDO::FETCH_ASSOC);
        }
?>
    </div>
</body>

</html>
