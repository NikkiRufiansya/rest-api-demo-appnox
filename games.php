<?php

// Import script autoload agar bisa menggunakan library
require_once('./vendor/autoload.php');
// Import library
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit();
}

list(, $token) = explode(' ', $headers['Authorization']);

try {
    // Men-decode token. Dalam library ini juga sudah sekaligus memverfikasinya
    JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
    // Data game yang akan dikirim jika token valid
    $games = [
        [
            'title' => 'Dota 2',
            'genre' => 'Strategy',
            'images' => 'https://seeklogo.com/images/D/dota-2-logo-556BDCC022-seeklogo.com.png',
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,"
        
        ],
        [
            'title' => 'Ragnarok',
            'genre' => 'Role Playing Game',
            'images' => 'https://image.api.playstation.com/vulcan/ap/rnd/202207/1210/4xJ8XB3bi888QTLZYdl7Oi0s.png',
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,"
        
            
        ]
    ];

    $response = [
        'status' => 'success',
        'data' => $games
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
    http_response_code(401);
    exit();
}