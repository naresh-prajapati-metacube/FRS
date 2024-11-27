<?php
include_once('FRS.php');
$frs = new FSR;

$request = $_REQUEST;
$file = $_FILES['webcam'];
if (empty($frs->checkMobileExist($request['mobile']))) {
    echo json_encode(
        [
            'status' => false,
            'message' => 'Mobile not exists'
        ]
    );
    die;
}
$login = $frs->login($request, $file);
echo json_encode($login);
