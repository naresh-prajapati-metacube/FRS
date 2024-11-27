<?php
include_once('FRS.php');
$frs = new FSR;

$request = $_REQUEST;
$file = $_FILES['webcam'];
if ($frs->checkMobileExist($request['mobile'])) {
    echo json_encode(
        [
            'status' => false,
            'message' => 'Mobile already exists'
        ]
    );
    die;
}
$register = $frs->register($request, $file);
echo json_encode($register);
