<?php

//El controlador recibe la peticion (registro)
// usa el modelo para obtener info y devuelve una respuesta


namespace App\Controllers;

use App\Models\Usuario;
 
use Psr\Http\Message\ResponseInterface as Response; // para acceder a datos que manda el cliente. ej postman
use Psr\Http\Message\ServerRequestInterface as Request;// para enviar la respuesta al cliente

class RegistroController
{
    public function registro(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody(); // obtenemos datos que envio el cliente

        $nombre = $datos['nombre'] ?? ''; //guardamos datos
        $password= $datos['password'] ?? '';

        // hacer Validaciones ! van aca? no son validaciones de bases de datos
        // Nombre entre 6 y 20 caracteres y solo alfanumericos . ctype_alnum verifica que no tenga caracteres especiales

        if (strlen($nombre) < 6 || strlen($nombre) > 20 || !ctype_alnum($nombre)) {
            return $this->json($response, [
                'error' => 'El nombre de usuario debe tener entre 6 y 20 caracteres y solo contener letras y números.'
            ], 400);
        }

        // Que el nombre no este en uso
          // Instanciar el modelo `Usuario` y verificar si el nombre ya existe
        $usuario = new Usuario();
         if ($usuario->existeNombre($nombre)) {
             return $this->json($response, ['error' => 'Ese nombre de usuario ya está en uso.'], 409);
         }

        // Password por lo menos 8 caracteres. contener caracteres mayúsculas, minúsculas, números y caracteres especiales.      

   
        

        // Registrar usuario
        $okRegistro = $usuario->registrar($nombre,  $password);

        if (!$okRegistro) {
            return $this->json($response, ['error' => 'Error al registrar.'], 500);
        }

        return $this->json($response, ['mensaje' => 'Usuario registrado con éxito.'], 201);
    }

    // Función para responder en JSON  -ver
    private function json(Response $res, array $data, int $status): Response
    {
        $res->getBody()->write(json_encode($data));
        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}