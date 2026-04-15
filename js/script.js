document.addEventListener('DOMContentLoaded', function () {

    // ===== AUTO-FERMETURE DES MESSAGES =====
    const messages = document.querySelectorAll('.message');
    messages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(function () { msg.remove(); }, 500);
        }, 4000);
    });

// ===== RECHERCHE EN TEMPS RÉEL =====
const searchInput = document.querySelector('.recherche-bar input');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const valeur = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.livre-card');
        cards.forEach(function (card) {
            const titre = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
            const auteurEl = card.querySelector('.auteur');
            const auteur = auteurEl ? auteurEl.textContent.toLowerCase() : '';
            if (valeur === '' || titre.includes(valeur) || auteur.includes(valeur)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

    // ===== COMPTEUR DE CARACTÈRES POUR LES AVIS =====
    const textarea = document.querySelector('textarea[name="commentaire"]');
    if (textarea) {
        const maxLength = 500;
        const counter = document.createElement('p');
        counter.style.cssText = 'font-size:0.75rem; color:#b08a84; text-align:right; margin-top:4px;';
        counter.textContent = `0 / ${maxLength} caractères`;
        textarea.parentNode.appendChild(counter);
        textarea.setAttribute('maxlength', maxLength);
        textarea.addEventListener('input', function () {
            const len = this.value.length;
            counter.textContent = `${len} / ${maxLength} caractères`;
            counter.style.color = len > maxLength * 0.9 ? '#c47a6a' : '#b08a84';
        });
    }

    // ===== BOUTON RETOUR EN HAUT =====
    const btnTop = document.createElement('button');
    btnTop.textContent = '↑';
    btnTop.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #c47a6a;
        color: white;
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        font-size: 1.2rem;
        cursor: pointer;
        display: none;
        box-shadow: 0 4px 15px rgba(196,122,106,0.4);
        z-index: 999;
        transition: all 0.2s;
    `;
    document.body.appendChild(btnTop);
    window.addEventListener('scroll', function () {
        btnTop.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    btnTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

});

// Attendre que tout le contenu HTML soit chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // Gestion de la confirmation de suppression
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Récupérer le titre du livre stocké dans l'attribut data-titre
            const titre = this.getAttribute('data-titre');
            
            // Afficher la boîte de dialogue
            const confirmation = confirm(`Êtes-vous sûr de vouloir supprimer définitivement le livre "${titre}" ?`);
            
            // Si l'utilisateur clique sur "Annuler", on bloque l'action de suppression 
            if (!confirmation) {
                event.preventDefault();
            }
        });
    });

});