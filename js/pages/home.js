// Animation Lottie
document.addEventListener('DOMContentLoaded', function() {
    const lottieContainer = document.getElementById("lottie-animation");
    if (lottieContainer) {
        // Récupère l'URL de base depuis la balise meta
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        
        // Construire le chemin correct (éviter les doubles slashes)
        const animationPath = baseUrl ? baseUrl + "/assets/Doctor.json" : "/assets/Doctor.json";
        
        console.log('Tentative de chargement Lottie:', animationPath);
        
        try {
            const animation = lottie.loadAnimation({
                container: lottieContainer,
                renderer: "svg",
                loop: true,
                autoplay: true,
                path: animationPath,
                rendererSettings: {
                    preserveAspectRatio: 'xMidYMid slice',
                    progressiveLoad: true,
                    hideOnTransparent: true
                }
            });
            
            animation.addEventListener('data_ready', function() {
                console.log('✅ Animation Lottie chargée avec succès');
            });
            
            animation.addEventListener('data_failed', function() {
                console.error('❌ Échec du chargement de l\'animation Lottie');
            });
        } catch (error) {
            console.error('❌ Erreur Lottie:', error);
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