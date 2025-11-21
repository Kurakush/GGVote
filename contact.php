<?php
require('header.php');
$connexion=dbconnect(); 


$sql = "SELECT * FROM admin WHERE contact=1";
$query = $connexion->prepare($sql);
$query->execute();
$row_count = $query->rowCount();

// Check the number of rows that match the SELECT statement 
if($row_count!=1) 
{
  echo "Pb d'accès à la bdd"; 
}
else{ 
    $row = $query->fetch();
    $email = $row["email"];

    $connexion = null;
  ?> 

    <div class="main">

    <h1>Nous contacter</h1>
    <p align="center">Avez-vous des questions sur GGVote ?</p>

        <div class="formcontact">
            <form action="/action_page.php">
                <label for="fname">Votre nom : </label>
                <input type="text" id="fname" name="firstname" placeholder="Votre nom..">

                <label for="lname">Votre email : </label>
                <input type="text" id="lname" name="lastname" placeholder="Votre email..">


                <label for="lname">Sujet : </label>
                <input type="text" id="lname" name="lastname" placeholder="Le sujet de votre message..">


                <label for="subject">Votre message : </label>
                <textarea id="subject" name="subject" placeholder="Ecrivez ici.." style="height:200px"></textarea>

                <input type="submit" class="okbtn" value="Submit">
            </form>
        </div>
&nbsp;
    </div>

<!--Section: Contact v.2-->
<?php
}


require('footer.php');
?>