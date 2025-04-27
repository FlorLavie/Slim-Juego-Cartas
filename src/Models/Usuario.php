<?php

// el modelo se conecta con la base de datos, ejecuta consulta SQL y devuelve resultado al controlador
// tiene que importar la clase conexion de base de datos

// validaciones de base de datos ( ej si el usuario ya existe)

namespace App\Models;
use App\Middlewares\IsLoggedMiddleware;


use App\Config\Conexion; // importa clase con conexion a base de datos
use PDO;
use Firebase\JWT\JWT;

class Usuario
{   
   
    //existe usuario?
    public function existeUsuario(string $nombre, string $user, string $password)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $sql = "SELECT * FROM usuario WHERE nombre = :nombre AND usuario = :usuario AND password = :password";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nombre'=> $nombre, ':usuario'=> $user, ':password'=> $password]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    // actualizar token
    public function actualizarToken ($user, $token, $vencimiento)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;
        
        $sql = "UPDATE usuario SET token = :token, vencimiento_token = :vencimiento WHERE usuario = :usuario";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':token' => $token,
            ':vencimiento'=> $vencimiento,
            ':usuario' => $user
        ]);
    }


 
    // ACTUALIZAR USUARIO
    public function actualizarUsuario(string $nombre, string $password, int $id)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $sql = "UPDATE usuario SET nombre = :nombre, password=:password WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':password' => $password,
            ':id' => $id
        ]);
    }

    // EXISTE NOMBRE DE USUARIO
    public function existeNombre(string $nombre): bool  //  Verificar que el nombre de usuario no esté en uso.
    {   // si el usuario ya existe en DB
        $pdo = Conexion::conectar(); // conecta a base de datos
        if (!$pdo) return false; // si no se pudo conectar

        $sql = "SELECT 1 FROM usuario WHERE nombre = :nombre";
        $stmt = $pdo->prepare($sql); // se prepara la consulta
        $stmt->execute([':nombre' => $nombre]); //se ejecuta la consulta

        return $stmt->fetch(); // resultado
    }


    // INSERTA USUARIO A BASE DE DATOS
    public function registrar(string $nombre, string  $password): bool
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $sql = "INSERT INTO usuario (nombre, password) VALUES (:nombre, :password)"; //Creamos una consulta SQL para insertar un nuevo usuario con nombre y contraseña.
        $stmt = $pdo->prepare($sql); // se prepara la consulta

        //$claveHash = password_hash($clave, PASSWORD_DEFAULT); // Encriptamos la clave

        return $stmt->execute([ // Ejecutamos la consulta con los valores reales.
            ':nombre' => $nombre,
            ':password' => $password
        ]);
    }

    // OBTENER USUARIO 
     public function obtenerUsuario(string $id)
     {
         $pdo = Conexion::conectar();
         if (!$pdo) return false;
    
         $sql = "SELECT * FROM usuario WHERE id = :id";
         $stmt= $pdo->prepare($sql);
         $stmt->execute([
                ':id' => $id
          ]);
    
          $usuario= $stmt->fetch(PDO::FETCH_ASSOC);
         return $usuario;
        }


        // VERIFICAR USUARIO mazo
        // obtiene usuario_id del mazo
        public function verificarUsuario($mazoId)
        {
            $pdo = Conexion::conectar();
            if (!$pdo) return false;
        
            $sql = "SELECT usuario_id FROM mazo WHERE id = :mazoId";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                 ':mazoId' => $mazoId 
                ]);
            $usuario_id = $stmt->fetchColumn();
        
            // retorno el usuario relacionado con el mazo
            return $usuario_id;
        }
}



