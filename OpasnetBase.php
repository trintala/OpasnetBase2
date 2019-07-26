<?php
# OBSOLETE
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/OpasnetBase2/OpasnetBase.php" );
EOT;
        exit( 1 );
}


$wgExtensionCredits['specialpage'][] = array(
	'name' => 'OpasnetBase',
	'author' => 'Einari Happonen, Juha Villman',
	'url' => 'http://www.mediawiki.org/wiki/Extension:OpasnetBase',
	'description' => 'OpasnetBase - Opasnet database user interface',
	'descriptionmsg' => 'Opasnet database user interface',
	'version' => '2.0.1',
);
 
$dir = dirname(__FILE__) . '/';
 
$wgAutoloadClasses['OpasnetBase'] = $dir . 'OpasnetBase_body.php'; # Tell MediaWiki to load the extension body.
//$wgAutoloadClasses['OpasnetBaseTags'] = $dir . 'OpasnetBase.tags.php';   #implements tags
$wgExtensionMessagesFiles['OpasnetBase'] = $dir . 'OpasnetBase.i18n.php';
$wgExtensionMessagesFiles['OpasnetBaseAlias'] = $dir . 'OpasnetBase.alias.php';
$wgSpecialPages['OpasnetBase'] = 'OpasnetBase'; # Let MediaWiki know about your new special page.

//$wgSpecialPageGroups['OpasnetBase'] = 'other';

$wgResourceModules['ext.OpasnetBase'] = array(
        // JavaScript and CSS styles. To combine multiple file, just list them as an array.
        'scripts' => 'modules/scripts.js',
        'styles' => 'modules/screen.css',
 
        // When your module is loaded, these messages will be available through mw.msg()
      //  'messages' => array( 'myextension-hello-world', 'myextension-goodbye-world' ),
 
        // If your scripts need code from other modules, list their identifiers as dependencies
        // and ResourceLoader will make sure they're loaded before you.
        // You don't need to manually list 'mediawiki' or 'jquery', which are always loaded.
        //'dependencies' => array( 'jquery' ),
 
        // ResourceLoader needs to know where your files are; specify your
        // subdir relative to "/extensions" (or $wgExtensionAssetsPath)
        'localBasePath' => dirname( __FILE__ ),
        'remoteExtPath' => 'OpasnetBase'
);

require_once($dir . 'config.php');   # Configuration
require_once($dir . 'OpasnetBase.parser.php');   # Parsers

?>