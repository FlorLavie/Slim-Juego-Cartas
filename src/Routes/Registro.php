<?php

// Ruta (endpoint) para el registro en Slim
// Cuando el usuario se registre, se ejecuta el método "registro" del RegistroController

use App\Controllers\RegistroController; // Uso del controlador

$app->post('/registro', RegistroController::class . ':registro'); 
// Cuando alguien haga un POST a "/registro"
// La app ejecuta RegistroController::registro() para procesarlo