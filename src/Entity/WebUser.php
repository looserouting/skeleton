<?php

declare(strict_types=1);

namespace App\Entity;

class WebUser
{
    public int $id;
    public string $username;
    public string $mail;
    public string $password;

    function __construct(string $username = '', string $mail = '', string $password = '', int $id = 0) {
        $this->id = $id;
        $this->mail = $mail;
        $this->username = $username;
        $this->password = $password;
    }

    public static function fromArray(array $data): WebUser
    {
        return new WebUser(
            $data['username'],
            $data['mail'],
            $data['password'],
            $data['id'] ?? 0
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'mail' => $this->mail,
            'password' => $this->password
        ];
    }
}
