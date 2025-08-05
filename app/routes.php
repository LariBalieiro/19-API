<?php

declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app): void {
    // Permite OPTIONS para todas as rotas (útil para CORS)
    $app->options('/{routes:.+}', function (Request $request, Response $response): Response {
        return $response;
    });

    $auth = require __DIR__ . '/routes/autenticacao.php';
    $auth($app);
    
    // Carrega o grupo de rotas de produtos
    $produtos = require __DIR__ . '/routes/produtos.php';
    $produtos($app);

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function(Request $request, Response $response) {
        $response->getBody()->write(json_encode(['error' => 'Rota não encontrada']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    });

};
?>