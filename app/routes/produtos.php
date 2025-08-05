<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App2\Models\Produto;

return function (App $app): void {
    $app->group('/api/v1', function ($group) {
        $group->get('/produtos/lista', function (Request $request, Response $response) {
            $produtos = Produto::get();

            $response->getBody()->write(json_encode($produtos));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/api/v1', function ($group) {
        $group->post('/produtos/adiciona', function (Request $request, Response $response) {
            $dados = $request->getParsedBody();
            $produto = Produto::create($dados);

            $response->getBody()->write(json_encode($produto));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/api/v1', function ($group) {
        $group->get('/produtos/lista/{id}', function (Request $request, Response $response, array $args) {
            $id = (int)$args['id'];
            $produto = Produto::find($id);

            if (!$produto) {
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(['erro' => 'Produto não encontrado']));
            }

            $response->getBody()->write(json_encode($produto));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/api/v1', function ($group) {
        $group->put('/produtos/atualiza/{id}', function (Request $request, Response $response, array $args) {
            $id = (int)$args['id'];
            $dados = $request->getParsedBody();

            $produto = Produto::find($id);

            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $produto->update($dados);

            $response->getBody()->write(json_encode($produto));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $app->group('/api/v1', function ($group) {
        $group->delete('/produtos/deletar/{id}', function (Request $request, Response $response, array $args) {
            $id = (int)$args['id'];
            $produto = Produto::find($id);

            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $produto->delete();

            $response->getBody()->write(json_encode(['mensagem' => 'Produto deletado com sucesso']));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });
};