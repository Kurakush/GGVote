<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('dbconnect.php');

// check disconnect
if (isset($_GET["disconnect"]) && $_GET["disconnect"] == 1){
   // admin
    unset($_SESSION["login"]);
    unset($_SESSION["admin_id"]);

    // électeur
    unset($_SESSION["electeur_email"]);
    unset($_SESSION["idelecteur"]);

    // candidat
    unset($_SESSION["idjoueur_candidat"]);
    unset($_SESSION["candidat_email"]);
    unset($_SESSION["candidat_pseudo"]);
    unset($_SESSION["candidature_complete"]);
    unset($_SESSION["candidature_validee"]);

    $_SESSION['flash_message'] = "Vous êtes déconnecté.";
    header("Location: index.php");
    exit;
}

// ==========================
//  CONNEXION ADMIN
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['role'] ?? '') === 'admin') {

    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $_SESSION['flash_error'] = "Veuillez remplir tous les champs (Admin).";
        header("Location: index.php");
        exit;
    }

    $connexion = dbconnect();
    if (!$connexion) {
        $_SESSION['flash_error'] = "Erreur de connexion à la base de données.";
        header("Location: index.php");
        exit;
    }

    $sql = "SELECT * FROM admin WHERE login_admin = :login LIMIT 1";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([':login' => $login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['mot_de_passe'])) {
        $_SESSION['login']    = $admin['login_admin'];
        $_SESSION['admin_id'] = $admin['idadmin'];
        $_SESSION['flash_message'] = "Connexion réussie (Administrateur).";
    } else {
        $_SESSION['flash_error'] = "Identifiants incorrects (Admin).";
    }

    header("Location: index.php");
    exit;
}

// ==========================
//  CONNEXION ELECTEUR
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['role'] ?? '') === 'electeur') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['flash_error'] = "Veuillez remplir tous les champs (Électeur).";
        header("Location: index.php");
        exit;
    }

    $connexion = dbconnect();
    if (!$connexion) {
        $_SESSION['flash_error'] = "Erreur de connexion à la base de données.";
        header("Location: index.php");
        exit;
    }

    $sql = "SELECT * FROM electeur WHERE email = :email LIMIT 1";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([':email' => $email]);
    $electeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($electeur) {

        if ((int)$electeur['actif'] === 0) {
            $_SESSION['flash_error'] = "Votre compte électeur n'est pas actif.";
        }
        elseif (password_verify($password, $electeur['mot_de_passe'])) {
            $_SESSION['electeur_email'] = $electeur['email'];
            $_SESSION['idelecteur']     = $electeur['idelecteur'];
            $_SESSION['flash_message'] = "Connexion réussie (Électeur).";
        }
        else {
            $_SESSION['flash_error'] = "Identifiants incorrects (Électeur).";
        }

    } else {
        $_SESSION['flash_error'] = "Identifiants incorrects (Électeur).";
    }

    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['role'] ?? '') === 'candidat') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['flash_error'] = "Veuillez remplir tous les champs (Candidat).";
        header("Location: index.php");
        exit;
    }

    $connexion = dbconnect();
    if (!$connexion) {
        $_SESSION['flash_error'] = "Erreur de connexion à la base de données.";
        header("Location: index.php");
        exit;
    }

    $sql = "SELECT idjoueur, email_candidat, mdp_candidat, pseudo,
                   candidature_complete, candidature_validee
            FROM joueur
            WHERE email_candidat = :email
            LIMIT 1";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([':email' => $email]);
    $candidat = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($candidat && password_verify($password, $candidat['mdp_candidat'])) {

        $_SESSION['idjoueur_candidat']    = (int)$candidat['idjoueur'];
        $_SESSION['candidat_email']       = $candidat['email_candidat'];
        $_SESSION['candidat_pseudo']      = $candidat['pseudo'];
        $_SESSION['candidature_complete'] = (int)$candidat['candidature_complete'];
        $_SESSION['candidature_validee']  = (int)$candidat['candidature_validee'];

        $_SESSION['flash_message'] = "Connexion réussie (Candidat).";
    } else {
        $_SESSION['flash_error'] = "Identifiants incorrects (Candidat).";
    }

    header("Location: index.php");
    exit;
}




$admin    = isset($_SESSION["login"]);
$electeur = isset($_SESSION["electeur_email"]);
$candidat = isset($_SESSION["idjoueur_candidat"]);
?>

<html>
<head>
    <meta charset="utf-8">
    <title>GGVote</title>
    <link rel="stylesheet" href="ggvote.css">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;700&display=swap" rel="stylesheet">

    <script>
    // Affiche le modal de connexion
    function authenticate() {
        let modal = document.getElementById('loginModal');
        modal.style.display = 'flex';

        // Par défaut : onglet électeur
        switchTab('electeur');
    }

    // Déconnexion toujours sur index.php
    function disconnect() {
        window.location.href = "index.php?disconnect=1";
    }

    // Gestion des onglets
    function switchTab(role) {
        const tabElecteurBtn = document.getElementById('tabElecteur');
        const tabAdminBtn    = document.getElementById('tabAdmin');
        const tabCandidatBtn = document.getElementById('tabCandidat');

        const formElecteur   = document.getElementById('formElecteur');
        const formAdmin      = document.getElementById('formAdmin');
        const formCandidat   = document.getElementById('formCandidat');

        // Réinitialisation des onglets et contenus
        tabElecteurBtn.classList.remove('active');
        tabAdminBtn.classList.remove('active');
        tabCandidatBtn.classList.remove('active');

        formElecteur.classList.remove('active');
        formAdmin.classList.remove('active');
        formCandidat.classList.remove('active');

        if (role === 'electeur') {
            tabElecteurBtn.classList.add('active');
            formElecteur.classList.add('active');
        } else if (role === 'admin') {
            tabAdminBtn.classList.add('active');
            formAdmin.classList.add('active');
        } else if (role === 'candidat') {
            tabCandidatBtn.classList.add('active');
            formCandidat.classList.add('active');
        }
    }

    // Fermer le modal si clic en dehors
    window.onclick = function(event) {
        const modal = document.getElementById('loginModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function openInscription() {
        const modal = document.getElementById('inscriptionModal');
        modal.style.display = 'flex';
        switchSignupTab('electeur');
    }

    function closeInscription() {
        document.getElementById('inscriptionModal').style.display = 'none';
    }

    function switchInscriptionTab(type) {
        const tabE = document.getElementById('tabInscriptionElecteur');
        const tabC = document.getElementById('tabInscriptionCandidat');

        const paneE = document.getElementById('inscriptionElecteur');
        const paneC = document.getElementById('inscriptionCandidat');

        tabE.classList.remove('active');
        tabC.classList.remove('active');
        paneE.classList.remove('active');
        paneC.classList.remove('active');

        if (type === 'electeur') {
            tabE.classList.add('active');
            paneE.classList.add('active');
        } else {
            tabC.classList.add('active');
            paneC.classList.add('active');
        }
    }

    // Fermer la modale inscription si clic en dehors
    window.addEventListener('click', function(event) {
        const signup = document.getElementById('inscriptionModal');
        if (event.target === signup) signup.style.display = "none";
    });
    </script>
</head>

<body>
    <!-- Barre d'header -->
    <div class="navbar">
        <ul>
            <li class="logoGG"><img src="images/ggvoteSansFond.png" alt="Logo GGVOTE" height="100px"></li>

            <?php
            if ($admin || $electeur || $candidat){
                ?>
                <li style="float:right"><a href="#" onclick="disconnect()">DECONNEXION</a></li>
                <?php
            }
            else{
                ?>
                <li style="float:right"><a href="#" onclick="authenticate()">CONNEXION</a></li>
                <?php
            }
            ?>

            <?php
            if (!$admin && !$electeur && !$candidat){
                ?>
                <li style="float:right"><a href="#" onclick="openInscription()">INSCRIPTION</a></li>
                <?php
            }
            ?>


            <?php
            if ($admin) { 
                ?>
                <li style="float:right"><a href="admin/index.php">CONSOLE ADMIN</a></li>
                <?php 
            } 
            ?>

            <li style ="float:right"><a href="contact.php">CONTACT</a></li>
            <li style="float:right"><a href="resultat.php">RÉSULTATS</a></li>

            <?php if ($electeur || $admin) { ?>
            <li style="float:right"><a href="voter.php">VOTER</a></li>
            <?php } ?>

            <?php if ($electeur) { 
                ?>
                <li style="float:right"><a href="profil_electeur.php">ESPACE ELECTEUR</a></li>
                <?php 
            }
            ?>

            <?php if ($candidat) { 
                ?>
                <li style="float:right"><a href="espace_candidat.php">ESPACE CANDIDAT</a></li>
                <?php 
            }
            ?>

            
            <li style="float:right"><a href="index.php">ACCUEIL</a></li>
        </ul>
    </div>

    <!-- Modal de connexion (admin + électeur) -->
    <div id="loginModal" class="modal">
        <div class="modal-content animate">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('loginModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                <h1>Connectez-vous !</h1>
            </div>

            <!-- Onglets -->
            <div class="tab-menu">
                <button type="button" id="tabElecteur" class="active" onclick="switchTab('electeur')">Électeur</button>
                <button type="button" id="tabAdmin" onclick="switchTab('admin')">Admin</button>
                <button type="button" id="tabCandidat" onclick="switchTab('candidat')">Candidat</button>
            </div>

            <!-- Contenu onglet ÉLECTEUR -->
            <div id="formElecteur" class="tab-content active">
                <form class="dlgcontainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="role" value="electeur">

                    <label for="email"><b>Email</b></label>
                    <input type="email" placeholder="Entrez votre email" name="email" id="email" required>

                    <label for="psw_e"><b>Mot de passe</b></label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="password" id="psw_e" required>
                        
                    <button type="submit" class="okbtn">Connexion</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>

                    <?php if (!empty($error_electeur)) { ?>
                        <p style="color:#e31919;"><?php echo $error_electeur; ?></p>
                    <?php } ?>
                </form>
            </div>

            <!-- Contenu onglet ADMIN -->
            <div id="formAdmin" class="tab-content">
                <form class="dlgcontainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="role" value="admin">

                    <label for="login"><b>Login admin</b></label>
                    <input type="text" placeholder="Entrez votre login admin" name="login" id="login" required>

                    <label for="psw_a"><b>Mot de passe</b></label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="password" id="psw_a" required>
                        
                    <button type="submit" class="okbtn">Connexion</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>

                    <?php if (!empty($error_admin)) { ?>
                        <p style="color:#e31919;"><?php echo $error_admin; ?></p>
                    <?php } ?>
                </form>
            </div>

            <!-- Contenu onglet CANDIDAT -->
            <div id="formCandidat" class="tab-content">
                <form class="dlgcontainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <!-- Très important : indiquer qu’on est sur le rôle candidat -->
                    <input type="hidden" name="role" value="candidat">

                    <label for="email_c"><b>Email</b></label>
                    <!-- Le name doit être "email" pour correspondre au PHP -->
                    <input type="email" placeholder="Entrez votre email" name="email" id="email_c" required>

                    <label for="psw_c"><b>Mot de passe</b></label>
                    <!-- Le name doit être "password" pour correspondre au PHP -->
                    <input type="password" placeholder="Entrez votre mot de passe" name="password" id="psw_c" required>
            
                    <button type="submit" class="okbtn">Connexion</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>

                    <?php if (!empty($error_candidat)) { ?>
                        <p style="color:#e31919;"><?php echo $error_candidat; ?></p>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL INSCRIPTION (électeur + candidat) -->
    <div id="inscriptionModal" class="modal">
    <div class="modal-content animate">
        <div class="dlgheadcontainer">
            <span onclick="closeInscription()" class="close" title="Close Modal">&times;</span>
            <h1>Inscrivez-vous !</h1>
        </div>

        <!-- Onglets inscription -->
        <div class="tab-menu">
            <button type="button" id="tabInscriptionElecteur" class="active" onclick="switchInscriptionTab('electeur')">Électeur</button>
            <button type="button" id="tabInscriptionCandidat" onclick="switchInscriptionTab('candidat')">Candidat</button>
        </div>

        <!-- Contenu inscription ÉLECTEUR -->
        <div id="inscriptionElecteur" class="tab-content active">
            <div class="dlgcontainer">
                <p style="margin-bottom:16px;">
                    Créez un compte électeur (validation admin avant de voter).
                </p>

                <a href="inscription_electeur.php" class="okbtn" style="display:block;text-align:center;text-decoration:none;">
                    Continuer
                </a>
                <button type="button" onclick="closeInscription()" class="cancelbtn">Annuler</button>
            </div>
        </div>

        <!-- Contenu inscription CANDIDAT -->
        <div id="inscriptionCandidat" class="tab-content">
            <div class="dlgcontainer">
                <p style="margin-bottom:16px;">
                    Créez un compte candidat et choisissez votre compétition.
                </p>

                <a href="inscription_candidat.php" class="okbtn" style="display:block;text-align:center;text-decoration:none;">
                    Continuer
                </a>
                <button type="button" onclick="closeInscription()" class="cancelbtn">Annuler</button>
            </div>
        </div>
    </div>
</div>


    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-message success">
        <?= $_SESSION['flash_message']; ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="flash-message error">
        <?= $_SESSION['flash_error']; ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const radios = Array.from(document.querySelectorAll('input[id^="slide"]'));
    if (radios.length === 0) return;

    setInterval(() => {
        // On cherche le slide actuellement sélectionné
        const currentIndex = radios.findIndex(r => r.checked);
        const nextIndex = (currentIndex + 1) % radios.length;
        radios[nextIndex].checked = true;
    }, 5000); // 5000 ms = 5 secondes entre chaque image
});
</script>

        


