<?php
	$lang_interface='fr';
	$local_lang_cause='default';
	if(isset($_SESSION['local_lang'])){$oldSessionLang=$_SESSION['local_lang'];}
	else{$oldSessionLang="";}
	if(isset($_GET['lang']))
		{
		$lang_interface=htmlspecialchars($_GET['lang']);
		$lang_interface=substr($lang_interface,0,2);
		//$_SESSION['local_lang']=$lang_interface;
		$local_lang_cause='get';
		}
	else
		{
			if(isset($_SESSION['local_lang']))
				{	$local_lang_cause='session';
					$lang_interface=$_SESSION['local_lang'];}
			else if(isset($_COOKIE["lang"])) {
				$local_lang_cause='cookie';
     		//$_SESSION['local_lang']=$_COOKIE["lang"];
				$lang_interface=$_COOKIE["lang"];
			}
			else
				{
					//pas de cookie, pas de get pas de session
					$local_lang_cause='browser';
					$lang_interface = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);
				}
		}
	//$acceptLang = ['fr', 'en', 'tr','it'];
	$lang_interface=substr($lang_interface,0,2);
	//$lang_interface = in_array($lang_interface, $acceptLang) ? $lang_interface : 'en';

$_SESSION['local_lang']=$lang_interface;
//require_once("gettext/lib/streams.php");
//require_once("gettext/lib/gettext.php");
//$locale_file=new FileReader("gettext/locale/".$local_lang."/".$local_lang.".mo");

//$locale_fetch=new gettext_reader($locale_file);
$translate_file=file_get_contents("gettext/".$lang_interface.".json");
$translate_data = json_decode($translate_file, true);

if (!function_exists('__'))
{
	//echo 'elle exist pas';
    function __($text){
			//return $text;
	global $translate_data;
	//return $locale_fetch->translate($text);
	 if(isset($translate_data[$text]))
	 {return $translate_data[$text];}
	 else
	 {return $text;}
	//return $locale_fetch->translate($text);
	}
}
?>
