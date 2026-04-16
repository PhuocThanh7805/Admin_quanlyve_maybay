<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlightAdmin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/styleadmincp.css">
</head>
<body>
    <div class="admin-container">
        <?php include("modules/menu.php"); ?>

        <div class="main-panel">
            <?php include("modules/header.php"); ?>

            <div class="content-wrapper">
                <?php
                    include("config/config.php");
                    include("modules/main.php");
                ?>
            </div>
            
            <?php // include("modules/footer.php"); ?>
        </div>
    </div>
</body>
</html>