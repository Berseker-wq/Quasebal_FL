import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener("DOMContentLoaded", () => {
  const bouton = document.getElementById("envoyer");

  const champs = [
    { id: "nom", erreurId: "erreurNom", message: "Le nom est obligatoire." },
    { id: "prenom", erreurId: "erreurPrenom", message: "Le prénom est obligatoire." },
    { id: "email", erreurId: "erreurEmail", message: "L'email est obligatoire et doit être valide." },
    { id: "telephone", erreurId: "erreurTelephone", message: "Le téléphone est obligatoire et doit contenir uniquement des chiffres." },
    { id: "adresse", erreurId: "erreurDemande", message: "L'adresse est obligatoire." },
  ];

  if (bouton) {
    bouton.addEventListener("click", (event) => {
      let valide = true;

      champs.forEach(({ id, erreurId, message }) => {
        const champ = document.getElementById(id);
        const erreur = document.getElementById(erreurId);

        if (!champ || !erreur) return;

        const valeur = champ.value.trim();

        if (!valeur) {
          erreur.textContent = message;
          erreur.style.display = "block";
          valide = false;
          return;
        } else {
          erreur.style.display = "none";
        }

        if (id === "email") {
          const regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
          if (!regexEmail.test(valeur)) {
            erreur.textContent = "Veuillez entrer un email valide (exemple: user@domain.com).";
            erreur.style.display = "block";
            valide = false;
          }
        }

        if (id === "telephone") {
          if (!/^\d+$/.test(valeur)) {
            erreur.textContent = "Le numéro de téléphone doit être composé uniquement de chiffres.";
            erreur.style.display = "block";
            valide = false;
          }
        }
      });

      if (!valide) {
        event.preventDefault();
      } else {
        alert("Formulaire envoyé avec succès !");
      }
    });
  }

  // Effacer les erreurs au fur et à mesure
  champs.forEach(({ id, erreurId }) => {
    const champ = document.getElementById(id);
    const erreur = document.getElementById(erreurId);

    champ?.addEventListener("input", () => {
      if (champ.value.trim()) {
        erreur.style.display = "none";
      }
    });
  });

  // Redémarrage automatique de la vidéo
  const video = document.getElementById("video");
  video?.addEventListener("ended", () => {
    video.currentTime = 0;
    video.play();
  });
});
