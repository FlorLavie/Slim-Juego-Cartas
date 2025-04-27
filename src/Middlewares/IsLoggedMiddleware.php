<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class IsLoggedMiddleware implements MiddlewareInterface
{
    public static $secret = "miclavesecreta"; // tu clave secreta

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            if ($request->hasHeader("Authorization")) {
                $token = $request->getHeaderLine("Authorization");
                if (!empty($token)) {
                    $key = new Key(self::$secret, "HS256");
                    $dataToken = JWT::decode($token, $key);
                    
                    $now = (new \DateTime("now"))->format("Y-m-d H:i:s");

                    if ($dataToken->expired_at < $now) {
                        $response = new \Slim\Psr7\Response();
                        $response->getBody()->write(json_encode(["error" => 'Token vencido']));
                        return $response->withHeader("Content-Type", "application/json")->withStatus(401);
                    } else {
                        $request = $request->withAttribute('usuario', $dataToken->usuario);
                        $response = $handler->handle($request);
                        return $response;
                    }
                }
            }

            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["error" => 'Acción requiere login']));
            return $response->withHeader("Content-Type", "application/json")->withStatus(401);

        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader("Content-Type", "application/json")->withStatus(500);
        }
    }
}