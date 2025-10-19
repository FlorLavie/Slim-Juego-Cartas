# 🎴 API Slim PHP - Juego de Cartas

**Proyecto:** `Slim-Juego-Cartas`  
**Materia:** Seminario de Lenguajes – Opción: PHP, React y API REST  
**Universidad:** UNLP  

---

## 🔹 **Descripción**

API REST desarrollada con **Slim Framework (PHP)** y **MySQL** para gestionar un **juego de cartas online**.  
Permite registrar usuarios, crear mazos, jugar partidas contra el servidor y consultar estadísticas.  
Sigue **arquitectura MVC** y utiliza **JSON** para la comunicación entre cliente y servidor.

---

## 🔹 **Tecnologías utilizadas**

- **PHP** + Slim Framework  
- **MySQL** para persistencia de datos  
- **Arquitectura MVC**  
- **JSON** para intercambio de datos  
- **Endpoints REST** siguiendo modelo CRUD  

---

## 🔹 **Endpoints principales**

### **Usuarios**
- `POST /login` – Verifica credenciales y retorna token  
- `POST /registro` – Agrega un nuevo usuario  
- `PUT /usuarios/{usuario}` – Edita nombre y contraseña del usuario logueado  
- `GET /usuarios/{usuario}` – Obtiene información del usuario logueado  

### **Mazos**
- `POST /mazos` – Crea un mazo (máx. 5 cartas, 3 mazos por usuario)  
- `PUT /mazos/{mazo}` – Edita nombre del mazo  
- `DELETE /mazos/{mazo}` – Elimina un mazo si no ha participado en partidas  
- `GET /usuarios/{usuario}/mazos` – Lista los mazos del usuario  

### **Juego**
- `POST /partidas` – Inicia una partida con un mazo del usuario  
- `POST /jugadas` – Registra la jugada del usuario y calcula la jugada del servidor  
- `GET /usuarios/{usuario}/partidas/{partida}/cartas` – Lista cartas en mano (opcional)  

### **Cartas**
- `GET /cartas?atributo={atributo}&nombre={nombre}` – Lista cartas según filtros y puntos de ataque  

### **Estadísticas**
- `GET /estadisticas` – Devuelve cantidad de partidas ganadas, perdidas y empatadas (no requiere login)  

---

## 🔹 **Reglas y consideraciones**

- Todos los endpoints devuelven **status code HTTP** adecuado:  
  - 200 OK – Acción exitosa  
  - 400 Bad Request – Solicitud incorrecta  
  - 401 Unauthorized – Usuario no autorizado  
  - 404 Not Found – Recurso no encontrado  
  - 409 Conflict – Conflicto al eliminar datos  
- Los errores incluyen un mensaje explicativo en el body  
- Para JSON: `$app->addBodyParsingMiddleware();` y `$data = $request->getParsedBody();`  
- Eliminaciones solo si los datos no se usan en otras tablas  

---

## 🔹 **Mecánica del juego**

- Cada jugador puede crear **hasta 3 mazos** de 5 cartas  
- El servidor tiene un mazo predefinido (usuario id=1)  
- Cada partida tiene **5 rondas**: jugador y servidor juegan cartas simultáneamente  
- Algunas cartas tienen **ventajas de atributo** (tabla `gana_a`)  
- Las cartas usadas se descartan; al final de la 5ta ronda se determina el ganador

## 🔹 **Cómo usar**

1. Clonar el repositorio:  
```bash
git clone <url-del-repo>
