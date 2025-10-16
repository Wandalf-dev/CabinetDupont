// Auto-hide & close pour .flash-message
document.addEventListener('DOMContentLoaded', () => {
  const startLeave = (el) => el.classList.add('leaving');

  const wireToast = (el) => {
    if (el.__wired) return; // évite double binding
    el.__wired = true;

    // Durée configurable via data-duration (ms), défaut 5000
    const duration = parseInt(el.dataset.duration || '5000', 10);
    let timer = setTimeout(() => startLeave(el), duration);

    // Pause au survol
    el.addEventListener('mouseenter', () => clearTimeout(timer));
    el.addEventListener('mouseleave', () => {
      timer = setTimeout(() => startLeave(el), 1200);
    });

    // Bouton fermer optionnel
    const btn = el.querySelector('[data-flash-close]');
    if (btn) btn.addEventListener('click', () => startLeave(el));

    // Retrait du DOM quand l'anim de sortie finit
    el.addEventListener('animationend', () => {
      if (el.classList.contains('leaving')) el.remove();
    });
  };

  // Toasters présents au chargement
  document.querySelectorAll('.flash-message').forEach(wireToast);

  // Toasters ajoutés plus tard (ajax/partials)
  const mo = new MutationObserver((muts) => {
    muts.forEach(m => {
      m.addedNodes.forEach(n => {
        if (n.nodeType !== 1) return;
        if (n.matches?.('.flash-message')) wireToast(n);
        n.querySelectorAll?.('.flash-message').forEach(wireToast);
      });
    });
  });
  mo.observe(document.body, { childList: true, subtree: true });
});
