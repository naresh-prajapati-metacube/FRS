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
</head>
<?php
session_start();
if (!empty($_SESSION['current_user'])) {
    header('Location: welcome.php');
}
?>
<body>

    <div class="container-fluid bg-primary">
        <h5 class="d-inline"><a class="text-white" href="index.html">Register</a></h5>
        <h5 class="d-inline ms-4"><a class="text-white" href="login-page.php">Login</a></h5>
    </div>

    <div class="container mt-5">
        <div class="row">
            <form action="" id="frs_form" onsubmit="return false;">
                <div class="col-8 offset-2">
                    <h3 class="text-center">Login</h3>
                        <div class="row">
                            <div class="col-6 offset-3">
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" placeholder="Mobile.." name="mobile"
                                        id="mobile">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div id="my_camera"></div>
                            </div>
                            <div class="col-6">
                                <div id="results"></div>
                            </div>
                            <div class="col-12">
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