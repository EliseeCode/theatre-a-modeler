<div id="cookieConsent">
    <div id="closeCookieConsent">x</div>
    <?php echo __("Ce site internet utilise des cookies.");?> <a href="cookiePolicy.php" target="_blank"><?php echo __("Plus d'informations");?></a>. <a class="cookieConsentOK" onclick="cookieOK();"><?php echo __("C'est bon");?></a>
</div>

<style>
/*Cookie Consent Begin*/
#cookieConsent {
    background-color: rgba(20,20,20,0.8);
    min-height: 26px;
    font-size: 14px;
    color: #ccc;
    line-height: 26px;
    padding: 8px 0 8px 30px;
    font-family: "Trebuchet MS",Helvetica,sans-serif;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    display: none;
    z-index: 9999;
}
#cookieConsent a {
    color: #4B8EE7;
    text-decoration: none;
}
#closeCookieConsent {
    float: right;
    display: inline-block;
    cursor: pointer;
    height: 20px;
    width: 20px;
    margin: -15px 0 0 0;
    font-weight: bold;
}
#closeCookieConsent:hover {
    color: #FFF;
}
#cookieConsent a.cookieConsentOK {
    background-color: #F1D600;
    color: #000;
    display: inline-block;
    border-radius: 5px;
    padding: 0 20px;
    cursor: pointer;
    float: right;
    margin: 0 60px 0 10px;
}
#cookieConsent a.cookieConsentOK:hover {
    background-color: #E0C91F;
}
/*Cookie Consent End*/
</style>
<script>
function cookieOK(){
  gtag('js', new Date());
  gtag('config', 'UA-140408884-1');
  createCookie("lang","<?php echo $lang_interface;?>",365);
  createCookie("cookieOK","ok",365);
}

$(document).ready(function(){
  if(readCookie("cookieOK")!="ok")
  {
    setTimeout(function () {
        $("#cookieConsent").fadeIn(200);
     }, 3000);
    $("#closeCookieConsent, .cookieConsentOK").click(function() {
        cookieOK();
        $("#cookieConsent").fadeOut(200);
    });
  }
  else{
    cookieOK();
  }
});

</script>
