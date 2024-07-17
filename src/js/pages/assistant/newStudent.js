import initializeSelects from "./../../global/components/select.js";

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById("internato").addEventListener("change", function() { configSelects("internato") });
    document.getElementById("quarto").addEventListener("change", function() { configSelects("quarto") });
    configSelects("internato", true);
})


function configSelects(id = undefined, onload = false) {
    const containers = document.getElementsByClassName("select-container");

    if (!onload) {
        for (let i = 0; i < containers.length; i++) {
            containers[i].removeChild(containers[i].children[1]);
            containers[i].removeChild(containers[i].children[1]);
        }
    }

    const internato = document.getElementById("internato").selectedIndex == 0 ? 'm' : 'f';
    const quarto = document.getElementById("quarto").options[document.getElementById("quarto").selectedIndex].innerHTML;

    for (let j = 0; j < containers.length; j++) {
        const selects = containers[j].getElementsByTagName("select");

        for (let i = 0; i < selects.length; i++) {
            if (selects[i].id == "serie") continue;
            if (selects[i].id == "internato") continue;
            if (selects[i].id == "quarto" && id == "quarto") continue;

            switch (selects[i].id) {
                case "quarto": {
                    if (internato == 'm') {
                        for (let k = 0; k < selects[i].options.length; k++) selects[i].options[k].disabled = k == 4;
                    }

                    else {
                        for (let k = 0; k < selects[i].options.length; k++) selects[i].options[k].disabled = k > 4;
                    }

                    break;
                }

                case "cama": {
                    const tmp = quarto.split(".")[1];
                    if (internato == 'm') {
                        for (let k = 0; k < selects[i].options.length; k++) selects[i].options[k].disabled = tmp == 4 ? k > 7 : k > 9;
                    }

                    else {
                        for (let k = 0; k < selects[i].options.length; k++) selects[i].options[k].disabled = quarto == "1.2" ? false : quarto == "1.3" ? k > 7 : k > 9;
                    }

                    break;
                }
            }

            if (selects[i].options[selects[i].selectedIndex].disabled) selects[i].selectedIndex = 0;
        }
    }

    initializeSelects(onload);
}