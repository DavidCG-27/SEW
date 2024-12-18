<!DOCTYPE HTML>
<?php
class Carrusel
{
    protected $pais;
    protected $capital;
    function __construct($pais, $capital)
    {
        $this->pais = $pais;
        $this->capital = $capital;
    }

    function crearCarrusel()
    {
        $api_key = 'a618ac02d533ab23767ec09984deda54';
        $tag = $this->pais;
        $perPage = 10;

        $url = 'https://api.flickr.com/services/feeds/photos_public.gne?';
        $url .= '&api_key=' . $api_key;
        $url .= '&tags=' . $tag;
        $url .= '&per_page=' . $perPage;
        $url .= '&format=json';
        $url .= '&nojsoncallback=1';

        $respuesta = file_get_contents($url);
        $json = json_decode($respuesta);
        if ($json == null) {
            echo "<h3>Error en el archivo JSON recibido</h3>";
        }
        echo "<article>";
        echo "<h3>Carrusel de fotos de " . $this->pais . "</h3>";
        for ($i = 0; $i < $perPage; $i++) {
            $titulo = $json->items[$i]->title;
            $URLfoto = str_replace("_m.jpg", "_b.jpg", $json->items[$i]->media->m);
            print "<img alt='" . $titulo . "' src='" . $URLfoto . "' />";
        }
        echo "<button> &gt; </button>";
        echo "<button> &lt; </button>";
        echo "</article>";
    }
}

class Moneda
{
    protected $cambio;
    protected $local;
    function __construct($cambio, $local)
    {
        $this->cambio = $cambio;
        $this->local = $local;
    }

    // Para realizar el cambio de moneda utilizo la API Fixer.io
    function pintarCambio()
    {
        $accessKey = '3f9c55b40372275aa9ed4a111948f9de'; 

        $url = "https://data.fixer.io/api/latest?access_key=".$accessKey."&base=".$this->local."&symbols=".$this->cambio;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resultado = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($resultado, true);

        $tipoCambio = $data['rates'][$this->cambio] ?? null;

        if (!$tipoCambio) {
            echo("<p>Error al obtener el tipo de cambio</p>");
        }
        echo "<article>";
        echo "<h3> Cambio de moneda </h3>";
        echo "<p>1 ". $this->local. " equivale a ". $tipoCambio. " ". $this->cambio. "</p>";
        echo "</article>";
    }
}
?>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="David Covián Gómez" />
    <meta name="description" content="Viajes" />
    <meta name="keywords" content="F1, viaje, lance, stroll" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>F1 Desktop - Viajes</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="estilo/viajes.css" />
    <link rel="icon" href="multimedia/imagenes/favicon.ico" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/viajes.js"></script>
</head>

<body>
    <header>
        <h1><a href="index.html" title="Enlace a pagina de inicio">F1 Desktop</a></h1>
        <nav>
            <a href="index.html" title="Página de Inicio ">Inicio</a>
            <a href="piloto.html" title="Piloto ">Piloto</a>
            <a href="noticias.html" title="Página de Noticias ">Noticias</a>
            <a href="calendario.html" title="Página del Calendario ">Calendario</a>
            <a href="meteorologia.html" title="Página de Meteorología ">Meteorología</a>
            <a href="circuito.html" title="Página de los circuitos ">Circuito</a>
            <a href="viajes.php" class="active" title="Página de Viajes ">Viajes</a>
            <a href="juegos.html" title="Página de Juegos ">Juegos</a>
        </nav>
    </header>
    <p>Estás en: <a href="index.html" title="Página de Inicio">Inicio</a> >> Viajes</p>
    <h2>Viajes</h2>
    <?php
    $carrusel = new Carrusel("Azerbaijan", "Baku");
    $carrusel->crearCarrusel();
    ?>
    <?php 
        $moneda = new Moneda("AZN", "EUR");
        $moneda->pintarCambio();
    ?>
    <main>
        <h3>Obtener Mapas</h3>
        <input type="button" value="Obtener mapa estático" onClick="v.mostrarEstatico();" />
        <input type="button" value="Obtener mapa dinámico" onClick="v.mostrarDinamico();" />
    </main>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMh6tEOK0VSOlEHxxx4Ln_QsT3Lno0_0U&callback=v.getMapaDinamico"></script>
    <script async>v.eliminar()</script>
    <script>v.crearFuncionalidadCarrusel()</script>
</body>

</html>