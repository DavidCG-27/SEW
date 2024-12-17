class Agenda {
    constructor() {
        this.apiURL = "https://api.jolpi.ca/ergast/f1/2024/races.json";
    }

    cargarCarreras() {
        $.getJSON(this.apiURL, (data) => {
            let section = document.createElement("section");
            $("main").append(section);
            let h3 = document.createElement("h3");
            h3.textContent= "Carreras de esta temporada";
            $("main > section").append(h3);
            data.MRData.RaceTable.Races.forEach((race) => {
                const carreraHTML = `
                    <article>
                        <h3>${race.raceName}</h3>
                        <p>Circuito: ${race.Circuit.circuitName}</p>
                        <p>Ubicación: ${race.Circuit.Location.locality}, ${race.Circuit.Location.country}</p>
                        <p>Coordenadas: ${race.Circuit.Location.lat}, ${race.Circuit.Location.long}</p>
                        <p>Fecha y hora: ${race.date} ${race.time}</p>
                    </article>`;
                
                $("main > section").append(carreraHTML);
            });
        });
    }
}
$(document).ready(function() {
    const agenda = new Agenda();
    $("main > button").on("click", function() {
        agenda.cargarCarreras();
    });
});
