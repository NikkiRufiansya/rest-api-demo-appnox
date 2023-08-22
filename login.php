<?php
// Import script autoload agar bisa menggunakan library
require_once('./vendor/autoload.php');
// Import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

// Cek method request apakah POST atau tidak
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'message' => 'Method Not Allowed'
    ]);
    exit();
}

// Mendapatkan data dari input HTTP POST
$json = file_get_contents('php://input');
$input = json_decode($json);


// Pastikan data yang diperlukan ada dalam input
if (!isset($input->email) || !isset($input->password)) {
    http_response_code(400);
    echo json_encode([
        'message' => 'Email dan password diperlukan'
    ]);
    exit();
}

// Cuma data mock/dummy, bisa diganti dengan data dari database
$user = [
    'email' => 'niki@rml.co.id',
    'password' => '123456'
];

// Verifikasi email dan password
if ($input->email !== $user['email'] || $input->password !== $user['password']) {
    http_response_code(401);
    echo json_encode([
        'message' => 'Email atau password tidak sesuai'
    ]);
    exit();
}

// Inisialisasi payload
$expired_time = time() + (15 * 60);
$payload = [
    'email' => $input->email,
    'exp' => $expired_time
];


$access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET'], 'HS256');
$refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET'], 'HS256');



echo json_encode([
    'status' => 'success',
    'access_token' => $access_token, 
]);



// // Encode access token
// $access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);

// // Keluarkan respons sukses
// echo json_encode([
//     'accessToken' => $access_token,
//     'expiry' => date(DATE_ISO8601, $expired_time)
// ]);

// // Ubah waktu kadaluarsa lebih lama (1 jam)
// $payload['exp'] = time() + (60 * 60);
// $refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET']);
// // Simpan refresh token di http-only cookie
// setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);
?>
