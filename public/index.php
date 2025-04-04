<?php

require __DIR__ . '/../vendor/autoload.php';


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php'; //  carga librerias necesarias para el proyecto

$app = AppFactory::create();  // crea la aplicacion
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) { //este bloque define una ruta que responde a las solicitudes GET en la raiz /.
    $response->getBody()->write("Hello world!");
    return $response;   // cuando un usuario accede a la pagina principal de la app, el servidor responde con el mensaje "hello World!". 
});

$app->run(); // inicia la aplicacion y comienza a escuchar solicitudes HTTP. Slim maneja la peticion segun las rutas definidas.


