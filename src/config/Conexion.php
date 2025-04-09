<?php

// conexion a base de datos usando PDO
// reutilizar la clase

namespace App\Config;

use PDO;
use PDOException;

class Conexion
{
    public static function conectar(): ?PDO
    {
        $host = 'localhost';
        $base = 'seminariophp';
        $usuario = 'root';
        $contrasena = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$base;charset=utf8mb4", $usuario, $contrasena);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }
}