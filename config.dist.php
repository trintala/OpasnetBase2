<?php
	global $wgArticlePath, $wgScript;
	
	// Url to special page
	if (isset($wgArticlePath) && ! empty($wgArticlePath))
        define('obcPageUrl', str_replace('$1','Special:Opasnet_Base',$wgArticlePath)); #$obcPageUrl = str_replace('$1','Special:Opasnet_Base',$wgArticlePath);
    else
        define('obcPageUrl', $wgScript.'/Special:Opasnet_Base');#$obcPageUrl = $wgScript.'/Special:Opasnet_Base';	
 
	if (! defined('OB_INTERFACE_URL'))
		define('OB_INTERFACE_URL', 'http://'.''.'/opasnet_base_2/index.php'); // modify to the appropriate OpasnetBase2 server url
 	
 	$obcDebug = true;
 	
 	define('obcDatabaseUsername', ''); // user name matching this wiki in OpasnetBase2 user database
 	define('obcDatabasePassword', ''); // corresponding password/secret 
 	
?>
