<table class='clean locations'>
<tr>
<td>
	<?php echo wfMessage('text_from')->text(); ?>:&nbsp;
</td>
<td>
<?php
	if (isset($ranges[$index->ident]))
		$min = $ranges[$index->ident][0];
	else
		$min = '';
	echo "<input type='text' value='".$min."' class='index_".$index_id." number_range_from' name='number_range_from'><br/>";			
?>
</td>
</tr>
<tr>
<td>
<?php echo wfMessage('text_to')->text();?>:&nbsp;
</td>
<td>
<?php
	if (isset($ranges[$index->ident]))
		$max = $ranges[$index->ident][1];
	else
		$max = '';
	echo "<input type='text' value='".$max."' class='index_".$index_id." number_range_to' name='number_range_to'><br/>";
?>
</td>
</tr>
</table>

<div class='popup_actions'>
	<input id='apply_dims_<?php echo $index_id; ?>' type='button' onclick='mw.OpasnetBase.apply_number_range(<?php echo $index_id; ?>)' value='<?php echo wfMessage('text_apply')->text(); ?>' />
	<input type='button' onclick='mw.OpasnetBase.hide_options(<?php echo $index_id; ?>)' value='<?php echo wfMessage('text_cancel')->text(); ?>' />
</div>