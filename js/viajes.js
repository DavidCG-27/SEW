class Viajes {
    constructor() {
        navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this), this.consultarErrores.bind(this));
    }
    getPosicion(posicion) {
        this.longitud = posicion.coords.longitude;
        this.latitud = posicion.coords.latitude;
        this.precision = posicion.coords.accuracy;
        this.altitud = posicion.coords.altitude;
        this.precisionAltitud = posicion.coords.altitudeAccuracy;
        this.rumbo = posicion.coords.heading;
        this.velocidad = posicion.coords.speed;
    }

    consultarErrores(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                this.mensaje = "El usuario no permite la petición de geolocalización"
                break;
            case error.POSITION_UNAVAILABLE:
                this.mensaje = "Información de geolocalización no disponible"
                break;
            case error.TIMEOUT:
                this.mensaje = "La petición de geolocalización ha caducado"
                break;
            case error.UNKNOWN_ERROR:
                this.mensaje = "Se ha producido un error desconocido"
                break;
        }
    }

    eliminar() {
        var sec1 = $("body > section");
        sec1.remove();

        var div = $("body > div");
        div.remove();
    }


    mostrarEstatico() {
        this.eliminar();
        this.getMapaEstatico();
    }

    mostrarDinamico() {
        this.eliminar();
        this.getMapaDinamico();
    }

    getMapaEstatico() {
        var apiKey = "&key=AIzaSyCMh6tEOK0VSOlEHxxx4Ln_QsT3Lno0_0U";
        var url = "https://maps.googleapis.com/maps/api/staticmap?";

        var centro = "center=" + this.latitud + "," + this.longitud;
        var zoom = "&zoom=15";
        var tamaño = "&size=800x600";
        var marcador = "&markers=color:red%7Clabel:S%7C" + this.latitud + "," + this.longitud;
        var sensor = "&sensor=false";

        this.imagenMapa = url + centro + zoom + tamaño + marcador + sensor + apiKey;

        var section = document.createElement("section");

        var h4 = document.createElement("h4");
        h4.textContent = "Mapa estático";
        section.append(h4);

        let imagen = document.createElement("img");
        imagen.src = this.imagenMapa;
        imagen.alt = "Mapa estático de tu posición";
        section.append(imagen);

        $("body").append(section);
    }

    getMapaDinamico() {
        var div = document.createElement("div");
        $("body").append(div);

        var centro = { lat: this.latitud, lng: this.longitud };
        var mapaGeoposicionado = new google.maps.Map(document.querySelector("body > div"), {
            zoom: 8,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        let infoWindow = new google.maps.InfoWindow;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                infoWindow.setPosition(pos);
                infoWindow.setContent('Localización encontrada');
                infoWindow.open(mapaGeoposicionado);
                mapaGeoposicionado.setCenter(pos);
            }, function () {
                handleLocationError(true, infoWindow, mapaGeoposicionado.getCenter());
            });
        } else {
            handleLocationError(false, infoWindow, mapaGeoposicionado.getCenter());
        }
    }

    crearFuncionalidadCarrusel() {
        const slides = document.querySelectorAll("img");
        const nextSlide = document.querySelector("button:nth-of-type(1)");

        let curSlide = 3;
        let maxSlide = slides.length - 1;

        nextSlide.addEventListener("click", function () {
            if (curSlide === maxSlide) {
                curSlide = 0;
            } else {
                curSlide++;
            }

            slides.forEach((slide, indx) => {
                var trans = 100 * (indx - curSlide);
                $(slide).css('transform', 'translateX(' + trans + '%)')
            });
        });

        const prevSlide = document.querySelector("button:nth-of-type(2)");

        prevSlide.addEventListener("click", function () {
            if (curSlide === 0) {
                curSlide = maxSlide;
            } else {
                curSlide--;
            }

            slides.forEach((slide, indx) => {
                var trans = 100 * (indx - curSlide);
                $(slide).css('transform', 'translateX(' + trans + '%)')
            });
        });
    }
}
var v = new Viajes();