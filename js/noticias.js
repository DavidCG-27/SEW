class Noticias {
    constructor() {
        if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
            document.write("<p>Este navegador no soporta API File, por lo que puede que no se genere la página correctamente</p>");
            return;
        }

        document.querySelector("form > button").addEventListener("click", (event) => {
            event.preventDefault();
            this.addNoticiaManual();
        });

        document.querySelector("h3+p > label > input").addEventListener("change", (event) => {
            this.readInputFile(event.target.files);
        });
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
            imagen.setAttribute("alt", "Imagen generica de F1");
            article.insertBefore(imagen, article.querySelector("p:first-of-type"));
        });
        
        let section = document.querySelector("section");
        if (!section) {
            section = document.createElement("section");
            let h3 = document.createElement("h3");
            h3.textContent = "Últimas noticias";
            section.appendChild(h3);
            document.querySelector("main").appendChild(section);
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
        let form = document.querySelector("form");
        let titulo = form.querySelector("input[name='titulo']").value;
        let contenido = form.querySelector("input[name='contenido']").value;
        let autor = form.querySelector("input[name='autor']").value;

        if (titulo && contenido && autor) {
            this.createNoticiaElement(titulo, contenido, autor);
            
            form.querySelector("input[name='titulo']").value = "";
            form.querySelector("input[name='contenido']").value = "";
            form.querySelector("input[name='autor']").value = "";
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    var n = new Noticias();
});
