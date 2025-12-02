<?php 
require('dbconnect.php');

//Load Session Variables
session_start();

// check disconnect
if (isset($_GET["disconnect"]) && $_GET["disconnect"] == 1){
    unset($_SESSION["login"]);            // admin
    unset($_SESSION["electeur_email"]);   // électeur
    unset($_SESSION["idelecteur"]);
}

// ==========================
//  CONNEXION ADMIN
// ==========================
if (isset($_POST['role']) && $_POST['role'] === 'admin') {

    if (isset($_POST['login']) && isset($_POST["password"])){

        $connexion = dbconnect(); 
        if(!$connexion) {
            echo "Pb d'accès à la bdd"; 
        }
        else{

            // Ton code de base, légèrement simplifié
            $sql = "SELECT * FROM admin WHERE login_admin = :login AND mot_de_passe = :password";
            $query = $connexion->prepare($sql);
            $query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
            $query->bindValue(':password', $_POST['password'], PDO::PARAM_STR);
            $query->execute();
            $members_array = $query->fetchAll();

            $row_count = count($members_array);

            if($row_count == 1) {
                $member_row = $members_array[0];
                $_SESSION['login'] = $member_row['login_admin'];
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

    if (isset($_POST['email']) && isset($_POST["password"])){

        $connexion = dbconnect(); 
        if(!$connexion) {
            echo "Pb d'accès à la bdd"; 
        }
        else{

            $sql = "SELECT * FROM electeur WHERE email = :email AND mot_de_passe = :password";
            $query = $connexion->prepare($sql);
            $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $query->bindValue(':password', $_POST['password'], PDO::PARAM_STR);
            $query->execute();
            $members_array = $query->fetchAll();

            if(count($members_array) == 1) {
                $member_row = $members_array[0];
                $_SESSION['electeur_email'] = $member_row['email'];
                $_SESSION['idelecteur']     = $member_row['idelecteur'];
                $_SESSION['flash_message'] = "Connexion réussie en tant qu'électeur !";
            } else {
                $_SESSION['flash_error'] = "Identifiants incorrects (Électeur)";
            }
        }
        $connexion = null;
    }
}

// set admin / electeur vars
$admin    = isset($_SESSION["login"]);
$electeur = isset($_SESSION["electeur_email"]);
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
        const formElecteur   = document.getElementById('formElecteur');
        const formAdmin      = document.getElementById('formAdmin');

        tabElecteurBtn.classList.remove('active');
        tabAdminBtn.classList.remove('active');
        formElecteur.classList.remove('active');
        formAdmin.classList.remove('active');

        if (role === 'electeur') {
            tabElecteurBtn.classList.add('active');
            formElecteur.classList.add('active');
        } else {
            tabAdminBtn.classList.add('active');
            formAdmin.classList.add('active');
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
            <li style ="float:right"><a href="contact.php">CONTACT</a></li>
            <li style="float:right"><a href="resultat.php">RÉSULTATS</a></li>
            <li style="float:right"><a href="voter.php">VOTER</a></li>
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
                    <input type="text" placeholder="Entrez votre identifiant" name="login" id="login" required>

                    <label for="psw_a"><b>Mot de passe</b></label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="password" id="psw_a" required>
                        
                    <button type="submit" class="okbtn">Connexion</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>

                    <?php if (!empty($error_admin)) { ?>
                        <p style="color:#e31919;"><?php echo $error_admin; ?></p>
                    <?php } ?>
                </form>
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

        


