<table class='clean locations'>
<tr>
<td>
<?php
	$j = 0;
	$tmp = array();
	foreach($indices as $ind)
		$tmp[] = $ind->id;
	$ii = array_search($index_id, $tmp); # index index in index list!
	if (isset($locations->locations))
		foreach ($locations->locations as $lk=>$lv)
		{
			if ($excludes && isset($excludes[$ii]) && in_array($lk, $excludes[$ii]))
				$checked = '';
			else
				$checked = "checked='checked'";
			
			echo "<input onchange='mw.OpasnetBase.check_index(".$index_id.",this)' class='index_".$index_id." location_checkbox' name='selected_locs' type='checkbox' ".$checked." value='".$lk."'>".$lv."<br/>";
			$j++;
	
			if ($j % 15 == 0)
				echo "</td><td>";
		}
?>
</td>
</tr>
</table>

<div class='popup_actions'>
	<div class='check_options'>
	<a href='javascript: mw.OpasnetBase.check_all_locations(<?php echo $index_id;?>);'><?php echo wfMessage('text_check_all')->text(); ?></a>&nbsp;|
	<a href='javascript: mw.OpasnetBase.uncheck_all_locations(<?php echo $index_id;?>);'><?php echo wfMessage('text_uncheck_all')->text(); ?></a>
	</div>
	<input id='apply_dims_<?php echo $index_id; ?>' type='button' onclick='mw.OpasnetBase.apply_locations(<?php echo $index_id; ?>)' value='<?php echo wfMessage('text_apply')->text(); ?>' />
	<input type='button' onclick='mw.OpasnetBase.hide_options(<?php echo $index_id; ?>)' value='<?php echo wfMessage('text_cancel')->text(); ?>' />
</div>