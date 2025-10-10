// Carousel circulaire JS pour services (effet infini sans reset visible)

const grid = document.querySelector('.services-track');
if (grid) {
  let position = 0;
  const speed = 0.7; // px par frame
  let paused = false;
  const cards = Array.from(grid.children);
  // On duplique les cartes 2 fois (total 3 sets)
  for (let i = 0; i < 2; i++) {
    cards.forEach(card => {
      const clone = card.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      grid.appendChild(clone);
    });
  }
  const setWidth = grid.scrollWidth / 3;
  // On commence au set du milieu
  position = -setWidth;
  function animate() {
    if (!paused) {
      position -= speed;
      if (Math.abs(position) >= setWidth * 2) {
        position = -setWidth;
      }
      grid.style.transform = `translateX(${position}px)`;
    }
    requestAnimationFrame(animate);
  }
  animate();
  // Ajout des listeners sur toutes les cartes
  grid.querySelectorAll('.service-card').forEach(card => {
    card.addEventListener('mouseenter', () => { paused = true; });
    card.addEventListener('mouseleave', () => { paused = false; });
  });
}
