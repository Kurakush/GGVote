
    <footer class="gg-footer">
    <div class="footer-content">
        <h2 class="footer-title">GGVote</h2>

        <p class="footer-desc">
            Plateforme de vote e-sport — Projet académique BUT Informatique.
        </p>

        <div class="footer-authors">
            <p>Développé par <strong>DEGRELLE Thomas</strong> & <strong>LACROIX Eve</strong></p>
            <p>2026 — Tous droits réservés.</p>
        </div>

        <div class="footer-links">
            <a href="index.php">Accueil</a>
            <a href="contact.php">Contact</a>
            <a href="mentions-legales.php">Mentions légales</a>
            <a href="cgu.php">CGU</a>
        </div>
    </div>
</footer>

<!-- Ancre pour remonter en haut de page -->
<a href="#" class="back-to-top" id="backToTop">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <path d="M12 19V5M12 5L6 11M12 5L18 11" stroke="#D9D9D9" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>

<!-- Script pour le bouton "Back to Top" -->
<script>
const backToTop = document.getElementById("backToTop");

    // Affichage / masquage du bouton
    window.addEventListener("scroll", () => {
        if (window.scrollY > 200) {
            backToTop.classList.add("show");
        } else {
            backToTop.classList.remove("show");
        }
    });

    // Scroll fluide vers le haut
    backToTop.addEventListener("click", (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>
</body>

