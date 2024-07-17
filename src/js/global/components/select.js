function initializeSelects(onload = true) {
    var x, i, j, l, ll, selElmnt, a, b, c;

    x = document.getElementsByClassName("select-container");
    l = x.length;

    for (i = 0; i < l; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        ll = selElmnt.length;
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");

        // Adiciona os elementos 'option' na nova 'div'
        for (j = 0; j < ll; j++) {
            // Cria a sub'div' e coloca seu texto como sendo o texto da option original correspondente
            c = document.createElement("DIV");
            c.innerHTML = selElmnt.options[j].innerHTML;
            if (selElmnt.options[j].disabled) c.classList.add("disabled");
            c.index = j;
            c.select = selElmnt;

            // Adiciona um evento de click a sub'div'
            c.addEventListener("click", function (e) {
                if (this.select.options[this.index].disabled) {
                    e.stopPropagation();
                    return;
                }

                var y, i, k, s, h, sl, yl;

                // Seleciona a 'select' original
                s = this.parentNode.parentNode.getElementsByTagName("select")[0];

                // Configura para todas as 'select's presentes no container
                sl = s.length;

                // Recupera o elemento que mostra o valor na tela
                h = this.parentNode.previousSibling;

                // Itera sobre todas as 'select's encontradas
                for (i = 0; i < sl; i++) {
                    if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        y = this.parentNode.getElementsByClassName("same-as-selected");
                        yl = y.length;

                        for (k = 0; k < yl; k++) {
                            y[k].classList.remove("same-as-selected");
                        }

                        this.classList.add("same-as-selected");
                        break;
                    }
                }
                
                s.dispatchEvent(new Event("change"));
                h.click();
            });

            if (selElmnt.selectedIndex == j) c.classList.add("same-as-selected");

            b.appendChild(c);
        }
        x[i].appendChild(b);
        a.addEventListener("click", function (e) {
            e.stopPropagation();
            closeAllSelect(this);

            try {
                this.nextSibling.classList.toggle("select-hide");
                this.classList.toggle("select-arrow-active");
            } catch {}
        });
    }

    function closeAllSelect(elmnt) {
        var x, y, i, xl, yl, arrNo = [];
        x = document.getElementsByClassName("select-items");
        y = document.getElementsByClassName("select-selected");
        xl = x.length;
        yl = y.length;
        for (i = 0; i < yl; i++) {
            if (elmnt == y[i]) {
                arrNo.push(i)
            } else {
                y[i].classList.remove("select-arrow-active");
            }
        }
        for (i = 0; i < xl; i++) {
            if (arrNo.indexOf(i)) {
                x[i].classList.add("select-hide");
            }
        }
    }

    if (onload) document.addEventListener("click", closeAllSelect);
};

export default initializeSelects;