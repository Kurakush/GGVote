<?php 
require('dbconnect.php');

//Load Session Variables
session_start();

//check disconnect
if (isset($_GET["disconnect"])){
    if ($_GET["disconnect"]==1){
        unset($_SESSION["login"]);
    }
}

//check credentiels
if (isset($_POST['login'])){
    if (isset($_POST["password"])){
        
        $sql="SELECT COUNT(*) FROM admin"; 

        $connexion=dbconnect(); 
        if(!$connexion->query($sql)) {
            echo "Pb d'accès à la bdd"; 
        }
        else{
            
            /* Query Prepare */
            $sql = "SELECT * FROM admin WHERE login_admin = :login AND mot_de_passe =:password";
            $query = $connexion->prepare($sql);
            $query->bindValue(':login', $_POST['login'], PDO::PARAM_STR);
            $query->bindValue(':password', $_POST['password'], PDO::PARAM_STR);
            $query->execute();
            $members_array = $query->fetchAll();

            $row_count = count($members_array);

            // Check the number of rows that match the SELECT statement 
            if($row_count==1) 
            {
                $member_row = $members_array[0];
                $_SESSION['login'] = $member_row['login'];
            }
        }
        $connexion=null;
    }
}

//set admin var = true if user is logged
$admin = false;
if (isset($_SESSION["login"])){
    $admin = true;

    //coucou
}

?>

<html>
<head>
    <meta charset="utf-8">
    <title>GGVote</title>
    <link rel="stylesheet" href="ggvote.css">
    <script>
    /**
     * Display authentication modal form : 
     */
    function authenticate() {
        // Display loginModal div and display it
        let modal = document.getElementById('loginModal');
        modal.style.display='block';
    }

    /**
     * Disconnection
     */
    function disconnect() {
        window.location.href = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" + '?disconnect=1';
    }
  </script>
</head>

<body>
    <!-- Barre d'header -->
    <div class="navbar">
        <ul>
            <li class="logoGG"><img src="ggvoteSansFond.png" alt = "Logo GGVOTE" height="100px"></li>

            <?php
            if ($admin){
                ?>
                <li style="float:right"><a href="#" onclick="disconnect()">Déconnexion</a></li>
                <?php
            }
            else{
                ?>
                <li style="float:right"><a href="#" onclick="authenticate()">Connexion</a></li>
                <?php
            }
            ?>
        </ul>
    </div>

    <!-- Authentication pour les différents espaces -->
    <div id="loginModal" class="modal">
  
        <form id="loginForm" class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('loginModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1>Connectez vous !</h1>
            </div>

            <div class="dlgcontainer">
                <label for="uname"><b>Login</b></label>
                <input type="text" placeholder="Entrez votre identifiant" name="login" id="login" required>

                <label for="psw"><b>Mot de passe</b></label>
                <input type="password" placeholder="Entrez votre mot de passe" name="password" id="password" required>
                    
                <button type="submit" class="okbtn">Connexion</button>
                <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>

            </div>

        </form>
    </div>
        
</body>

