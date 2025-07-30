import './js/script.js';
import './styles/app.css'; 

console.log('JS chargé avec succès !');

/*
 * Thèmes multiples (clair, sombre, sépia, bleu, One Piece)
 */
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

// --- Stripe Payment Form JS ---

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  submitButton.disabled = true;
  spinner.classList.remove('d-none');
  submitText.textContent = 'Paiement en cours...';
  cardErrors.textContent = '';

  // Timeout pour éviter blocage long (ex: 30s)
  const timeoutPromise = new Promise((_, reject) =>
    setTimeout(() => reject(new Error('Timeout réseau, merci de réessayer')), 30000)
  );

  try {
    const { paymentMethod, error } = await Promise.race([
      stripe.createPaymentMethod({ type: 'card', card }),
      timeoutPromise,
    ]);

    if (error) throw error;

    const response = await Promise.race([
      fetch(form.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ paymentMethodId: paymentMethod.id }),
      }),
      timeoutPromise,
    ]);

    const result = await response.json();

    if (result.error) throw new Error(result.error);

    if (result.requiresAction) {
      const confirmation = await Promise.race([
        stripe.confirmCardPayment(result.paymentIntentClientSecret),
        timeoutPromise,
      ]);

      if (confirmation.error) throw confirmation.error;

      window.location.href = result.redirect ?? '/commande/confirmation';
    } else if (result.success) {
      window.location.href = result.redirect;
    } else {
      throw new Error('Erreur inconnue lors du paiement.');
    }
  } catch (err) {
    cardErrors.textContent = err.message;
    spinner.classList.add('d-none');
    submitText.textContent = 'Payer';
    submitButton.disabled = false;
  }
});
