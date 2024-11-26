<?php
class FSR
{
    private $conn;
    private $apiKey = 'BApWjX5BdfPLhbJ8qjedm21jTOrbjMkt';
    private $apiSecret = 'x1n_nL7KuSzbmzhr5dg4QDSzfG33QQmT';
    public function __construct() {
        $connStr = "host=ec2-34-199-117-32.compute-1.amazonaws.com port=5432 dbname=metacafe-wallet user=metacafe password=5hdnWbgbtsmihKVqg590";
        $this->conn = pg_connect($connStr);
    }

    public function checkMobileExist($mobile) {
        $checkMobileQuery = pg_query($this->conn, "select * from redpeppers.frs_user where mobile = $mobile");
        $checkMobileResult = pg_fetch_all($checkMobileQuery);
        return !empty($checkMobileResult);
    }

    public function uploadPhoto($file, $fileName = null) {
        $filepath = 'uploads/';
        move_uploaded_file($file['tmp_name'], $filepath.$fileName);
        return $filepath.$fileName;
    }

    protected function getCurlResponse($url, $postData) {
        $post_data_string = http_build_query($postData);
        $url = $url.'?'.$post_data_string;
        $headers = [
           "Content-Type: application/json",
           "Content-Length: 0",
        ];
        
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function detectFace($photoUrl) {
        $postData = [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'image_url' => $photoUrl,
        ];
        $url = 'https://api-us.faceplusplus.com/facepp/v3/detect';
        return $this->getCurlResponse($url, $postData);
    }

    public function compareFace($savedToken, $compareToken) {
        $postData = [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'face_token1' => $savedToken,
            'face_token2' => $compareToken,
        ];
        $url = 'https://api-us.faceplusplus.com/facepp/v3/compare';
        return $this->getCurlResponse($url, $postData);
    }

    public function register($request, $file) {
        $photoName = $request['mobile'].'.jpg';
        $photo = $this->uploadPhoto($file, $photoName);
        if(!empty($request['save_data'])) {
            $date = date('Y-m-d H:i:s');
            $photoUrl = 'https://metacafe-dev-api.metacube.com/frs/'.$photo;
            $faceResponse = $this->detectFace($photoUrl);
            $face = json_decode($faceResponse);
            $imgToken = $face->faces[0]->face_token ?? null;
            if(empty($imgToken)) {
                return [
                    'status' => false,
                    'message' => 'Face did not recognize!!',
                    'data' => []
                ];
            }
            $date = date('Y-m-d H:i:s');
            $query = "INSERT INTO redpeppers.frs_user (first_name,last_name,mobile,email,photo,img_token,face_plus_detect_response,created_at) VALUES ('$request[first_name]', '$request[last_name]', '$request[mobile]', '$request[email]', '$photo', '$imgToken', '$faceResponse', '$date')";
            $insert = pg_query($this->conn, $query);
            if(empty($insert)) {
                return [
                    'status' => false,
                    'message' => 'registration failed',
                    'data' => []
                ];
            }
        }
        return [
            'status' => true,
            'message' => empty($request['save_data']) ? 'snapped successfully' : 'registration successful',
            'data' => [
                'photo' => $photo
            ]
        ];
    }

    public function login($request, $file) {
        $photoName = $request['mobile'].'-compare.jpg';
        $photo = $this->uploadPhoto($file, $photoName);
        $photoUrl = 'https://metacafe-dev-api.metacube.com/frs/'.$photo;
        $faceResponse = $this->detectFace($photoUrl);
        $face = json_decode($faceResponse);
        $imgToken = $face->faces[0]->face_token ?? null;
        if(empty($imgToken)) {
            return [
                'status' => false,
                'message' => 'Face did not recognize!!',
                'data' => []
            ];
        }

        if(!empty($request['save_data'])) {
            $mobile = $request['mobile'];
            $savedImgTokenQuery = pg_query($this->conn, "select * from redpeppers.frs_user where mobile = $mobile");
            $savedImgTokenResult = pg_fetch_assoc($savedImgTokenQuery);
            $savedImgToken = $savedImgTokenResult['img_token'] ?? null;
            if(empty($savedImgToken)) {
                return [
                    'status' => false,
                    'message' => 'Token not found!!',
                    'data' => []
                ];
            }
            $compareFace = $this->compareFace($savedImgToken, $imgToken);
            $faceMatch = json_decode($compareFace);
            $confidence = $faceMatch->confidence ?? 'null';
            $query = "UPDATE redpeppers.frs_user SET face_plus_compare_response = '$compareFace', last_compare_confidence = $confidence where mobile = '$mobile'";
            $update = pg_query($this->conn, $query);
            if(!empty($faceMatch->error_message)) {
                return [
                    'status' => false,
                    'message' => $faceMatch->error_message,
                    'data' => []
                ];
            } else if(empty($faceMatch->confidence) || $faceMatch->confidence < 90) {
                return [
                    'status' => false,
                    'message' => 'Unauthorized!!',
                    'data' => []
                ];
            } else if(empty($update)) {
                return [
                    'status' => false,
                    'message' => 'login failed',
                    'data' => []
                ];
            }

            session_start();
            $_SESSION['current_user'] = $savedImgTokenResult;
        }
        return [
            'status' => true,
            'message' => empty($request['save_data']) ? 'snapped successfully' : 'logged-in successful',
            'data' => [
                'photo' => $photo
            ]
        ];
    }
}