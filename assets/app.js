import './js/script.js';
import './styles/app.css'; 

console.log('JS chargé avec succès !');

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// ========================
// Thèmes multiples (clair, sombre, sépia, bleu, One Piece)
// ========================
const selector = document.getElementById("theme-selector");
const body = document.body;
const themes = ["light-theme", "dark-theme", "sepia-theme", "blue-theme", "onepiece-theme"];

// Appliquer le thème enregistré si présent
const savedTheme = localStorage.getItem("theme");
if (themes.includes(savedTheme)) {
  body.classList.add(savedTheme);
  if (selector) {
    selector.value = savedTheme;
  }
}

// Gérer le changement de thème
if (selector) {
  selector.addEventListener("change", (e) => {
    // Supprimer tous les anciens thèmes
    themes.forEach(theme => body.classList.remove(theme));
    
    const selected = e.target.value;
    body.classList.add(selected);
    localStorage.setItem("theme", selected);
  });
}
