<?php

$servername = "php.localhost";  // Nombre del servidor (generalmente 'localhost')
$username = "root";         // Tu usuario de MySQL (por defecto es 'root' en local)
$password = "";             // Contraseña de tu base de datos (deja vacío si no tiene)
$dbname = "seminariophp";  // Nombre de la base de datos a la que te conectarás

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//echo "Conexión exitosa";


// Consulta SQL (ajusta el nombre de la tabla)
$sql = "SELECT * FROM usuario"; 
$resultado = mysqli_query($conn, $sql);

// Mostrar los datos en una tabla HTML
if (mysqli_num_rows($resultado) > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
            </tr>";

    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
                <td>" . $fila["id"] . "</td>
                <td>" . $fila["nombre"] . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No hay registros.";
}

// Cerrar conexión
mysqli_close($conn);
?>

