class Noticias {
    constructor() {
        if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
            document.write("<p>Este navegador no soporta API File, por lo que puede que no se genere la página correctamente</p>");
            return;
        }

        document.querySelector("main > button").addEventListener("click", () => this.addNoticiaManual());

        document.querySelector("p > label > input").addEventListener("change", (event) => this.readInputFile(event.target.files));
    }

    readInputFile(files) {
        var archivo = files[0];
        var tipoTexto = /text.*/;
        if (archivo.type.match(tipoTexto)) {
            var lector = new FileReader();
            lector.onload = (evento) => {
                this.printNoticias(evento.target.result);
            };
            lector.readAsText(archivo);
        } else {
            return;
        }
    }

    printNoticias(contenidoArchivo) {
        var noticias = contenidoArchivo.split("\n");
        noticias.forEach((item) => {
            var [titulo, contenido, autor] = item.split("_");
            if (titulo && contenido && autor) {
                this.createNoticiaElement(titulo, contenido, autor);
            }
        });
    }

    createNoticiaElement(titulo, contenido, autor) {
        var article = document.createElement("article");

        let tituloElemento = document.createElement("h3");
        tituloElemento.textContent = titulo;

        let contenidoElemento = document.createElement("p");
        contenidoElemento.textContent = contenido;

        let autorElemento = document.createElement("p");
        autorElemento.textContent = `Autor: ${autor}`;

        article.appendChild(tituloElemento);
        
        article.appendChild(contenidoElemento);
        article.appendChild(autorElemento);

        this.getImagenParaNoticia((imagenUrl) => {
            let imagen = document.createElement("img");
            imagen.setAttribute("src", imagenUrl);
            imagen.setAttribute("alt", "Imagen generica de F1")
            article.insertBefore(imagen, article.querySelector("p:first-of-type"));
        });
        let section = document.querySelector("section");
        if(!section){
            section = document.createElement("section");
            document.querySelector("main").appendChild(section)
        }
        section.appendChild(article);
    }

    getImagenParaNoticia(funcion) {
        var flickrAPI = "http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        $.getJSON(flickrAPI, {
            tags: "F1 2024",
            tagmode: "all",
            format: "json"
        })
        .done(function (data) {
            let i = Math.floor(Math.random() * data.items.length);
            funcion(data.items[i].media.m);
        });
    }

    addNoticiaManual() {
        let titulo = document.querySelector("main > label:first-of-type > input").value;
        let contenido = document.querySelector("main > label:nth-of-type(2) > input").value;
        let autor = document.querySelector("main > label:nth-of-type(3) > input").value;

        if (titulo && contenido && autor) {
            this.createNoticiaElement(titulo, contenido, autor);
            
            document.querySelector("main > label:first-of-type > input").value = "";
            document.querySelector("main > label:nth-of-type(2) > input").value = "";
            document.querySelector("main > label:nth-of-type(3) > input").value = "";
        }
    }
}
document.addEventListener("DOMContentLoaded", () => {
    var n = new Noticias();
});


