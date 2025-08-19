// Point d’entrée de notre application React

// 📦 Importation des dépendances principales
import React from "react";
import ReactDOM from "react-dom/client";

// 🧩 Composant principal de l'application
import App from "../../mon_app/src/components/Category/CategoryCard.jsx";

// 🎨 Fichier de styles (CSS global de l'app)
import "./index.css";

// 🔄 Sélection de l’élément racine dans le DOM (doit exister dans ton HTML ou Twig)
const rootElement = document.getElementById("root");

if (rootElement) {
  // 🚀 Montage du composant App dans le DOM via React 18 (createRoot)
  ReactDOM.createRoot(rootElement).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
} else {
  // ⚠️ Message d’erreur si aucun élément avec l’id "root" n’a été trouvé
  console.error("L'élément avec l'id 'root' est introuvable dans le DOM.");
}
