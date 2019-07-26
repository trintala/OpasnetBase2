<?php
	#require_once dirname(__FILE__).'/lib/OpasnetObject.class.php';

	#$wgHooks['ParserFirstCallInit'][] = 'OpasnetBaseParser::efOpasnetBaseInit';
	#$wgExtensionMessagesFiles['OpasnetBase'] = __DIR__ . '/OpasnetBase.i18n.php';
require_once(dirname(__FILE__) . '/config.php');   # Configuration

class OpasnetBaseParser {
	public static function efOpasnetBaseInit(&$parser)
	{
		$parser->setFunctionHook( 'opasnet_base_link', 'OpasnetBaseParser::efOpasnetBaseLink_Render' );
		return true;
	}
	 	
	public static function efOpasnetBaseLink_Render( &$parser, $param1 )
	{
		global $obcPageUrl;
		
		# Connect opasnet base database
		#$connection = new OpasnetConnection();
		try
		{
			$idents = OpasnetObject::idents($param1);
		}
		catch (Exception $e)
		{
			return wfMessage('text_parser_no_results')->text();
		}
				
		if (empty($idents)) return '';
		
		# Disconnect the database
		# $connection->disconnect();

		// Nothing exciting here, just escape the user-provided
		// input and throw it back out again
		//return array('Show results from the [[Special:Opasnet_Base?id='.$param1.'|Opasnet base]]','isHTML');
		return array('<a href="'.$obcPageUrl.'?id='.$param1.'">' . wfMessage('text_parser_show_results')->text() . '</a>', 'isHTML' => true);
	}

}
?>
