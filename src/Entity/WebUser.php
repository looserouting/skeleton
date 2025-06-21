<?php

declare(strict_types=1);

namespace Dive\Model;

class WebUser
{
    public int $id;
    public string $username;
    public string $mail;
    public string $password;

    function __construct( int $id=0, string $username = '', string $mail = '', string $password = '') {
        $this->id = $id;
        $this->mail = $mail;
        $this->username = $username;
        $this->password = $password;
    }

    public static function fromArray(array $data): WebUser
    {
        return new WebUser(
            $data['id'],
            $data['username'],
            $data['mail'],
            $data['password']
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
