class Fondo {
    constructor(pais, ciudad, circuito) {
        this.pais = pais;
        this.ciudad = ciudad;
        this.circuito = circuito;
    }

    getImagen() {
        var flickrAPI = "http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        $.getJSON(flickrAPI,
            {
                tags: "circuit, " + this.ciudad,
                tagmode: "all",
                format: "json"
            })
            .done(function (data) {
                var url = (data.items[0].media.m).replace("_m.jpg", "_b.jpg");;
                $("body").css({
                    "background-image": "url(" + url + ")",
                    "background-repeat": "no-repeat",
                    "background-size": "cover",
                    "min-height": "100vh" //Ponemos el min-height para que el fondo se vea correctamente en pantallas pequeñas
                });
            });
    }
}
var f = new Fondo('Azerbaiyán', 'Baku', 'Circuito callejero de Bakú');
f.getImagen();