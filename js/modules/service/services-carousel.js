// Carousel avec comportements différents desktop/mobile
// Desktop : animation continue automatique (comme à l'origine)
// Mobile : autoslide carte par carte + swipe manuel

const carousel = document.querySelector('.services-carousel');
const track = document.querySelector('.services-track');

if (carousel && track) {
  const MOBILE_BREAKPOINT = 768;
  let autoSlideTimer = null;
  let isInteracting = false;
  let animationSpeed = 0.5; // vitesse animation desktop
  let position = 0;
  let animationId = null;

  function isMobile() {
    return window.innerWidth <= MOBILE_BREAKPOINT;
  }

  // ===== DESKTOP : Animation continue originale =====
  function initDesktopAnimation() {
    // Dupliquer les cartes pour l'effet infini desktop
    const cards = Array.from(track.children);
    cards.forEach(card => {
      const clone = card.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    });

    const setWidth = track.scrollWidth / 2;
    position = 0;

    function animate() {
      if (!isMobile() && !isInteracting) {
        position -= animationSpeed;
        if (Math.abs(position) >= setWidth) {
          position = 0;
        }
        track.style.transform = `translateX(${position}px)`;
      }
      animationId = requestAnimationFrame(animate);
    }

    animate();

    // Pause au survol desktop
    track.querySelectorAll('.service-card').forEach(card => {
      card.addEventListener('mouseenter', () => { isInteracting = true; });
      card.addEventListener('mouseleave', () => { isInteracting = false; });
    });
  }

  // ===== MOBILE : Slider carte par carte =====
  const originalCards = Array.from(track.querySelectorAll('.service-card'));
  
  function initMobileSlider() {
    // Dupliquer pour mobile (si pas déjà fait)
    const currentCards = track.querySelectorAll('.service-card').length;
    if (currentCards === originalCards.length) {
      for (let i = 0; i < 2; i++) {
        originalCards.forEach(card => {
          const clone = card.cloneNode(true);
          clone.setAttribute('aria-hidden', 'true');
          track.appendChild(clone);
        });
      }
    }
  }

  function getCardWidth() {
    const card = originalCards[0];
    if (!card) return 280;
    const gap = parseFloat(getComputedStyle(track).gap) || 16;
    return card.offsetWidth + gap;
  }

  function getSetWidth() {
    return getCardWidth() * originalCards.length;
  }

  function slideNext() {
    if (!isMobile() || isInteracting) return;
    
    const cardWidth = getCardWidth();
    const setWidth = getSetWidth();
    
    carousel.scrollBy({ left: cardWidth, behavior: 'smooth' });
    
    setTimeout(() => {
      if (carousel.scrollLeft >= setWidth) {
        carousel.scrollTo({ left: 0, behavior: 'auto' });
      }
    }, 500);
  }

  function startMobileSlide() {
    if (!isMobile()) return;
    stopMobileSlide();
    autoSlideTimer = setInterval(slideNext, 3500);
  }

  function stopMobileSlide() {
    if (autoSlideTimer) {
      clearInterval(autoSlideTimer);
      autoSlideTimer = null;
    }
  }

  // Events mobile
  carousel.addEventListener('touchstart', () => {
    isInteracting = true;
    stopMobileSlide();
  });

  carousel.addEventListener('touchend', () => {
    isInteracting = false;
    if (isMobile()) setTimeout(startMobileSlide, 2000);
  });

  // Initialisation
  if (isMobile()) {
    initMobileSlider();
    setTimeout(startMobileSlide, 1000);
  } else {
    initDesktopAnimation();
  }

  // Handle resize
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      if (isMobile()) {
        if (animationId) cancelAnimationFrame(animationId);
        track.style.transform = '';
        startMobileSlide();
      } else {
        stopMobileSlide();
        carousel.scrollLeft = 0;
        if (!animationId) initDesktopAnimation();
      }
    }, 300);
  });
}

