class Pais {
    constructor(nombrePais, nombreCapital, poblacion) {
        this.apikey = "bdf77abd364137ac8f46b6e5b1b4e226";
        this.nombrePais = nombrePais;
        this.nombreCapital = nombreCapital;
        this.poblacion = poblacion;
    }
    completarPais(nombreCircuito, longitudMeta, latitudMeta, altitudMeta, formaDeGobierno, religionMayoritaria) {
        this.nombreCircuito = nombreCircuito;
        this.formaDeGobierno = formaDeGobierno;
        this.religionMayoritaria = religionMayoritaria;
        this.longitudMeta = longitudMeta;
        this.latitudMeta = latitudMeta;
        this.altitudMeta = altitudMeta;
    }
    getNombre() {
        return this.nombrePais;
    }
    getCapital() {
        return this.nombreCapital;
    }
    getInfo() {
        return `<ol> <li>Nombre del circuito: ${this.nombreCircuito}</li> <li>Población: ${this.poblacion} habitantes</li> <li>Forma de gobierno: ${this.formaDeGobierno}</li> <li>Religion mayoritaria: ${this.religionMayoritaria}</li> </ol>`;
    }
    writeCoordernadas() {
        document.write(`<p> Coordenadas de la linea de meta del circuito (longitud,latitud,altitud): [${this.longitudMeta};${this.latitudMeta};${this.altitudMeta}] </p>`);
    }

    cargarTiempo() {
        this.tipo = "xml";
        this.unidades = "metric";
        this.idioma = "es";
        this.url = "https://api.openweathermap.org/data/2.5/forecast?lat=" + this.latitudMeta + "&lon=" + this.longitudMeta + "&units=" + this.unidades + "&lang=" + this.idioma + "&mode=" + this.tipo + "&APPID=" + this.apikey;
        console.log(this.url);
        $.ajax({
            dataType: "xml",
            url: this.url,
            method: 'GET',
            success: function (datos) {
                var fecha_inicial =new Date($('forecast > time:first-child', datos).attr("from"));
                let contador = 0;
                let section = document.createElement("section");
                $("body").append(section);
                let h3 = document.createElement("h3");
                h3.textContent = "Pronóstico del tiempo para la zona de la meta";
                section.appendChild(h3);
                for (let i = 0; i < $('forecast > time', datos).length; i++) { 
                    if(contador == 5){break;}

                    let fecha = new Date($('forecast > time:nth-of-type('+i+')', datos).attr("from"));
                    if(fecha.getHours() == fecha_inicial.getHours()){
                        contador++;
                        let fecha_hora_string = fecha.toLocaleString("es-ES");
                        let temperaturaMax = $('forecast > time:nth-of-type('+i+') > temperature', datos).attr("max");
                        let temperaturaMin = $('forecast > time:nth-of-type('+i+') > temperature', datos).attr("min");
                        let humedad = $('forecast > time:nth-of-type('+i+') > humidity', datos).attr("value");
                        let icono = "https://openweathermap.org/img/wn/"+$('forecast > time:nth-of-type('+i+') > symbol', datos).attr("var")+".png";
                        let iconoSignificado = $('forecast > time:nth-of-type('+i+') > symbol', datos).attr("name");

                        let article = document.createElement("article");
                        let h3 = document.createElement("h3");
                        h3.textContent = fecha_hora_string;
                        article.appendChild(h3);
                        let img = document.createElement("img");
                        img.setAttribute("src", icono);
                        img.setAttribute("alt", iconoSignificado);
                        article.appendChild(img);

                        let p = document.createElement("p");
                        p.textContent = "Temperatura máxima: " + temperaturaMax + "°C";
                        article.appendChild(p);

                        p = document.createElement("p");
                        p.textContent = "Temperatura mínima: " + temperaturaMin + "°C";
                        article.appendChild(p);

                        p = document.createElement("p");
                        p.textContent = "Humedad: " + humedad + "%";
                        article.appendChild(p);

                        $("section").append(article);
                    }
                }
            }
        });
    }

}
var p = new Pais("Azerbaiyán", "Bakú", "10110000");
p.completarPais("Circuito callejero de Bakú", "49.8544016", "40.3734472", "-0.5107555", "República", "Musulmana");
document.write(`<h3>Meteorologia en ${p.getNombre()} </h3>`);
document.write(`<h4> Datos relevantes: </h4>`);
document.write(`<p> Capital: ${p.getCapital()}</p>`);
document.write(p.getInfo());
p.writeCoordernadas();
p.cargarTiempo();
