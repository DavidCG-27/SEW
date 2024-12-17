<!DOCTYPE HTML>
<?php
class Record{
    public $server;
    public $user;
    public $pass;
    public $dbname;
    function __construct(){
        $this->server="localhost";
        $this->user="DBUSER2024";
        $this->pass="DBPSWD2024";
        $this->dbname="records";
    }

    function mostrar_10_mejores(){
        $db = new mysqli($this->server, $this->user, $this->pass, $this->dbname);
        $query = "SELECT * FROM registro ORDER BY tiempo ASC LIMIT 10";
        $result = $db->query($query);
        echo "<section>";
        echo "<h2>10 mejores tiempos</h2>";
        echo "<ol>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>".$row["nombre"]. " ". $row["apellidos"]. " - ". $row["nivel"]. " - ". $row["tiempo"]." segundos". "</li>";
        }
        echo "</ol>";
        echo "</section>";
        $db->close();
    }
}
$record = new Record();
$db = new mysqli($record->server, $record->user, $record->pass, $record->dbname);

if ($db->connect_errno){
    echo "Error de conexión: " . $db->connect_error;
    exit();
}

$nombre = "";
$apellidos = "";
$nivel = "";
$tiempo = 0.0;

if (isset($_POST['submit'])) {
    if(!(empty(trim($_POST["nombre"])) || empty(trim($_POST["apellidos"])))){
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $nivel = $_POST["nivel"];
        
        if (isset($_POST["tiempo"])) {
            $tiempo = (double)explode(" ", $_POST["tiempo"])[0];
        }

        $consulta = $db->prepare("INSERT INTO registro (nombre, apellidos, nivel, tiempo) VALUES (?, ?, ?, ?)");
        $consulta->bind_param("sssd", $nombre, $apellidos, $nivel, $tiempo);
        $consulta->execute();

        $consulta->close();
    }
    $db->close();
}
?>


<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name ="author" content ="David Covián Gómez" />
    <meta name ="description" content ="Juego del semáforo" />
    <meta name ="keywords" content ="F1, semáforo, juego, velocidad, respuesta, tiempo" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>F1 Desktop</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="estilo/semaforo_grid.css" />

    <script src="js/semaforo.js"></script>    
</head>
<body>
    <header>
        <h1><a href="index.html" title="Enlace a pagina de inicio">F1 Desktop</a></h1>
        <nav>
            <a href="index.html" title="Pagina de Inicio ">Inicio</a>
            <a href="piloto.html" title="Piloto ">Piloto</a>
            <a href="noticias.html" title="Pagina de Noticias ">Noticias</a>
            <a href="calendario.html" title="Pagina del Calendario ">Calendario</a>
            <a href="meteorologia.html" title="Pagina de Meteorología ">Meteorología</a>
            <a href="circuito.html" title="Pagina de los circuitos ">Circuito</a>
            <a href="viajes.html" title="Pagina de Viajes ">Viajes</a>
            <a href="juegos.html" class="active" title="Pagina de Juegos ">Juegos</a>
        </nav>
    </header>
    <p>Estás en: <a href="index.html" title="Pagina de Inicio">Inicio</a> >> <a href="juegos.html" title="Pagina de Juegos">Juegos</a> >> Semáforo</p>
    <h3>Menú de juegos disponibles</h3>
    <nav>
        <ol>
            <li><a href="memoria.html" title="Memoria">Memoria</a></li>
            <li><a href="semaforo.php" title="Semáforo">Semáforo</a></li>
            <li><a href="api.html" title="Predicción de puestos (Ejercicio API libre JS)">Predicción de puestos</a></li>
        </ol>
    </nav>
    <main>
        <script>new Semaforo().createStructure()</script>
        <?php $record->mostrar_10_mejores(); ?>
    </main>
</body>
</html>