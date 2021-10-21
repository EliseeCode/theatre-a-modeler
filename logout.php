<?php
/* Log out process, unsets and destroys session variables */
require 'db.php';
session_start();

if(isset($_SESSION["url"]))
{$url=$_SESSION["url"];}
else {$url="";}
session_unset();
session_destroy();
session_start();
if(file_exists ( 'vendor/autoload.php' ))
{
@require_once 'vendor/autoload.php';
// Get $id_token via HTTPS POST.
$CLIENT_ID=$_ENV["GOOGLE_CLIENT_ID"];
$client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
$client->revokeToken();
}
if($url!=""){$_SESSION["url"]=$url;}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>LogOut</title>
<script src='js/cookiesManager.js'></script>
</head>

<body>
    <script>
    eraseCookie("login");
    eraseCookie("hash");
    window.location='loginPage.php?logOut=1';
    </script>
</body>
</html>
