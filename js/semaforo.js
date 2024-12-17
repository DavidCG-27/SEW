class Semaforo {
    levels = [0.2, 0.5, 0.8];
    lights = 4;
    unload_moment = null;
    clic_moment = null;
    difficulty = 0;
    constructor() {
        this.difficulty = this.levels[Math.floor(Math.random() * this.levels.length)];
    }
    createStructure() {
        let main = document.querySelector('main');
        let titulo = document.createElement('h3');
        titulo.textContent = 'Semáforo';
        main.appendChild(titulo);

        for (let i = 0; i < this.lights; i++) {
            let div = document.createElement('div');
            main.appendChild(div);
        }

        let button = document.createElement('button');
        button.textContent = 'Arranque';
        button.addEventListener('click', this.initSequence.bind(this));
        main.appendChild(button);

        button = document.createElement('button');
        button.addEventListener('click', this.stopReaction.bind(this));
        button.setAttribute('disabled', '');
        button.textContent = 'Reacción';
        main.appendChild(button);
    }

    stopReaction() {
        this.clic_moment = new Date();
        let diferencia = this.clic_moment - this.unload_moment;

        let main = document.querySelector('main');
        main.classList.remove('unload');

        let p = document.createElement('p');
        p.textContent = `Tiempo de reacción: ${diferencia} ms`;
        main.appendChild(p);

        let bArranque = document.querySelector('main > button:first-of-type');
        bArranque.removeAttribute('disabled');

        let bReaccion = document.querySelector('main > button:nth-of-type(2)');
        bReaccion.setAttribute('disabled', '');

        this.createRecordForm(diferencia);
    }

    endSequence() {
        let main = document.querySelector('main');
        main.classList.remove('load');
        main.classList.add('unload');
        let button = document.querySelector('main > button:nth-of-type(2)');
        button.removeAttribute('disabled');
    }

    initSequence() {
        let main = document.querySelector('main');
        main.classList.add('load');
        let p = document.querySelector('body > main > p');
        if (main && p) {
            main.removeChild(p);
        }
        let form = document.querySelector('body > main > form');
        if(main && form){
            main.removeChild(form);
        }

        let button = document.querySelector('main > button:first-of-type');
        button.setAttribute('disabled', '');

        setTimeout(() => {
            this.unload_moment = new Date();
            this.endSequence();
        }, 2000 + (this.difficulty * 100));
    }

    createRecordForm(tiempo){
        let form = document.createElement("form");
        form.setAttribute("name","datos");
        form.setAttribute("method", "post");

        let label = document.createElement("label");
        label.textContent="Nombre: "
        let input = document.createElement("input");
        input.setAttribute("type","text");
        input.setAttribute("name","nombre");
        label.appendChild(input);
        form.appendChild(label);

        label = document.createElement("label");
        label.textContent="Apellidos: "
        input = document.createElement("input");
        input.setAttribute("type","text");
        input.setAttribute("name","apellidos");
        label.appendChild(input);
        form.appendChild(label);

        label = document.createElement("label");
        label.textContent="Nivel: "
        input = document.createElement("input");
        input.value = this.parseDifficulty();
        input.setAttribute("name","nivel");
        input.setAttribute("readonly","");
        label.appendChild(input);
        form.appendChild(label);

        label = document.createElement("label");
        label.setAttribute("name","tiempo");
        label.textContent="Tiempo: "
        input = document.createElement("input");
        input.value = `${tiempo/1000} segundos`;
        input.setAttribute("readonly","");
        label.appendChild(input);
        form.appendChild(label);

        input = document.createElement("input");
        input.setAttribute("type","submit");
        input.setAttribute("name","submit");
        input.setAttribute("value","Enviar");
        form.appendChild(input);

        let main = document.querySelector('main');      
        main.appendChild(form);
    }
    parseDifficulty(){
        switch(this.difficulty){
            case 0.2:
                return "Fácil";
            case 0.5:
                return "Medio";
            case 0.8:
                return "Difícil";
            default:
                return "ERROR";
        }
    }
}