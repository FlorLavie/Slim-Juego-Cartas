<?php

// controllador para el usuario

namespace App\Controllers;

use App\Models\Usuario;
use Firebase\JWT\JWT;
 
use Psr\Http\Message\ResponseInterface as Response; // para acceder a datos que manda el cliente. ej postman
use Psr\Http\Message\ServerRequestInterface as Request;// para enviar la respuesta al cliente
use App\Middlewares\IsLoggedMiddleware;

class AuthController
{   

    // EDITAR USUARIO
    public function editarUsuario(Request $request, Response $response,array $args): Response
    {

        $datos = $request->getParsedBody();

        $idUsuario= $args['id'];
        $nombre= $datos['nombre']??'';
        $password = $datos['password']??'';
       
        // Obtener el usuario logueado desde el request
        $usuarioToken = $request->getAttribute('usuario');
       

       // Verificar que el usuario logueado tenga el mismo ID que el de la URL
        if ($usuarioToken->id != $idUsuario) {
            $response->getBody()->write(json_encode(["error" => "No tienes permisos para editar este usuario."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }
        
          // Validación del nombre
        $patronNombre = "/^[a-zA-Z0-9]{6,20}$/";
            if (!preg_match($patronNombre, $nombre)) {
                $response->getBody()->write(json_encode(["error" => "Nombre no valido."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
         }

         // Validación de la contraseña (mínimo 8 caracteres, mayúsculas, minúsculas, números y caracteres especiales)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
            $response->getBody()->write(json_encode(["error" => "Contraseña no valida."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        // se actualiza el usuario
        $usuario = new Usuario();
        $actualizar= $usuario->actualizarUsuario($nombre,$password,$idUsuario);
        
        if (!$actualizar){
            $response->getBody()->write(json_encode(["error"=> $actualizar["error"]]));
            return $response->withHeader('Content-Type','application/json')->withStatus(401);
        }
        $response->getBody()->write(json_encode(["mensaje" => "Usuario actualizado"]));
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    }





    // OBTENER USUARIO
    public function obtenerUsuario (Request $request, Response $response, array $args): Response
    {
        
        $id= $args['id'];

        
        $usuario= new Usuario();
        $verificacion = $request->getAttribute('usuario');
       
        if (!$verificacion["logueado"]){
            $response->getBody()->write(json_encode(["error" => $verificacion["error"]]));
            return $response->withHeader('Content-Type','application/json')->withStatus(401);
        }
        print_r($usuario->obtenerUsuario($id));
        $response->getBody()->write(json_encode(["mensaje" => "Usuario encontrado"]));
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    }





    // LOGIN 
    public function login(Request $request, Response $response): Response
    {
        $datos= $request->getParsedBody(); //obtengo lo que escribio el usuario

        $nombre = $datos['nombre']??'';
        $user = $datos['usuario']??'';
        $password = $datos['password']??'';

        $usuarioExiste = new Usuario();
        $usuario=$usuarioExiste->existeUsuario($nombre,$user,$password);
        if ($usuario){
            $usu = $usuario['usuario'];
            $idUsuario = $usuario['id'];

            date_default_timezone_set('America/Argentina/Buenos_Aires'); // O la zona horaria que desees


            $expire = (new \DateTime("now"))->modify("+1 hour")->format("Y-m-d H:i:s");

            $token = JWT::encode(["usuario"=> $idUsuario, "expired_at" => $expire], IsLoggedMiddleware::$secret, 'HS256');
            
            
            $usuarioExiste->actualizarToken($user,$token,$expire);


            
        } else {
            return $this->json($response,[
                'error' => 'Credenciales no válidas.'
            ],404);
        }
        //Devuelvo el token como respuesta en el header
        $response = $response->withHeader('token', $token);
          //Envío en el Body un mensaje
        $response->getBody()->write(json_encode(['mensaje' => 'Usuario loggeado!']));
        $response->withStatus(200);
        return $response;

    }






    // REGISTRO
    public function registro(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody(); // obtenemos datos que envio el cliente
        $user = $datos['usuario']??'';
        $nombre = $datos['nombre'] ?? ''; //guardamos datos
        $password= $datos['password'] ?? '';

        // Validaciones que no tienen que ver con la base de datos
        // Nombre entre 6 y 20 caracteres y solo alfanumericos . ctype_alnum verifica que no tenga caracteres especiales
        //preg_match()
        $patron = "/^[a-zA-Z0-9]{6,20}$/";
        if (!preg_match($patron,$user)){
            return $this->json($response,[
                'error' => 'El nombre de usuario debe tener entre 6 y 20 caracters y solo contener letras y numeros.'
            ],400);
        }
         // Password por lo menos 8 caracteres. contener caracteres mayúsculas, minúsculas, números y caracteres especiales.   
        // Expresiones regulares preg_match()

        if (!preg_match('/^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[^A-Za-z0-9]).{8,}$/', $password)){
            return $this->json($response,[
                'error' => 'La contraseña debe tener al menos 8 caracteres y contener mayúsculas, minúsculas, números y caracteres especiales.'
            ],400);
         }   


        // Que el nombre no este en uso
          // Instanciar el modelo `Usuario` y verificar si el nombre ya existe
        $usuario = new Usuario();
         if ($usuario->existeNombre($nombre)) {
             return $this->json($response, ['error' => 'Ese nombre de usuario ya está en uso.'], 409);
         }
          
        // Registrar usuario
        $okRegistro = $usuario->registrar($nombre,  $password);

        if (!$okRegistro) {
            return $this->json($response, ['error' => 'Error al registrar.'], 500);
        }

        return $this->json($response, ['mensaje' => 'Usuario registrado con éxito.'], 201);
    }



    // Función para responder en JSON  
    private function json(Response $res, array $data, int $status): Response
    {
        $res->getBody()->write(json_encode($data));
        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}