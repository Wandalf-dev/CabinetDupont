// Animation Lottie
document.addEventListener('DOMContentLoaded', function() {
    const lottieContainer = document.getElementById("lottie-animation");
    if (lottieContainer) {
        lottie.loadAnimation({
            container: lottieContainer,
            renderer: "svg",
            loop: true,
            autoplay: true,
            path: "assets/Dentist.json",
        });
    }

    // Mise en surbrillance du jour courant
    const today = new Date().getDay(); // 0=Dimanche ... 6=Samedi
    const rows = document.querySelectorAll(".hours-table tr[data-day]");
    rows.forEach((tr) => {
        if (parseInt(tr.getAttribute("data-day"), 10) === today) {
            tr.classList.add("is-today");
        }
    });
});