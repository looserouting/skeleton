<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Dotenv\Dotenv;
use looserouting\JwtAuth\Auth;
use looserouting\JwtAuth\Config as JwtConfig;
use PDO;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    //TODO if database does not exist go to setup()
    PDO::class => DI\factory(function () {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $_ENV['MYSQL_HOST'] ?? 'localhost',
            $_ENV['MYSQL_DATABASE']
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $options);
    }),
    //Configure twig
    Environment::class => DI\factory(function () {
        $loader = new FilesystemLoader(__DIR__ . '/../src/View');
        $twig = new Environment($loader, [
          'debug' => true
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return $twig;
    }),

    // Configure JWT Auth
    JwtConfig::class => DI\create()
        ->constructor(
            secret: $_ENV['JWT_SECRET'],
            algo: $_ENV['JWT_ALGO'] ?? 'HS256',
            accessTokenTTL: (int)($_ENV['JWT_ACCESS_TTL'] ?? 900),
            refreshTokenTTL: (int)($_ENV['JWT_REFRESH_TTL'] ?? 604800),
            storagePath: $_ENV['JWT_STORAGE_PATH'] ?? __DIR__ . '/../storage/jwt'
        ),
    
    Auth::class => DI\create()->constructor(DI\get(JwtConfig::class)),

];