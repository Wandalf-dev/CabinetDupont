// Animation Lottie
document.addEventListener('DOMContentLoaded', function() {
    const lottieContainer = document.getElementById("lottie-animation");
    if (lottieContainer) {
        // Récupère l'URL de base depuis une balise meta ou utilise un chemin relatif
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || window.location.origin + '/cabinetdupont';
        
        try {
            const animation = lottie.loadAnimation({
                container: lottieContainer,
                renderer: "svg",
                loop: true,
                autoplay: true,
                path: baseUrl + "/assets/Dentist.json",
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid slice',
                    progressiveLoad: true,
                    hideOnTransparent: true
                }
            });
        } catch (error) {
            console.error('Erreur lors du chargement de l\'animation Lottie:', error);
        }
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