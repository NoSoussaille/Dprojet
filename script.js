// Récupérer les éléments
const burgerMenu = document.getElementById('burger-menu');
const navMenu = document.getElementById('nav-menu');
const menuItems = document.querySelectorAll('.menu-item');

// Ajouter un événement pour ouvrir/fermer le menu principal
burgerMenu.addEventListener('click', () => {
    const mainNav = navMenu.querySelector('.main-nav');
    mainNav.classList.toggle('active'); // Afficher/masquer le menu
});

// Supprime le paramètre "error" de l'URL après affichage
if (window.location.search.includes("error=acces_refuse")) {
    const url = new URL(window.location);
    url.searchParams.delete("error");
    window.history.replaceState({}, document.title, url.toString());
}
