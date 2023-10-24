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

// Data pengguna dalam bentuk array
$users = [
    [
        'email' => 'niki@rml.co.id',
        'password' => '123456',
    ],
    [
        'email' => 'putra@rml.co.id',
        'password' => '123456',
    ],
    [
        'email' => 'rizky@rml.co.id',
        'password' => '123456'
    ]
    // Tambahkan pengguna lain di sini
];

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

$authenticated = false;
$selectedUser = null;

// Loop melalui data pengguna dan verifikasi email dan password
foreach ($users as $user) {
    if ($input->email === $user['email'] && $input->password === $user['password']) {
        $authenticated = true;
        $selectedUser = $user;
        break;
    }
}

if (!$authenticated) {
    http_response_code(401);
    echo json_encode([
        'message' => 'Email atau password tidak sesuai'
    ]);
    exit();
}

// Inisialisasi payload
$expired_time = time() + (15 * 600000000);
$payload = [
    'email' => $selectedUser['email'],
    'exp' => $expired_time
];

$access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET'], 'HS256');
$refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET'], 'HS256');

echo json_encode([
    'status' => 'success',
    'access_token' => $access_token,
    'data' => [
        "email" => $input->email
    ]
]);
?>
