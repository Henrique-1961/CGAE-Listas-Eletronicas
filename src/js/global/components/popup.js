document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("popup").onclick = function() {
        if (!this.classList.contains("opacity-1")) return;

        this.classList.add("fade-out");
        this.classList.remove("opacity-1");
        
        setTimeout(() => {
            document.getElementById("popup").style.display = "none";
        }, 1000);
    }

    setTimeout(() => {
        const popup = document.getElementById("popup");

        if (popup.classList.contains("opacity-1")) {
            popup.classList.add("fade-out");
            popup.classList.remove("opacity-1");

            setTimeout(() => {
                document.getElementById("popup").style.display = "none";
            }, 1000);
        }
    }, 10000);
});