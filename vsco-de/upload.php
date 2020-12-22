<?php include("includes/init.php");
$title = 'upload';
const MAX_FILE_SIZE = 1000000;
$description = '';
$file_name = '';
$file_ext = '';
$date = '';
$tag = '';
$tagexists = FALSE;
$validForm = FALSE;
$descriptionFeedback = FALSE;
$dateFeedback = FALSE;
$tagFeedback = FALSE;
$formFeedback = '';

/**If upload form is submitted */
if (isset($_POST["submitform"])) {
    /**Give feedback when inputs are empty */
    if (empty($_POST["description"])) {
        $descriptionFeedback = TRUE;
    } else {
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $description = trim($description);
    }

    if (empty($_POST["date"])) {
        $dateFeedback = TRUE;
    } else {
        /** Don't need to filter date value bc date type in form already formats and sanitizes it! */
        $date = $_POST['date'];
    }
    if (empty($_POST['image_tag'])) {
        $tagFeedback = TRUE;
    } else {
        $tag = filter_input(INPUT_POST, 'image_tag', FILTER_SANITIZE_STRING);
        $tag = trim($tag);
        $tag = strtolower($tag);
        $tag = str_replace(' ', '', $tag);
    }

    /**If upload form is submitted with no errors esp in the file upload input */
    $upload_info = $_FILES["image_file"];
    if (!$descriptionFeedback and !$dateFeedback and !$tagFeedback) {
        if ($upload_info['error'] == UPLOAD_ERR_OK) {
            $title='uploaded';
            $validForm = TRUE;
            $file_name = basename($upload_info['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            /**Insert image details into images table - note: these values cannot be empty!  */
            $imagesql = "INSERT INTO images(file_name, file_ext, description, date) VALUES (:file_name, :file_ext, :description, :date)";
            $imageparams = array(
                ':file_name' => htmlspecialchars($file_name),
                ':file_ext' => htmlspecialchars($file_ext),
                ':description' => htmlspecialchars($description),
                ':date' => htmlspecialchars($date)
            );

            /**Redirect image path from location in server to specific folder with specific name */
            $files = exec_sql_query($db, $imagesql, $imageparams)->fetchAll(PDO::FETCH_ASSOC);
            $image_id = $db->lastInsertId("id");
            $new_path = "uploads/images/$image_id.$file_ext";
            move_uploaded_file($_FILES["image_file"]["tmp_name"], $new_path);

            /**Determine if added tag already exists */
            $tagrecords = exec_sql_query($db, "SELECT * FROM tags")->fetchAll();
            if (count($tagrecords) > 0) {
                foreach ($tagrecords as $tagrecord) {
                    if ($tagrecord["keyword"] == htmlspecialchars($tag)) {
                        $tagexists = TRUE;
                        $tag_id = htmlspecialchars($tagrecord["id"]);
                    }
                }
            }
            /**Add tag into tag table only if tag doesn't currently exist */
            if ($tagexists == FALSE) {
                $tagsql = "INSERT INTO tags(keyword) VALUES (:tag)";
                $tagparams = array(
                    ':tag' => htmlspecialchars($tag)
                );
                $tag_insert = exec_sql_query($db, $tagsql, $tagparams)->fetchAll(PDO::FETCH_ASSOC);
                $tag_id = $db->lastInsertId("id");
            }
            /**Add reference to tag and to image in image_tags table */
            $sql = "INSERT INTO image_tags(image_id, tag_id) VALUES (:image_id, :tag_id)";
            $params = array(
                ':image_id' => htmlspecialchars($image_id),
                ':tag_id' => htmlspecialchars($tag_id)
            );
            $add = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
        } else if ($upload_info['error'] == UPLOAD_ERR_FORM_SIZE) {
            $formFeedback = 'Please only upload images under 1MB.';
        } else {
            $formFeedback = 'There was an error in the image upload. Please try again.';
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>SNAP - Upload</title>
    <link rel="stylesheet" href="styles/sites.css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,600;0,800;1,300&display=swap" rel="stylesheet">
</head>

<body>
    <header><?php include("includes/header.php"); ?></header>

    <h1 class=page_heading> UPLOAD </h1>
    <?php if ($validForm) { ?>
        <p id='thanks'> Thank you for your submission! </p>
        <?php
        /**Display submitted image */
        $records = exec_sql_query($db, "SELECT * FROM images WHERE id = $image_id")->fetchAll(PDO::FETCH_ASSOC);
        if (count($records) > 0) {
            foreach ($records as $record) {
                $data = array(
                    'image_id' => $record["id"]
                );
                /**Source: (original work) Sophia Oguri for image id's 1-12*/
                echo "<a href=\"details.php?" . http_build_query($data) . "\"><img src=\"uploads/images/" . htmlspecialchars($record["id"]) . "." . htmlspecialchars($record["file_ext"]) . "\" class = \"image_submission\" alt=\"" . htmlspecialchars($record["description"]) . "\"></a>";
            }
        }
    } else { ?>
        <p class="form_feedback"> <?php echo $formFeedback ?></p>
        <div class="upload">
            <form id="upload_form" action="upload.php" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE ?>" />

                <?php if ($tagFeedback or $dateFeedback or $descriptionFeedback) {echo "<p class=\"form_feedback\">Please re-upload your image with all of the inputs.</p>";}?>
                <div class="input">
                    <label class="upload_label" for="upload_button">Upload File:</label>
                    <label for="upload_button" class="upload_file">
                        <!--Source: (original work) Sophia Oguri-->
                        <img src="images/img.png" id="img_icon" alt="image icon">
                        <input id="upload_button" type="file" name="image_file" accept="image/*"/>
                    </label>
                </div>
                <?php if ($tagFeedback){ echo "<p class=\"form_feedback\">Please add a keyword to tag this image with.</p>";}?>
                <div class="input">
                    <label class="upload_label" for="image_tag">Tag:</label>
                    <input id = "image_tag" class="upload_input" type="text" name="image_tag" value="<?php echo htmlspecialchars($tag); ?>" />
                </div>

                <?php if ($descriptionFeedback){ echo "<p class=\"form_feedback\">Please add a description for this image.</p>";}?>
                <div class="input">
                    <label class="upload_label" for="image_description">Description:</label>
                    <input id = "image_description" class="upload_input" type="text" name="description" value="<?php echo htmlspecialchars($description); ?>" />
                </div>

                <?php if ($dateFeedback){ echo "<p class=\"form_feedback\">Please add the date you took this photo.</p>";}?>
                <div class="input">
                    <label class="upload_label" for="image_date">Date Taken:</label>
                    <input id = "image_date" class="upload_input" type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" />
                </div>

                <div class="input_submit">
                    <!--Source: (original work) Sophia Oguri-->
                    <img src="images/upload.png" id="go_icon" alt="upload_icon"/>
                    <button name="submitform" id="go" type="submit">Upload</button>
                </div>
            </form>
        </div>
    <?php } ?>

</body>

</html>
