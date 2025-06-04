<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function verifyToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    try {
        $decoded = JWT::decode($token, new Key('your_super_secret_jwt_key_here_change_this_in_production', 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$user = verifyToken();

if ($method === 'POST' && $path === '/submitassignment') {
    $input = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => true, 'message' => 'Assignment submitted', 'data' => $input]);

} elseif ($method === 'GET' && $path === '/viewassignment') {
    echo json_encode(['success' => true, 'assignments' => [
        ['id' => 1, 'title' => 'Math Assignment', 'due_date' => '2024-12-01'],
        ['id' => 2, 'title' => 'Science Project', 'due_date' => '2024-12-15']
    ]]);

} elseif ($method === 'PUT' && $path === '/editprofile') {
    $input = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => true, 'message' => 'Profile updated', 'data' => $input]);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
?>
