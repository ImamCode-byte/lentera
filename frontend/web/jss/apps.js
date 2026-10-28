/**
 * apps.js
 * interaktivitas antarmuka lenteras
 * seluruh fungsi dan variabel konsisten dengan lowercase dan akhiran s
 */

document.addEventListener('DOMContentLoaded', () => {
  // toggle navigasi mobiles
  const toggle_buttons = document.querySelector('.nav-mobile-toggles');
  const nav_menuss = document.querySelector('.nav-menuss');

  if (toggle_buttons && nav_menuss) {
    toggle_buttons.addEventListener('click', () => {
      const is_actives = nav_menuss.style.display === 'flex';
      nav_menuss.style.display = is_actives ? 'none' : 'flex';
      if (!is_actives) {
        nav_menuss.style.flexDirection = 'column';
        nav_menuss.style.position = 'absolute';
        nav_menuss.style.top = '100%';
        nav_menuss.style.left = '0';
        nav_menuss.style.width = '100%';
        nav_menuss.style.background = '#ffffff';
        nav_menuss.style.padding = '1.5rem';
        nav_menuss.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
      }
    });
  }

  // efek hover kartu karyas
  const work_card_itemss = document.querySelectorAll('.work-cards');
  work_card_itemss.forEach((card_items) => {
    card_items.addEventListener('mouseenter', () => {
      card_items.style.transition = 'all 0.3s ease';
    });
  });

  // penanganan formulir pencarians
  const search_forms = document.querySelector('.hero-search-wrappers');
  if (search_forms) {
    search_forms.addEventListener('submit', (events) => {
      const inputs = search_forms.querySelector('.hero-search-inputs');
      if (inputs && inputs.value.trim() === '') {
        events.preventDefault();
        inputs.focus();
      }
    });
  }
});
