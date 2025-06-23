<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

// Register Routes
// TODO CachedDispatcher
return function (RouteCollector $r) {
    $r->addRoute(['GET'], '/login[?dst={referer}]', ['App\Controller\LoginController', 'login']);
    $r->addRoute(['GET'], '/logout', ['App\Controller\LoginController','logout']);

    $r->addRoute('GET', '/', ['App\Controller\DashboardController','show']);
    $r->addRoute('GET', '/webusers', ['App\Controller\WebUsersController', 'list']);

    $r->addGroup('/webusers', function (RouteCollector $r) {
        $r->addRoute(['GET'], '/add', ['App\Controller\WebUsersController','add']);
    });
    
    $r->addGroup('/api', function (RouteCollector $r) {
	    $r->addRoute(['GET'], '/webusers', ['App\Controller\WebUsersController','fetch']);
        $r->addRoute(['POST'], '/login', ['App\Controller\LoginAPIController','login']);
        $r->addRoute(['POST'], '/logout', ['App\Controller\LoginAPIController','logout']);
    });
};
