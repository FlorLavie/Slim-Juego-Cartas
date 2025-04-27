<?php

// Rutas para ENDPOINTS de usuario

use App\Controllers\AuthController; // Uso del controlador
use App\Controllers\JuegoController; 
use App\Middlewares\IsLoggedMiddleware;

//registro
$app->post('/registro', [AuthController::class, 'registro']);
//login
$app->post('/login', [AuthController::class, 'login']);
//editar
$app->put('/editarUsuario/{id}', [AuthController::class, 'editarUsuario'])
   ->add(new IsLoggedMiddleware());
// obtener usuario
$app->get('/obtenerUsuario/{id}', [AuthController::class, 'obtenerUsuario'])
        ->add(new IsLoggedMiddleware());



// juego , partidas, jugadas
$app->post('/partidas', [JuegoController::class, 'Partida'])
   ->add(new IsLoggedMiddleware());
?>