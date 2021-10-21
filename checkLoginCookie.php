<?php
session_start();
?>
<script src="js/cookiesManager.js"></script>
<script>
if(readCookie("login") && readCookie("hash"))
{
newAdress="loginCookie.php?login="+readCookie("login")+"&hash="+readCookie("hash");
window.location=newAdress;
}else
{window.location="logout.php";}
</script>
