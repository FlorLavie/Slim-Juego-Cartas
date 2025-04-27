<?php

// el modelo se conecta con la base de datos, ejecuta consulta SQL y devuelve resultado al controlador
// tiene que importar la clase conexion de base de datos


// partida y jugada 

namespace App\Models;


use App\Config\Conexion; // importa clase con conexion a base de datos
use PDO;
use App\Models\Usuario;
use App\Middlewares\IsLoggedMiddleware;

class Juego
{   
    // RETORNO EL USUARIO RELACIONADO AL MAZO
    public function verificarUsuario($mazoId)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;
    
        $sql = "SELECT usuario_id FROM mazo WHERE id = :mazoId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ ':mazoId' => $mazoId ]);
        $idUsuario = $stmt->fetchColumn();
    
        // Aquí retornas el usuario_id relacionado con el mazo
        return $idUsuario;
    }




    // crear partida
    public function crearPartida ($idMazo,$idUsuario)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $fechaActual = date("Y-m-d H:i:s");

        $sql = "INSERT INTO partida (usuario_id, fecha, mazo_id,estado) VALUES (:usuario_id, :fecha, :mazo_id, :estado)";

        $stmt = $pdo->prepare($sql);

        $exito = $stmt->execute([
            ':usuario_id' => $idUsuario,
            ':fecha'=> $fechaActual,
            ':mazo_id' => $idMazo,
            ':estado' => 'en_curso'
        ]);

        if ($exito){
            return $pdo->lastInsertId();
        } else {
            return false;
        }

    }


   // VERIFICAR CARTA
   // que para fijarnos cuantas jugadas va lo mejor seria
   // con el  id de jugada , algo con select count,
   public function verificarCarta ($idCarta, $idPartida) 
   {
       $pdo = Conexion::conectar ();
       if (!$pdo) return false;


       // id de partida 
       $sql = "SELECT mazo_id FROM partida WHERE id=:id";
       $stmt = $pdo -> prepare ($sql);
       $stmt -> execute([
           ':id'=> $idPartida
       ]);
       $idMazo = $stmt-> fetch(PDO::FETCH_ASSOC);
       $idUsuarioMazo = $this->verificarUsuario($idMazo);

       if (!$idUsuarioMazo){
           return ["verificacion" => false, "error"=> "Usuario no logueado"];
       }

       $sql = "SELECT usuario_id FROM partida WHERE id=:id";
       $stmt = $pdo -> prepare ($sql);
       $stmt -> execute([
           ':id'=> $idPartida
       ]);

       $idUsuarioPartida = $stmt -> fetch(PDO::FETCH_ASSOC);
       if ($idUsuarioMazo!=$idUsuarioPartida) {
           return ["verificacion"=>false, "error"=>"El mazo no pertenece al usuario"];
       }

       $sql = "SELECT estado FROM mazo_carta WHERE carta_id=:carta_id";
       $stmt = $pdo -> prepare ($sql);
       $stmt -> execute ([
           ':carta_id' => $idCarta
       ]);

       $estadoCarta = $stmt-> fetch(PDO::FETCH_ASSOC);

       if ($estadoCarta = 'descartado'){
           return ["verificacion"=>false, "error"=> "La carta ya fue utilizada"];
       }
       return true;

    }


    // ACTUALIZAR EN MANO
    public function actualizarEnMano($idMazo)
    {
        $pdo=Conexion::conectar();
        if (!$pdo) return false;

        $sql = "UPDATE mazo_carta SET estado='en_mano' WHERE mazo_id=:mazo_id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':mazo_id'=> $idMazo
        ]);
    }


    // jugada servidor
    public function jugadaServidor():int
    {
        $pdo=Conexion::conectar();
        if (!$pdo) return false;

        $stmt= $pdo-> query("SELECT id FROM mazo WHERE usuario_id= 1");
        $mazoServidor = $stmt -> fetch();

        $sql = "SELECT carta_id FROM mazo_carta WHERE mazo_id = :mazo_id and estado != 'descartado' ORDER BY RAND() LIMIT 1";
        $stmt = $pdo -> prepare ($sql);
        $stmt -> execute([
            ':mazo_id'=> $mazoServidor 
        ]);
        $carta= $stmt -> fetchColumn(); //CARTA QUE VA A JUGAR

        $sql = "UPDATE mazo_carta SET estado='descartado' WHERE carta_id=:carta_id";
        $stmt = $pdo -> prepare ($sql);   
        $stmt -> execute([
            ':carta_id'=> $carta 
        ]);    

        return $carta;

    }




     // calcular ganador
     public function calcularGanador ($idcartaJugador,$idCartaServidor)
     {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $sql = "SELECT COUNT(*) as gana
                FROM gana_a
                WHERE carta_ganadora_id = :carta_jugador AND carta_perdedora_id = :carta_servidor";
        $stmt = $pdo->prepare($sql);
        $stmt -> execute ([
            ":carta_jugador" => $idcartaJugador,
            ":carta_servidor" => $idCartaServidor
        ]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

           // Comparación de las cartas
        if ($idcartaJugador == $idCartaServidor) {
            return 'empato';
        } elseif ($resultado['gana'] > 0) {
            return 'gano';
        } else {
            return 'perdio';
        }
    }


    // CREAR JUGADA
    public function crearJugada ($idPartida, $idCartaJugador, $idCartaServidor)
    {   
        // insertar en registro jugada
        //devolver id_jugada

        
        $pdo=Conexion::conectar();
        if (!$pdo) return false;


       // 2.)  Insertar la jugada
          $sql = "INSERT INTO jugadas (partida_id, carta_id_a, carta_id_b, el_usuario)
            VALUES (:partida_id, :carta_id_a, :carta_id_b, :el_usuario)";
    $stmt = $pdo->prepare($sql);

    $exito = $stmt->execute([
        ':partida_id' => $idPartida,
        ':carta_id_a' => $idCartaJugador,
        ':carta_id_b' => $idCartaServidor,
        ':el_usuario' => null
    ]);

    if ($exito) {
        return $pdo->lastInsertId(); // Devuelve el id de la jugada creada
    } else {
        return false;
    }
}
 
   

    // actualizar estado de cartas
    public function descartarCartas ($idCarta){
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        // descartar cartas
        $sql = "UPDATE mazo_carta SET estado = 'descartado' WHERE carta_id = :dCarta";
        $stmt = $pdo ->prepare($sql);
        $stmt -> execute([
            'idCarta' => $idCarta
        ]);


    }


    // actualizlar estado de jugadas
    public function actualizarEstadoJugada ($id_jugada, $ganador){
        $pdo = Conexion::conectar();
        if (!$pdo) return false;

        $sql = "UPDATE jugadas SET el_usuario = :ganador WHERE id = :id_jugada";
        $stmt = $pdo -> prepare ($sql);

        $stmt -> execute ([
            ':ganador' => $ganador,
            ':id' => $id_jugada, !// id jugada a actualizar

        ]);
    
    }
    //CONTAR JUGADAS DE UNA PARTIDA

    public function contarJugadas ($idPartida){

        $pdo = Conexion::conectar();
        if (!pdo) return false;

        $sql = "SELECT COUNT(*) as cantidad FROM jugada WHERE id = :idPartida";
        $stmt = $pdo ->prepare ($sql);

        $stmt -> execute ([
            'id' => $idPartida
        ]);

        $resultado = $stmt -> fetch(PDO::FETCH_ASSOC);

        if ($resultado){
            return (int)$resultado['cantidad']; //num Jugadas
        } else {
            return 0;
         }


    }


    // cerrar partida
    public function cerrarPartida($idPartida, $ganador)
    {
        $pdo = Conexion::conectar();
        if (!$pdo) return false;
    
        $sql = "UPDATE partida 
                SET estado = 'finalizada', el_usuario = :ganador
                WHERE id = :idPartida";
    
        $stmt = $pdo->prepare($sql);
    
        $exito = $stmt->execute([
            ':el_usuario' => $ganador,
            ':id' => $idPartida
        ]);
    
        return $exito;
    }


    // btener fuerza total
    public function obtenerFuerza ( $idCartaJugador,$idCartaServidor){

    }


}