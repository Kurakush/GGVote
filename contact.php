<?php
require('header.php');
$connexion=dbconnect(); 


$sql = "SELECT * FROM admin WHERE contact=1";
$query = $connexion->prepare($sql);
$query->execute();
$row_count = $query->rowCount();

$message_ok ="";
$message_erreur="";

// Check the number of rows that match the SELECT statement 
if($row_count!=1) 
{
  echo "Pb d'accès à la bdd"; 
}
else{ 
    $row = $query->fetch();
    $email = $row["email"];

    // Traitement du formulaire
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // récupération et validation des données
        $nom = htmlspecialchars($_POST['nom']);
        $email_user = htmlspecialchars($_POST['email']);
        $sujet = htmlspecialchars($_POST['sujet']);
        $message = htmlspecialchars($_POST['message']);
        
        // Validation des champs
        if (empty($nom) || empty($email_user) || empty($sujet) || empty($message)) {
            $message_erreur = "Tous les champs doivent être remplis.";
        } else {
            // Envoyer l'email
            $to = $email;
            $subject = "Contact GGVote: " . $sujet;
            $body = "Nom: " . $nom . "\n";
            $body .= "Email: " . $email_user . "\n\n";
            $body .= "Message:\n" . $message;

            $headers = "From: " . $email_user . "\r\n";
            $headers .= "Reply-To: " . $email_user . "\r\n";

            if (mail($to, $subject, $body, $headers)) {
                $message_ok = "Votre message a été envoyé avec succès.";
            } else {
                $message_erreur = "Une erreur est survenue lors de l'envoi de votre message.";
            }
        }
    }

    $connexion = null;
  ?> 

    <div class="main">

    <h1>Nous contacter</h1>
    <p align="center">Avez-vous des questions sur GGVote ?</p>

        <div class="formcontact">
            <form action="contact.php" method="post">
                <label for="fname">Votre nom : </label>
                <input type="text" id="fname" name="nom" placeholder="Votre nom..">

                <label for="email">Votre email : </label>
                <input type="email" id="email" name="email" placeholder="Votre email..">


                <label for="sujet">Sujet : </label>
                <input type="text" id="sujet" name="sujet" placeholder="Le sujet de votre message..">


                <label for="message">Votre message : </label>
                <textarea id="message" name="message" placeholder="Ecrivez ici.." style="height:200px"></textarea>

                <input type="submit" class="okbtn" value="Envoyer">
            </form>
        </div>
&nbsp;
    </div>

<!--Section: Contact v.2-->
<?php
}


require('footer.php');
?>