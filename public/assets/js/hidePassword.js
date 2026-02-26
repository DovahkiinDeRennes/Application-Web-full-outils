const copiables = document.querySelectorAll('.copiable');

copiables.forEach(el => {
el.addEventListener('click', () => { // Récupère la valeur à copier
const textToCopy = el.getAttribute('data-value');

// Copie dans le presse-papiers
navigator.clipboard.writeText(textToCopy).then(() => {}).catch(err => {
console.error('Erreur lors de la copie :', err);
});
});
});