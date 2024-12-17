<?php
class Simulador
{
    private $conn;
    private $user;
    private $host;
    private $pass;
    private $dbname;

    public function __construct()
    {
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        $this->dbname = "simulador_temporadas";

        $this->inicializarBD();

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
    }

    private function inicializarBD()
    {
        $db = new mysqli($this->host, $this->user, $this->pass);

        $query = "CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "`";
        $db->query($query);

        $db->select_db($this->dbname);

        $createPilotosTable = "CREATE TABLE IF NOT EXISTS `pilotos` (
            `piloto_id` INT AUTO_INCREMENT PRIMARY KEY,
            `nombre` VARCHAR(50) NOT NULL UNIQUE
        )";
        $db->query($createPilotosTable);

        $createEscuderiasTable = "CREATE TABLE IF NOT EXISTS `escuderias` (
            `escuderia_id` INT AUTO_INCREMENT PRIMARY KEY,
            `nombre` VARCHAR(50) NOT NULL UNIQUE
        )";
        $db->query($createEscuderiasTable);

        $createCarrerasTable = "CREATE TABLE IF NOT EXISTS `carreras` (
            `carrera_id` INT AUTO_INCREMENT PRIMARY KEY,
            `nombre` VARCHAR(50) NOT NULL UNIQUE
        )";
        $db->query($createCarrerasTable);

        $createPilotosEscuderiasTable = "CREATE TABLE IF NOT EXISTS `pilotos_escuderias` (
            `piloto_id` INT,
            `escuderia_id` INT,
            PRIMARY KEY (`piloto_id`, `escuderia_id`),
            FOREIGN KEY (`piloto_id`) REFERENCES `pilotos`(`piloto_id`),
            FOREIGN KEY (`escuderia_id`) REFERENCES `escuderias`(`escuderia_id`)
        )";
        $db->query($createPilotosEscuderiasTable);

        $createEscuderiasCarrerasTable = "CREATE TABLE IF NOT EXISTS `escuderias_carreras` (
            `escuderia_id` INT,
            `carrera_id` INT,
            PRIMARY KEY (`escuderia_id`, `carrera_id`),
            FOREIGN KEY (`escuderia_id`) REFERENCES `escuderias`(`escuderia_id`),
            FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`carrera_id`)
        )";
        $db->query($createEscuderiasCarrerasTable);

        $db->close();
    }

    public function reiniciarBaseDeDatos()
    {
        $db = new mysqli($this->host, $this->user, $this->pass, database: $this->dbname);
        $resultado = $db->query("SHOW TABLES");
        while ($fila = $resultado->fetch_array()) {
            $tabla = $fila[0];
            $db->query("SET FOREIGN_KEY_CHECKS = 0");
            $db->query("TRUNCATE TABLE `$tabla`");
            $db->query("SET FOREIGN_KEY_CHECKS = 1");
        }
        $db->close();
    }

    public function importarCSV($tabla, $archivo)
{
    if (!file_exists($archivo) || !is_readable($archivo)) {
        return "Error: No se pudo abrir el archivo CSV.";
    }

    $gestor = fopen($archivo, "r");
    $columnas = fgetcsv($gestor, 1000, ",");
    
    if (!$columnas) {
        fclose($gestor);
        return "Error: El archivo CSV no tiene cabeceras válidas.";
    }

    $columnasBD = [];
    $result = $this->conn->query("DESCRIBE `$tabla`");
    while ($row = $result->fetch_assoc()) {
        $columnasBD[] = $row['Field'];
    }

    $columnasValidas = array_intersect($columnas, $columnasBD);
    if (empty($columnasValidas)) {
        fclose($gestor);
        return "Error: Ninguna de las columnas del CSV coincide con la tabla '$tabla'.";
    }

    $numColumnas = count($columnasValidas);
    $placeholders = implode(',', array_fill(0, $numColumnas, '?'));
    $sql = "INSERT IGNORE INTO `$tabla` (`" . implode('`, `', $columnasValidas) . "`) VALUES ($placeholders)";

    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        fclose($gestor);
        return "Error al preparar la consulta: " . $this->conn->error;
    }

    $types = str_repeat('s', $numColumnas); 
    $params = array_fill(0, $numColumnas, '');

    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        $datosValidos = [];
        foreach ($columnasValidas as $col) {
            $index = array_search($col, $columnas);
            $datosValidos[] = $index !== false ? $datos[$index] : null;
        }

        $stmt->bind_param($types, ...$datosValidos);
        $stmt->execute();
    }

    fclose($gestor);
    $stmt->close();
    return "Archivo CSV importado correctamente en la tabla '$tabla'.";
}


    public function addPiloto($nombre)
    {
        $stmt = $this->conn->prepare("INSERT INTO pilotos (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->close();
    }

    public function addEscuderia($nombre)
    {
        $stmt = $this->conn->prepare("INSERT INTO escuderias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->close();
    }

    public function addCarrera($nombre)
    {
        $stmt = $this->conn->prepare("INSERT INTO carreras (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->close();
    }

    public function assignPilotoAEscuderia($piloto, $escuderia)
    {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO pilotos_escuderias (piloto_id, escuderia_id) VALUES ((SELECT piloto_id FROM pilotos WHERE nombre = ?), (SELECT escuderia_id FROM escuderias WHERE nombre = ?))");
        $stmt->bind_param("ss", $piloto, $escuderia);
        $stmt->execute();
        $stmt->close();
    }

    public function assignEscuderiaACarrera($escuderia, $carrera)
    {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO escuderias_carreras (escuderia_id, carrera_id) VALUES ((SELECT escuderia_id FROM escuderias WHERE nombre = ?), (SELECT carrera_id FROM carreras WHERE nombre = ?))");
        $stmt->bind_param("ss", $escuderia, $carrera);
        $stmt->execute();
        $stmt->close();
    }

    public function getOptions($table)
    {
        $result = $this->conn->query("SELECT nombre FROM $table");
        $options = [];
        while ($row = $result->fetch_assoc()) {
            $options[] = $row['nombre'];
        }
        return $options;
    }

    public function simulateCarrera($carrera)
    {
        $stmt = $this->conn->prepare("
        SELECT c.carrera_id
        FROM carreras c
        WHERE c.nombre = ?
    ");
        $stmt->bind_param("s", $carrera);
        $stmt->execute();
        $result = $stmt->get_result();
        $carreraData = $result->fetch_assoc();
        $stmt->close();

        if (!$carreraData) {
            return "La carrera especificada no existe.";
        }

        $stmt = $this->conn->prepare("
        SELECT e.escuderia_id, e.nombre AS escuderia
        FROM escuderias e
        JOIN escuderias_carreras ec ON e.escuderia_id = ec.escuderia_id
        JOIN carreras c ON ec.carrera_id = c.carrera_id
        WHERE c.nombre = ?
    ");
        $stmt->bind_param("s", $carrera);
        $stmt->execute();
        $result = $stmt->get_result();

        $escuderias = [];
        while ($row = $result->fetch_assoc()) {
            $escuderias[] = $row;
        }
        $stmt->close();

        if (empty($escuderias)) {
            return "No hay escuderías asociadas a esta carrera.";
        }

        $escuderiaIds = array_column($escuderias, 'escuderia_id');
        $placeholders = implode(',', array_fill(0, count($escuderiaIds), '?'));
        $types = str_repeat('i', count($escuderiaIds));

        $stmt = $this->conn->prepare("
        SELECT p.nombre AS piloto, e.nombre AS escuderia
        FROM pilotos p
        JOIN pilotos_escuderias pe ON p.piloto_id = pe.piloto_id
        JOIN escuderias e ON pe.escuderia_id = e.escuderia_id
        WHERE e.escuderia_id IN ($placeholders)
    ");
        $stmt->bind_param($types, ...$escuderiaIds);
        $stmt->execute();
        $result = $stmt->get_result();

        $pilotos = [];
        while ($row = $result->fetch_assoc()) {
            $pilotos[] = $row;
        }
        $stmt->close();

        if (empty($pilotos)) {
            return "No hay pilotos asociados a las escuderías de esta carrera.";
        }

        shuffle($pilotos);

        $rankedList = [];
        foreach ($pilotos as $index => $piloto) {
            $rankedList[] = [
                'posicion' => $index + 1,
                'piloto' => $piloto['piloto'],
                'escuderia' => $piloto['escuderia']
            ];
        }

        return $rankedList;
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

$simulador = new Simulador();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_piloto'])) {
        $simulador->addPiloto($_POST['nombre_piloto']);
    } elseif (isset($_POST['add_escuderia'])) {
        $simulador->addEscuderia($_POST['nombre_escuderia']);
    } elseif (isset($_POST['add_carrera'])) {
        $simulador->addCarrera($_POST['nombre_carrera']);
    } elseif (isset($_POST['assign_piloto_escuderia'])) {
        $simulador->assignPilotoAEscuderia($_POST['piloto'], $_POST['escuderia']);
    } elseif (isset($_POST['assign_escuderia_carrera'])) {
        $simulador->assignEscuderiaACarrera($_POST['escuderia'], $_POST['carrera']);
    } elseif (isset($_POST['simulate_carrera'])) {
        $simulador->simulateCarrera($_POST['carrera']);
    } elseif (isset($_POST['reiniciar'])) {
        $simulador->reiniciarBaseDeDatos();
    } elseif (isset($_POST['import_csv_pilotos']) && isset($_FILES['csv_pilotos'])) {
        $archivo = $_FILES['csv_pilotos']['tmp_name'];
        $simulador->importarCSV('pilotos', $archivo);
    } elseif (isset($_POST['import_csv_escuderias']) && isset($_FILES['csv_escuderias'])) {
        $archivo = $_FILES['csv_escuderias']['tmp_name'];
        $simulador->importarCSV('escuderias', $archivo);
    } elseif (isset($_POST['import_csv_carreras']) && isset($_FILES['csv_carreras'])) {
        $archivo = $_FILES['csv_carreras']['tmp_name'];
        $simulador->importarCSV('carreras', $archivo);
    } elseif (isset($_POST['import_csv_pilotosEscuderias']) && isset($_FILES['csv_pilotosEscuderias'])) {
        $archivo = $_FILES['csv_pilotosEscuderias']['tmp_name'];
        $simulador->importarCSV('pilotos_escuderias', $archivo);
    } elseif (isset($_POST['import_csv_escuderiasCarreras']) && isset($_FILES['csv_escuderiasCarreras'])) {
        $archivo = $_FILES['csv_escuderiasCarreras']['tmp_name'];
        $simulador->importarCSV('escuderias_carreras', $archivo);
    }
}

$pilotos = $simulador->getOptions('pilotos');
$escuderias = $simulador->getOptions('escuderias');
$carreras = $simulador->getOptions('carreras');
?>

<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="David Covián Gómez" />
    <meta name="description" content="Simulador de temporadas de F1" />
    <meta name="keywords" content="F1, simulador, temporada, pilotos, carreras, escuderías" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>F1 Desktop - Simulador</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/simulador.css" />
    <link rel="icon" href="multimedia/imagenes/favicon.ico" />
</head>

<body>
    <header>
        <h1><a href="index.html" title="Enlace a pagina de inicio">F1 Desktop</a></h1>
        <nav>
            <a href="../index.html" class="active" title="Página de Inicio">Inicio</a>
            <a href="../piloto.html" title="Piloto">Piloto</a>
            <a href="../noticias.html" title="Página de Noticias">Noticias</a>
            <a href="../calendario.html" title="Página del Calendario">Calendario</a>
            <a href="../meteorologia.html" title="Página de Meteorología">Meteorología</a>
            <a href="../circuito.html" title="Página de los circuitos">Circuito</a>
            <a href="../viajes.php" title="Página de Viajes">Viajes</a>
            <a href="../juegos.html" title="Página de Juegos">Juegos</a>
        </nav>
    </header>
    <p>Estás en: <a href="index.html" title="Página de Inicio">Inicio</a> >> <a href="juegos.html"
            title="Página de Juegos">Juegos</a> >> Simulador de carreras</p>
    <h2>Simulador de carreras</h2>

    <form method="post">
        <h3>Añadir Piloto</h3>
        <label>Nombre del Piloto: <input type="text" name="nombre_piloto" required /></label>
        <button type="submit" name="add_piloto">Añadir Piloto</button>
    </form>

    <form method="post">
        <h3>Añadir Escudería</h3>
        <label>Nombre de la Escudería: <input type="text" name="nombre_escuderia" required /></label>
        <button type="submit" name="add_escuderia">Añadir Escudería</button>
    </form>

    <form method="post">
        <h3>Añadir Carrera</h3>
        <label>Nombre de la Carrera: <input type="text" name="nombre_carrera" required /></label>
        <button type="submit" name="add_carrera">Añadir Carrera</button>
    </form>

    <form method="post">
        <h3>Asignar Piloto a Escudería</h3>
        <label>Piloto:
            <select name="piloto">
                <?php foreach ($pilotos as $piloto): ?>
                    <option value="<?= $piloto ?>"><?= $piloto ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Escudería:
            <select name="escuderia">
                <?php foreach ($escuderias as $escuderia): ?>
                    <option value="<?= $escuderia ?>"><?= $escuderia ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="assign_piloto_escuderia">Asignar</button>
    </form>

    <form method="post">
        <h3>Asignar Escudería a Carrera</h3>
        <label>Escudería:
            <select name="escuderia">
                <?php foreach ($escuderias as $escuderia): ?>
                    <option value="<?= $escuderia ?>"><?= $escuderia ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Carrera:
            <select name="carrera">
                <?php foreach ($carreras as $carrera): ?>
                    <option value="<?= $carrera ?>"><?= $carrera ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="assign_escuderia_carrera">Asignar</button>
    </form>

    <form method="post">
        <h3>Simular Carrera</h3>
        <label>Carrera:
            <select name="carrera">
                <?php foreach ($carreras as $carrera): ?>
                    <option value="<?= $carrera ?>"><?= $carrera ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="simulate_carrera">Simular</button>
    </form>

    <form method="post">
        <button type="submit" name="ayuda">Ayuda</button>
        <button type="submit" name="reiniciar">Reiniciar Base de Datos</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h4>Pilotos</h4>
        <label>Importar CSV: <input type="file" name="csv_pilotos" accept=".csv" /></label>
        <button type="submit" name="import_csv_pilotos">Importar CSV</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h4>Escuderías</h4>
        <label>Importar CSV: <input type="file" name="csv_escuderias" accept=".csv" /></label>
        <button type="submit" name="import_csv_escuderias">Importar CSV</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h4>Carreras</h4>
        <label>Importar CSV: <input type="file" name="csv_carreras" accept=".csv" /></label>
        <button type="submit" name="import_csv_carreras">Importar CSV</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h4>Asignar pilotos a escuderías</h4>
        <label>Importar CSV: <input type="file" name="csv_pilotosEscuderias" accept=".csv" /></label>
        <button type="submit" name="import_csv_pilotosEscuderias">Importar CSV</button>
    </form>

    <form method="post" enctype="multipart/form-data">
        <h4>Asignar escuderías a carreras</h4>
        <label>Importar CSV: <input type="file" name="csv_escuderiasCarreras" accept=".csv" /></label>
        <button type="submit" name="import_csv_escuderiasCarreras">Importar CSV</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_carrera'])) {
        $carrera = $_POST['carrera'];
        $ranking = $simulador->simulateCarrera($carrera);

        if (!is_array($ranking)) {
            echo "<p>ERROR: $ranking</p>";
        } else {
            echo "<h4>Ranking para la carrera: $carrera</h4>";
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Posición</th>";
            echo "<th>Piloto</th>";
            echo "<th>Escudería</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($ranking as $puesto) {
                echo "<tr>";
                echo "<td>{$puesto['posicion']}</td>";
                echo "<td>{$puesto['piloto']}</td>";
                echo "<td>{$puesto['escuderia']}</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ayuda'])) {
        echo "<p>
            Simulador de temporadas de F1:
            Se trata de un simulador de carreras en el que el usuario introducirá pilotos, escuderías y carreras.
            1. Introduce un piloto
            2. Introduce una escudería
            3. Introduce una carrera
            4. Asigna un piloto a una escudería
            5. Asigna una escudería a una carrera
            6. Simula una carrera
            7. ¡Disfruta!
        </p>";
    }
    ?>
</body>

</html>