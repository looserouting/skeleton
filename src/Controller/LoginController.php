<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\SessionUser;
use DI\Attribute\Inject;

class LoginController extends AbstractController
{
    #[Inject]
    private SessionUser $sessionuser;

    public function login(): void
    {
        $error = array();

        // if User is already authenticated redirect to 
        if ($this->sessionuser->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        // if POST then check formular and authenticate using sessionuser->authenticate
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sessionuser->authenticate($_POST['mail'], $_POST['password']);
            if ($this->sessionuser->isAuthenticated()) {
                $this->redirect('/');
            }
            $error[] = 'Benutzername oder Kennwort falsch!';
        }
        echo $this->render('Login/login.html.twig', ['errors' => $error]);
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
