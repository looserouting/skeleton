<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

// Register Routes
// TODO CachedDispatcher
return function (RouteCollector $r) {
    $r->addRoute(['GET','POST'], '/login[?dst={referer}]', ['App\Controller\LoginController', 'login']);
    $r->addRoute(['GET','POST'], '/logout', ['App\Controller\LoginController','logout']);

    $r->addRoute('GET', '/', ['App\Controller\DashboardController','show']);
    $r->addRoute('GET', '/webusers', ['App\Controller\WebUsersController', 'list']);

    $r->addGroup('/webusers', function (RouteCollector $r) {
        $r->addRoute(['GET','POST'], '/add', ['App\Controller\WebUsersController','add']);
    });
    
    $r->addGroup('/api', function (RouteCollector $r) {
	$r->addRoute(['GET','POST'], '/webusers', ['App\Controller\UsersController','fetch']);
    });
};
