<?php include('header.php'); ?>
<?php
session_start();
if (!empty($_SESSION['current_user'])) {
    header('Location: welcome.php');
}
?>
<div class="login-container">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">FRS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <a class="nav-link"href="index.php">Register</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="login-page.php">Login</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
        <div class="login-form row">
            <form action="" id="frs_form" onsubmit="return false;">
                <div class="col-12">
                    <!-- <h5 class="text-left">Login</h5> -->
                        <div class="row">
                            <div class="col-6 text-left">
                                <div class="form-group">
                                    <label for="mobile">Enter mobile</label>
                                    <input type="text" class="input-field" placeholder="Mobile.." name="mobile"
                                        id="mobile">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div id="my_camera" class="border"></div>
                            </div>
                            <div class="col-6">
                                <div id="results" class="border">
                                    <img src="images/defaul-img.jpg" class="w-100" style="height: 350px;">
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <input type="hidden" name="save_data" id="save_data" value="1">
                                <button type="button" id="show_photo_btn" class="btn btn-secondary" onClick="show_photo()">Show Photo</button>
                                <button type="button" id="sub_btn" class="btn btn-success" onClick="login()">Submit</button>
                            </div>
                        </div>
                </div>
            </form>
        </div>
</div>
</body>
<script type="text/javascript" src="scripts/webcam.js"></script>

<!-- Configure a few settings and attach camera -->
<script language="JavaScript">
    Webcam.set({
        width: 600,
        height: 460,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
</script>
<script language="JavaScript">
    function show_photo() {
        $('#save_data').val(0);
        return take_snapshot();
    }
    function login() {
        $('#save_data').val(1);
        return take_snapshot();
    }
    function take_snapshot() {
        if($('#mobile').val() == '') {
            alert('Mobile is required');
            return;
        }
        // take snapshot and get image data
        $('#show_photo_btn').attr('disabled','');
        $('#sub_btn').attr('disabled','');
        $('#sub_btn').html('Please wait...');
        Webcam.snap(function (data_uri) {
            Webcam.upload(data_uri, 'login.php', function (code, text) {
                console.log(text);
                
                $('#show_photo_btn').removeAttr('disabled');
                $('#sub_btn').removeAttr('disabled');
                $('#sub_btn').html('Submit');
                textObj = JSON.parse(text);
                if(textObj.status) {
                    if($('#save_data').val() == 1) {
                        location.href = 'welcome.php';
                    } else {
                        document.getElementById('results').innerHTML = 
                        '<img class="img-fluid" src="'+textObj.data.photo+'?var='+(new Date().getTime())+'"/>';
                    }
                }
                alert(textObj.message);
            }, 'frs_form');
        });
    }
</script>

</html>