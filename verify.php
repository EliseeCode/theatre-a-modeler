<?php
/* Verifies registered user email, the link to this page
   is included in the register.php email message
*/
include_once ("db.php");
session_start();
include_once ("local_lang.php");
// Make sure email and hash variables aren't empty
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
{
    $email = $mysqli->escape_string($_GET['email']);
    $hash = $mysqli->escape_string($_GET['hash']);
    // Select user with matching email and hash, who hasn't verified their account yet (active = 0)
    //check if there is an account with that mail and hash.
    $result = $mysqli->query("SELECT * FROM users WHERE email='".$email."' AND hash='".$hash."' AND active='0'");
    if ( $result->num_rows == 0 )//regroupe plusieurs cas de fraude et le cas ou le compte a été déja activé.
    {
        $_SESSION['message'] = __("Le compte a déjà été validé !");
        header("location: loginPage.php");
    }
    else {

        // Set the user status to active (active = 1)
    $mysqli->query("UPDATE users SET active='1' WHERE email='$email'") or die($mysqli->error);
    $_SESSION['message'] = __("Félicitation, votre compte a été validé!");
    $_SESSION['active'] = 1;
        //======================================
    $user = $result->fetch_assoc();
    $result->free();
    $_SESSION["active"]=1;
    $_SESSION["user_id"]=$user["user_id"];
    header("location: loginSession.php");
    exit();
  }
}
else {
    $_SESSION['message'] = "Invalid parameters provided for account verification!";
    header("location: error.php");
}
?>
