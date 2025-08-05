<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Middleware\JwtMiddleware;
use Slim\App;

return function (App $app) {
    // Se você usa container, certifique-se que ele está acessível aqui.
    // Por exemplo, se tem global $container ou usa $app->getContainer()
    // Vou assumir que você tem acesso ao container via $app->getContainer()
    $container = $app->getContainer();

    $secret = $container->get(\App\Application\Settings\SettingsInterface::class)->get('secretKey');

    // Adiciona middleware JWT customizado
    $app->add(new JwtMiddleware($secret));

    // Adiciona middleware de roteamento (ESSENCIAL)
    $app->addRoutingMiddleware();

    // Adiciona middleware de sessão (caso necessário)
    $app->add(SessionMiddleware::class);

    // Middleware CORS
    $app->add(function ($req, $handler) {
        $response = $handler->handle($req);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    // Middleware de erros (opcional mas recomendado)
    $app->addErrorMiddleware(true, true, true);
};