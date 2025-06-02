<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;

class App 
{
    private $container;
    private $dispatcher;

    public function __construct()
    {
        $this->initializeContainer();
        $this->initializeDispatcher();
    }

    private function initializeContainer()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . '/../config/php-di.conf.php');
        $containerBuilder->useAttributes(true);
        $this->container = $containerBuilder->build();
    }

    private function initializeDispatcher()
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(require __DIR__ . '../config/routes.php');
    }

    public function run()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $decoded_uri = rawurldecode($_SERVER['REQUEST_URI']);
        $uri = parse_url($decoded_uri, PHP_URL_PATH);

        // check authentication. If not authenticated redirect to /login
        if ($uri != '/login') {
            if (!isset($_SESSION['sessionuser']['auth']) || !$_SESSION['sessionuser']['auth']) {
                if (preg_match("/^\/api/", $uri)) {
                    header('Content-Type: application/json');
                    echo json_encode(['response' => 403, 'message' => 'Forbidden']);
                } else {
                    header('Location: /login', true, 302);
                }
                exit();
            }
        }

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
	//TODO Wenn die API angesprochen wurde, sollte die Antwort auch im JSON Format erfolgen.
	//Die Frage ist: mache ich das hier oder kann ich das im Controller abfangen?
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                echo "404 Not Found";
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                echo "405 Method Not Allowed" . PHP_EOL;
                break;
            case \FastRoute\Dispatcher::FOUND:
                $controller = $routeInfo[1];
                $parameters = $routeInfo[2];

                $this->container->call($controller, $parameters);

                break;
        }
    }
}
