// Animation Lottie
lottie.loadAnimation({
    container: document.getElementById("lottie-animation"),
    renderer: "svg",
    loop: true,
    autoplay: true,
    path: "/cabinetdupont/assets/Dentist.json",
});

// Mise en surbrillance du jour courant
(function () {
    const today = new Date().getDay(); // 0=Dimanche ... 6=Samedi
    const rows = document.querySelectorAll(".hours-table tr[data-day]");
    rows.forEach((tr) => {
        if (parseInt(tr.getAttribute("data-day"), 10) === today) {
            tr.classList.add("is-today");
        }
    });
})();