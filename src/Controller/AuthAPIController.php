<?php
namespace App;

use LooseRouting\JWTAuth;

class AuthAPIController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = new JWTAuth('supersecretkey'); // Setze deinen Secret Key
    }

    public function login()
    {
        // JSON-Daten lesen
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $userRepo = new UserRepository();

        if ($userRepo->validateCredentials($username, $password)) {
            $token = $this->auth->encode([
                'user' => $username,
                'exp' => time() + 3600
            ]);
            header('Content-Type: application/json');
            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid login']);
        }
    }
}
