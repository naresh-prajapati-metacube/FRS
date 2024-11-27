<?php include('header.php'); ?>
<?php
session_start();
if (!empty($_GET['logout'])) {
    session_unset();
    header('Location: login-page.php');
    exit;
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">FRS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="login-page.php">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <div class="mt-5 row text-center">
        <?php
        if (!empty($_SESSION['current_user'])) {
            $user = $_SESSION['current_user'];
        ?>
            <div class="col-12">
                <img src="<?php echo $user['photo']; ?>" class="img-thumbnail rounded-circle w-25">
            </div>
            <div class="col-12">
                <h1 class="text-light">Welcome <?php echo $user['first_name'] . (empty($user['last_name']) ? '' : ' ' . $user['last_name']); ?></h1>
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