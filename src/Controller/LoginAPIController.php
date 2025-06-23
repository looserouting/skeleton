<?php

declare(strict_types=1);

namespace App\Controller;

// Annahme, dass die Namespaces basierend auf der Projektstruktur und der DI-Konfiguration korrekt sind
use App\Repository\WebUserRepository;
use DI\Attribute\Inject;
use looserouting\JwtAuth\Auth;

class LoginAPIController extends AbstractController
{
    #[Inject]
    private Auth $auth;

    #[Inject]
    private WebUserRepository $userRepository;

    public function login(): void
    {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents("php://input"), true);
        // 'mail' wird anstelle von 'username' für Konsistenz mit LoginController und WebUsersController verwendet
        $mail = $data['mail'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($mail) || empty($password)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Mail and password are required.']);
            return;
        }

        // Der ursprüngliche Controller verwendete `validateCredentials`. Wir gehen von einer ähnlichen Methode aus,
        // die bei Erfolg das Benutzerobjekt zurückgibt.
        $user = $this->userRepository->validateCredentials($mail, $password);

        if ($user) {
            // Das Beispiel für JwtAuth verwendet eine Benutzer-ID, um Tokens zu generieren.
            // Annahme: Das Benutzerobjekt hat eine getId()-Methode.
            $tokens = $this->auth->generateTokens($user->getId());
            echo json_encode([
                'access_token' => $tokens['access'],
                'refresh_token' => $tokens['refresh'],
            ]);
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function logout(): void
    {
       // TODO das Token muss in der Blacklist gespeichert werden
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Successfully logged out.']);
    }
}
