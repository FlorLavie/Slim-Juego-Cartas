<?php

// el modelo se conecta con la base de datos, ejecuta consulta SQL y devuelve resultado al controlador
// tiene que importar la clase conexion de base de datos

// validaciones de base de datos ( ej si el usuario ya existe)

namespace App\Models;


use App\Config\Conexion; // importa clase con conexion a base de datos

class Usuario
{

    public function existeNombre(string $nombre): bool  //  Verificar que el nombre de usuario no esté en uso.
    {   // si el usuario ya existe en DB
        $pdo = Conexion::conectar(); // conecta a base de datos
        if (!$pdo) return false; // si no se pudo conectar

        $sql = "SELECT 1 FROM usuario WHERE nombre = :nombre";
        $stmt = $pdo->prepare($sql); // se prepara la consulta
        $stmt->execute([':nombre' => $nombre]); //se ejecuta la consulta

        return $stmt->fetch(); // resultado
    }

    // Inserta un nuevo usuario en la base de datos
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
}