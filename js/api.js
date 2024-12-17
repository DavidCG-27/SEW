class Prediccion {
    constructor() {
        this.apiPilotos = "https://api.jolpi.ca/ergast/f1/2024/drivers.json";
        this.apiCarreras = "https://api.jolpi.ca/ergast/f1/2024/races.json";
    }

    pintarPilotos(callback) {
        $.getJSON(this.apiPilotos, (data) => {
            data.MRData.DriverTable.Drivers.forEach((driver) => {
                let li = document.createElement("li");
                li.setAttribute("draggable", "true");
                li.setAttribute("data-piloto", driver.code);
                li.textContent = `${driver.givenName} ${driver.familyName}`;
                $("body > main > aside > ul").append(li);
            });

            if (callback) callback();
        });
    }

    pintarPuestos(callback) {
        $.getJSON(this.apiCarreras, (data) => {
            let numCarreras = data.MRData.total;
            let carrera = data.MRData.RaceTable.Races[numCarreras - 1];
            let carreraHTML = `
                <h4>
                    ${carrera.raceName}
                </h4>`;
            $("main > section > ul").before(carreraHTML);
        });

        for (let i = 0; i < 23; i++) {
            let li = document.createElement("li");
            li.setAttribute("data-dropzone", i + 1);
            li.textContent = `${i + 1}º`;
            $("body > main > section > ul").append(li);
        }

        if (callback) callback();
    }

    pintarTitulo() {
        let canvas = document.querySelector("body > canvas");
        let canvas1 = canvas.getContext("2d");
        canvas1.font = "italic 3em sans-serif";
        canvas1.strokeStyle = "#126263";
        canvas1.strokeText("Predicción de puestos", 0, 80);
    }

    aniadirListeners() {
        //Ejecutar cuando el DOM este cargado
        const pilotos = document.querySelectorAll("aside ul li[draggable='true']");
        const dropzones = document.querySelectorAll("section ul li[data-dropzone]");

        let draggedElement = null;

        pilotos.forEach((piloto) => {
            piloto.addEventListener("dragstart", (event) => {
                draggedElement = event.target;
            });

            piloto.addEventListener("dragend", () => {
                draggedElement = null;
            });
        });

        dropzones.forEach((dropzone) => {
            dropzone.addEventListener("dragover", (event) => {
                event.preventDefault();
            });

            dropzone.addEventListener("drop", (event) => {
                event.preventDefault();

                const existingPilot = dropzone.querySelector("[draggable='true']");
                if (existingPilot) {
                    document.querySelector("body > main > aside > ul").appendChild(existingPilot);
                }

                dropzone.append(draggedElement);
            });
        });

        const originalList = document.querySelector("body > main > aside > ul");
        originalList.addEventListener("dragover", (event) => {
            event.preventDefault();
        });

        originalList.addEventListener("drop", (event) => {
            event.preventDefault();

            if (draggedElement) {
                originalList.appendChild(draggedElement);
            }
        });

        document.querySelector("button").addEventListener("click", () => {
            let posiciones = [];
            dropzones.forEach((dropzone) => {
                const pilotoElement = dropzone.querySelector("[draggable='true']");
                if (pilotoElement) {
                    posiciones.push({
                        puesto: dropzone.textContent.trim(),
                        piloto: pilotoElement.getAttribute("data-piloto"),
                    });
                }
            });

            const blob = new Blob([JSON.stringify(posiciones, null, 2)], {
                type: "application/json",
            });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "posiciones.json";
            link.click();
        });

        document.querySelector("body>header+p+h3+nav+canvas+main+button+button").addEventListener("click", () => {
            p.showAyuda();
        });
    }

    showAyuda(){
        let p = document.querySelector("body>header+p+h3+nav+canvas+main+button+button+script+p");
        if(p)
            p.remove();
        else{
            let ayuda = document.createElement("p");
            ayuda.textContent = "La aplicación consiste en predecir la clasificación de una carrera. Se arrastrará cada piloto a su casilla correspondiente y luego se guardará en formato JSON.";
            let body = document.querySelector("body");
            body.appendChild(ayuda);
        }
    }
}

var p = new Prediccion();
p.pintarTitulo();
p.pintarPilotos(() => {
    p.pintarPuestos(() => {
        p.aniadirListeners();
    });
});
