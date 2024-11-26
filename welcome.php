<!DOCTYPE html>
<html lang="en">

<head>
    <title>FRS</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="images/fav.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #1abc9c !important;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    if(!empty($_GET['logout'])) {
        session_unset();
        header('Location: login-page.php');
        exit;
    }
    ?>
    <div class="container">
        <div class="mt-5 row text-center">
            <?php
            if (!empty($_SESSION['current_user'])) {
            ?>
                <div class="col-12">
                    <img src="uploads/1234567822.jpg" class="img-thumbnail rounded-circle w-25">
                </div>
                <div class="col-12">
                    <h1 class="text-light">Welcome Naresh Kumar</h1>
                </div>
                <div class="col-12">
                    <a href="?logout=1" class="btn btn-primary">Logout</a>
                </div>
            <?php
            } else {
                ?>
                    <div class="col-12">
                        <h3 class="text-light">You are not logged in</h3>
                    </div>
                    <div class="col-12">
                        <a href="login-page.php" class="btn btn-primary">Log-In</a>
                    </div>
                <?php
            }
            ?>

        </div>
    </div>
</body>

</html>