import './js/script.js';
import './styles/app.css'; 

console.log('JS chargé avec succès !');
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// ========================
// Thème sombre / clair
// ========================
const themeToggle = document.getElementById("toggle-theme");
const body = document.body;

// Charger le thème sauvegardé si présent
if (localStorage.getItem("theme") === "dark") {
  body.classList.add("dark-mode");
}

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    const isDark = body.classList.contains("dark-mode");
    localStorage.setItem("theme", isDark ? "dark" : "light");
  });
}
