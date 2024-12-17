function leerArchivoTexto(files) {
    var archivo = files[0];
    var nombre = $("body > section:first-of-type > p:nth-of-type(1)");
    var tamaño = $("body > section:first-of-type > p:nth-of-type(2)");
    var tipo = $("body > section:first-of-type > p:nth-of-type(3)");
    var ultima = $("body > section:first-of-type > p:nth-of-type(4)");
    var contenido = $("body > section:first-of-type > p:nth-of-type(5)");
    var areaVisualizacion = $("body > section:first-of-type > pre:first-of-type");
    var errorArchivo = $("body > section:first-of-type > p:nth-of-type(6)");
    nombre.text("Nombre del archivo: " + archivo.name);
    tamaño.text("Tamaño del archivo: " + archivo.size + " bytes");
    tipo.text("Tipo del archivo: " + archivo.type);
    ultima.text("Fecha de la última modificación: " + archivo.lastModifiedDate);
    contenido.text("Contenido del archivo de texto:");
    var tipoTexto = /text.*/;
    if (archivo.type.match(tipoTexto)) {
        var lector = new FileReader();
        lector.onload = function (evento) {
            areaVisualizacion.text(lector.result);
        }
        lector.readAsText(archivo);
    }
    else {
        errorArchivo.innerText = "Error : Archivo no válido";
    }
}

function leerKml() {
    var inputElement = document.querySelector("body > section:nth-of-type(3) > input");
    if (inputElement.files && inputElement.files.length > 0) {
        var file = inputElement.files[0];
        var reader = new FileReader();

        reader.onload = function (event) {
            var parser = new DOMParser();
            var kml = parser.parseFromString(event.target.result, "application/xml");

            var mapDiv = document.querySelector("body > section:nth-of-type(3) > div:first-of-type");
            var centro = { lat: 40.3734472, lng: 49.8544016 };
            var map = new google.maps.Map(mapDiv, {
                center: centro,
                zoom: 15,
                mapTypeId: "terrain"
            });

            var lineString = kml.getElementsByTagName("LineString")[0];
            if (lineString) {
                var coordinatesText = lineString.getElementsByTagName("coordinates")[0].textContent.trim();

                var path = coordinatesText.split(/\s+/).map(coord => {
                    var [lng, lat] = coord.split(",").map(Number);
                    return { lat: lat, lng: lng };
                });

                new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: "#FF0000",
                    strokeOpacity: 1.0,
                    strokeWeight: 4,
                    map: map
                });
            } else{

            }
        };

        reader.readAsText(file); // Leer el archivo KML como texto
    }
}


function leerSVG() {
    var inputElement = document.querySelector("body > section:nth-of-type(2) > input");
    if (inputElement.files && inputElement.files.length > 0) {
        var imagen = inputElement.files[0];

        var reader = new FileReader();

        reader.onload = function (event) {
            var img = document.querySelector("body > section:nth-of-type(2) > img");
            if (img != null) {
                img.remove();
            }
            var img = document.createElement("img");
            img.setAttribute("src", event.target.result);
            img.setAttribute("alt", "Imagen que representa el SVG");
            let section = document.querySelector("body > section:nth-of-type(2)");
            section.appendChild(img);
        };

        reader.readAsDataURL(imagen);

    }
}
