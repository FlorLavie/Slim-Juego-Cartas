<?php

require __DIR__ . '/../vendor/autoload.php'; // carga librerias necesarias para el proyecto


use Slim\Factory\AppFactory;

$app = AppFactory::create();  // crea la aplicacion
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Rutas  
require __DIR__ . '\..\src\Routes\Registro.php';  // Ruta de registro



// inicia la aplicacion y comienza a escuchar solicitudes HTTP. Slim maneja la peticion segun las rutas definidas.
$app->run(); 
