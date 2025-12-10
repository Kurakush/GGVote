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
    unset($_SESSION["idcandidat"]);
    unset($_SESSION["candidat_email"]);
    unset($_SESSION["candidat_pseudo"]);
}

// ==========================
//  CONNEXION ADMIN
// ==========================
if (isset($_POST['role']) && $_POST['role'] === 'admin') {

    if (!empty($_POST['login']) && !empty($_POST["password"])) {

        $connexion = dbconnect(); 
        if(!$connexion) {
            echo "Pb d'accès à la bdd"; 
        }
        else {

            // On récupère l’admin par son login
            $sql = "SELECT * FROM admin WHERE login_admin = :login LIMIT 1";
            $query = $connexion->prepare($sql);
            $query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
            $query->execute();
            $member_row = $query->fetch(PDO::FETCH_ASSOC);

            $password_ok = false;

            if ($member_row) {
                $hash_en_bdd = $member_row['mot_de_passe'];
                $mdp_saisi   = $_POST['password'];

                // Vérification du mot de passe hashé
                if (password_verify($mdp_saisi, $hash_en_bdd)) {
                    $password_ok = true;
                }
            }

            if ($password_ok) {
                $_SESSION['login']    = $member_row['login_admin'];
                $_SESSION['admin_id'] = $member_row['idadmin'];
                $_SESSION['flash_message'] = "Connexion réussie en tant qu'administrateur !";
            } else {
                $_SESSION['flash_error'] = "Identifiants incorrects (Admin)";
            }
            
        }
        $connexion = null;
    }
}

// ==========================
//  CONNEXION ELECTEUR
// ==========================
if (isset($_POST['role']) && $_POST['role'] === 'electeur') {

    if (!empty($_POST['email']) && !empty($_POST["password"])){

        $connexion = dbconnect(); 
        if(!$connexion) {
            echo "Pb d'accès à la bdd"; 
        }
        else{

            // On récupère l'électeur par son email
            $sql = "SELECT * FROM electeur WHERE email = :email LIMIT 1";
            $query = $connexion->prepare($sql);
            $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $query->execute();
            $member_row = $query->fetch(PDO::FETCH_ASSOC);

            $password_ok = false;

            if ($member_row) {
                $hash_en_bdd = $member_row['mot_de_passe'];
                $mdp_saisi   = $_POST['password'];

                // Vérification du mot de passe hashé
                if (password_verify($mdp_saisi, $hash_en_bdd)) {
                    $password_ok = true;
                }
            }

            if ($password_ok) {
                $_SESSION['electeur_email'] = $member_row['email'];
                $_SESSION['idelecteur']     = $member_row['idelecteur'];
                $_SESSION['flash_message']  = "Connexion réussie en tant qu'électeur !";

                $idelecteur = (int)$member_row['idelecteur'];

                // =======================================
                // GENERATION DES TOKENS PAR COMPETITION
                // =======================================

                // On récupère les compétitions qui ont AU MOINS un scrutin ouvert
                $sqlC = "SELECT DISTINCT c.idcompetition
                    FROM scrutin s
                    JOIN competition c ON c.idcompetition = s.idcompetition
                    WHERE s.etat_scrutin = 'ouvert'
                        AND s.date_ouverture <= NOW()
                        AND s.date_cloture   >= NOW()";
                $stmtC = $connexion->query($sqlC);
                $competitions = $stmtC->fetchAll(PDO::FETCH_ASSOC);

                foreach ($competitions as $comp) {
                $idcompetition = (int)$comp['idcompetition'];

                // Vérifier si un token existe déjà pour cet électeur et cette compétition
                $sqlCheck = "SELECT idtoken
                    FROM token
                    WHERE idelecteur = :idelecteur
                       AND idcompetition = :idcompetition";
                $stmtCheck = $connexion->prepare($sqlCheck);
                $stmtCheck->execute([
                    ':idelecteur'   => $idelecteur,
                    ':idcompetition'=> $idcompetition
            ]);

            if ($stmtCheck->rowCount() == 0) {

            // Génération du jeton
            $token_code = bin2hex(random_bytes(16));
            $token_hash = password_hash($token_code, PASSWORD_DEFAULT);

            $sqlInsert = "INSERT INTO token (code_token, token_hash, idelecteur, idcompetition, etat, date_generation)
                          VALUES (:code, :hash, :idelecteur, :idcompetition, 0, NOW())";
            $stmtI = $connexion->prepare($sqlInsert);
            $stmtI->execute([
                ':code'         => $token_code,
                ':hash'         => $token_hash,
                ':idelecteur'   => $idelecteur,
                ':idcompetition'=> $idcompetition
]);

            // Optionnel : affichage dans la session
            $_SESSION['token_comp_'.$idcompetition] = $token_code;
        }
    }

    } else {
        $_SESSION['flash_error'] = "Identifiants incorrects (Électeur)";
    }
        }
        $connexion = null;
    }
}

/* ==========================================================
 *  CONNEXION CANDIDAT
 *  (via le formulaire / modal avec role="candidat")
 * ========================================================== */
if (isset($_POST['role']) && $_POST['role'] === 'candidat') {

    // On vérifie que les champs sont remplis
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['flash_error'] = "Veuillez saisir un email et un mot de passe (Candidat).";
    } else {

        $connexion = dbconnect();
        if (!$connexion) {
            $_SESSION['flash_error'] = "Problème d'accès à la base de données (Candidat).";
        } else {

            // On cherche le candidat dans la table candidat_user
            $sql = "SELECT idcandidat, email, mdp_hash, pseudo, candidature_complete, candidature_validee
                    FROM candidat_user
                    WHERE email = :email
                    LIMIT 1";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([':email' => $email]);
            $candidat = $stmt->fetch(PDO::FETCH_ASSOC);

            $password_ok = false;

            if ($candidat && !empty($candidat['mdp_hash'])) {
                if (password_verify($password, $candidat['mdp_hash'])) {
                    $password_ok = true;
                }
            }

            if ($password_ok) {

                // Variables de session pour l'espace candidat
                $_SESSION['idcandidat']        = (int)$candidat['idcandidat'];
                $_SESSION['candidat_email']    = $candidat['email'];
                $_SESSION['candidat_pseudo']   = $candidat['pseudo'];
                $_SESSION['flash_message']     = "Connexion réussie en tant que candidat.";

                // Redirection vers l'espace candidat
                header("Location: espace_candidat.php");
                exit;

            } else {
                $_SESSION['flash_error'] = "Identifiants incorrects (Candidat).";
            }

            $connexion = null;
        }
    }
}



// set admin / electeur vars
$admin    = isset($_SESSION["login"]);
$electeur = isset($_SESSION["electeur_email"]);
$candidat = isset($_SESSION["idcandidat"]);
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

    // Déconnexion
    function disconnect() {
        window.location.href = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" + '?disconnect=1';
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
    </script>
</head>

<body>
    <!-- Barre d'header -->
    <div class="navbar">
        <ul>
            <li class="logoGG"><img src="images/ggvoteSansFond.png" alt="Logo GGVOTE" height="100px"></li>

            <?php
            if ($admin || $electeur){
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
                <li style="float:right"><a href="profil_electeur.php">MON PROFIL</a></li>
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
                <form class="dlgcontainer" action="login_candidat.php" method="post">
                    <label for="email_c"><b>Email</b></label>
                    <input type="email" placeholder="Entrez votre email" name="email_c" id="email_c" required>

                    <label for="psw_c"><b>Mot de passe</b></label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="password_c" id="psw_c" required>
                        
                    <button type="submit" class="okbtn">Connexion</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>
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

        


