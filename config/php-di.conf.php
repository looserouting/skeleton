<?php

require_once(__DIR__ . "/../config/PDO.conf.php");

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
//use Psr\Container\ContainerInterface;

return [
    'db.dns'    => PDO_DNS,
    'db.user'   => PDO_USER,
    'db.passwd' => PDO_PASSWD,
    'db.options'=> PDO_OPTIONS,

    //TODO if database does not exist go to setup()
    PDO::class => DI\create()
      ->constructor(DI\get('db.dns'), DI\get('db.user'), DI\get('db.passwd'), DI\get('db.options')),

    //Configure twig
    Environment::class => DI\factory(function () {
        $loader = new FilesystemLoader(__DIR__ . '/../src/View');
        $twig = new Environment($loader, [
          'debug' => true
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return $twig;
    })

];
