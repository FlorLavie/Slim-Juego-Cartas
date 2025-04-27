<?php

// controllador para el usuario

// partida y jugada
namespace App\Controllers;

use App\Models\Juego;
use Psr\Http\Message\ResponseInterface as Response; // para acceder a datos que manda el cliente. ej postman
use Psr\Http\Message\ServerRequestInterface as Request;// para enviar la respuesta al cliente
use App\Middlewares\IsLoggedMiddleware;

class JuegoController
{
    public function partida (Request $request, Response $response ): Response
    {
        $datos = $request->getParsedBody(); // recibe id de mazo
        $idMazo = $datos ['idMazo'];

        $juego = new Juego();

        //middleware verifica que el usuario este logueado

        // verificar que pertenezca al usuario logueado
        $verificado = $juego->verificarUsuario($idMazo);


        // crea partida para el usuario logueado
        $partida= $juego->crearPartida($idMazo,$verificado);
        if (!$partida){
            $response->getBody()->write(json_encode(["error" => "Error al crear la partida"]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }

        $cartas = $juego->obtenerCartas($idMazo);
        $responseData = [
            "mensaje" =>  "Partida creada exitosamente",
            "idPartida" => $partida,
            "cartas" => $cartas
        ];

        $response->getBody()->write(json_encode($responseData)); 
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    
    
    

        $enMano = $juego->actualizarEnMano($idMazo);
        if (!$enMano){
            $response->getBody()->write(json_encode(["error"=> "No se pudo obtener el mazo"]));
            return $response->withHeader('Content-Type','application/json')->withStatus(400);
        }
    }







        //JUGADA
       
        public function jugada (Request $request,Response $response):Response
        {   // VERIFICAR CARTA
            // CREAR JUGADA
            // CALCULAR GANADOR
            // ACTUALIZAR ESTADOS DE CARTAS ( DESCARTADAS)
            // GUARDAR EN EZL REGISTRO JUGADA EZL ESTADO FINAL DE LA MISMA
            // CERRAR PARTIDA SI ES LA QUINTA JUGADA

            $datos = $request->getParsedBody();
            $carta = $datos['carta']??''; //id carta jugador
            $idPartida = $datos['partida']??''; // id partida jugada
            
            // middleware verifica que el usuario este logueado

            $juego = new Juego();
            
            // validar que la carta este en el mazo con la que se empezó la partida
            // que no haya sido descartada
            $verificacion = $juego->verificarCarta($carta,$idPartida);
    
            if (!$verificacion["verificacion"]){
                $response->getBody()->write(json_encode(["error" => $verificacion["error carta"]]));
                return $response->withHeader('Content-Type','application/json')->withStatus(400);
            }

            // guardo carta del servidor 
            $cartaServidor = $juego->jugadaServidor();
            

            // GANADOR 
            $ganador = $juego ->calcularGanador($carta,$cartaServidor, $idPartida);


            // creo jugada
            $id_jugada = $juego->crearJugada( $carta, $cartaServidor, $idPartida);
            if (!$id_jugada){
                $response->getBody()->write(json_encode([
                    "error"=>"Hubo un error procesando la jugada"
                ]));
                return $response -> withHeader ("content_Type", "application/json")-> withStatus(400);
            } 
            $response->getBody()->write(json_encode([
                "mensaje" => "jugada procesada correctamente",
            ]));
            return $response->withHeader('Content-Type','application/json')->withStatus(200);
    

            //actualizar el estado de la en mazo_carta a DESCARTADO
            // actuazliar estado de las cartas
            $juego-> descartarCarta ($carta);
            $juego->descartarCarta($idCartaServidor);

             // actualizar el estado de la jugada ( gano, perdio , empato)
            $juego -> actualizarEstadoJugada ($id_jugada, $ganador);


            // FALTA LO DE LOS PUNTOS DE FUERZA
            $puntosJugador
            $puntosServidor
       
            // Verificar si es la quinta jugada para cerrar la partida
            $jugadasRealizadas = $juego->contarJugadas($idPartida);

            if ($jugadasRealizadas == 5) {
            // Cerrar partida con estado "finalizada"
                $juego->cerrarPartida($idPartida, $ganador);
                $mensajeFinal = [
                     'mensaje' => '5 jugadas realizadas, la partida finalizó',
                     'ganador' => $ganador['ganador']
                ];
           } else {
                $mensajeFinal = [
                'mensaje' => 'Jugada procesada correctamente'
            ];
          }

           // Devolver respuesta exitosa con los datos solicitados
           $response->getBody()->write(json_encode([
                  "cartaServidor" => $cartaServidor,
                  "puntosJugador" => $puntosJugador['puntosJugador'],
                  "puntosServidor" => $puntosGanador['puntosServidor'],
                  "mensaje" => $mensajeFinal
           ]));
    
          return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        
 }   

        


        
    
    

