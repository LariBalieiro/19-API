<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App2\Models\Usuario;
use Firebase\JWT\JWT;
use App\Application\Settings\SettingsInterface; // importe a interface

return function (App $app): void {
    $app->post('/api/token', function (Request $request, Response $response) use ($app): Response {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $senha = $data['senha'] ?? '';

            $usuario = \App2\Models\Usuario::where('email', $email)->first();

            if ($usuario !== null && md5($senha) === $usuario->senha) {
                $container = $app->getContainer();

                /** @var SettingsInterface $settings */
                $settings = $container->get(SettingsInterface::class);
                $secretKey = $settings->get('secretKey');

                $payload = [
                    'sub' => $usuario->id,
                    'email' => $usuario->email,
                    'iat' => time(),
                    'exp' => time() + 3600,
                ];

                $token = JWT::encode($payload, $secretKey, 'HS256');

                $response->getBody()->write(json_encode(['chave' => $token]));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'status' => 'erro',
                'mensagem' => 'Credenciais inválidas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'erro' => true,
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};
