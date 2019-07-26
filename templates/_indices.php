<?php

	if (is_array($indices))
	{
		$i = 1;
		$ind_ids = array();
	
		foreach($indices as $ind)
		{
			echo "<a href='javascript: mw.OpasnetBase.show_dimension({$ind->id});'>".$ind->name."</a>";		
			if ($i++ < count($indices)) echo "&nbsp; | &nbsp;";
			echo "<div id='dimension_options_{$ind->id}' class='popup'>";
			echo "</div>";
			$ind_ids[] = $ind->id;
		}
		
		(isset($get_vars['sr']) && $get_vars['sr'] == '1') ? $sr = true : $sr = false;
?>

	<form name='indices_form' action='' method='post'>
		<input type='hidden' name='indices' value='<?php echo join($ind_ids,','); ?>' />
	</form>

<?php

	}
	else
		echo wfMessage('error_invalid_index_data')->text();



?>