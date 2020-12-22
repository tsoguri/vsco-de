<?php
//includes header into every page
?>
<link rel="stylesheet" href="styles/sites.css" />
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,600;0,800;1,300&display=swap" rel="stylesheet">
<!--Source: (original work) Sophia Oguri-->
<a <?php if ($title=='index') echo 'href = \'indexnav.php\''; else echo 'href=\'index.php\''?>><img id = nav_icon src="images/nav.png" alt=nav_icon></a>
<!--Source: (original work) Sophia Oguri-->
<?php if ($title != 'upload'){?>
<a href='upload.php'><img id = upload_icon src="images/upload.png" alt=upload_icon></a>
<?php } ?>
