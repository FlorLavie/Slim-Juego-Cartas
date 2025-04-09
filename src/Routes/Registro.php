<?php

// Ruta (endpoint) para el registro en Slim
// Cuando alguien haga un post a /registro, se ejecuta el método "registro" del RegistroController

use App\Controllers\RegistroController; // Uso del controlador

$app->post('/registro', [RegistroController::class, 'registro']);
$app->post('/login', [RegistroController::class, 'login']);


?>