<span style='float: right;'><a href='?'><?php echo wfMessage('text_variable_list')->text(); ?></a></span>
<h2><?php echo $obj->name; ?></h2> 
<div class='dataset_selector_container'>
<?php
foreach ($idents as $ident => $name)
if (OpasnetObject::t2b_check($ident))
{
	if (count(explode('.',$ident)) > 1)
		$label = $name;
	else
		$label = wfMessage('text_main')->text();

	echo "<div class='dataset_selector ".(strtolower($ident) == strtolower($obj->ident) ? 'selected' : '')."'>";
	
	if (strtolower($ident) == strtolower($obj->ident))
		echo $label;
	else
		echo '<a href="?id='.$ident.'">'.$label.'</a>';
		
	echo "</div>";
}
?>
</div>
<table style='clear: both;'>
<tr>
<th><?php echo wfMessage('text_type')->text(); ?></th><td><?php echo $obj->type; ?></td>
</tr>
<?php if ($obj->samples > 1) { ?>
<tr>
<th><?php echo wfMessage('text_samples')->text(); ?></th><td><?php echo $obj->samples; ?></td>
</tr>
<?php } ?>
<!--
<tr>
<th><?php echo wfMessage('text_mean')->text(); ?></th><td><?php echo $obj->mean; ?></td>
</tr>
-->
<tr>
<th><?php echo wfMessage('text_unit')->text(); ?></th><td><?php echo $obj->unit; ?></td>
</tr>
<tr>
<th><?php echo wfMessage('text_wikilink')->text(); ?></th><td><a href="<?php echo $obj->wiki_url.$obj->page; ?>"><?php echo $obj->wiki_name; ?></a></td>
<tr>
<th><?php echo wfMessage('text_upload')->text(); ?></th><td>

<a href='javascript: mw.OpasnetBase.show_upload_history();'><?php echo $obj->act->who; ?>, <?php echo format_dt($obj->act->when); ?> - <?php echo $obj->act->comments; ?></a>
</td>
</tr>
</table>