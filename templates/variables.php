<?php
	echo wfMessage('text_total_variables')->text().' '.$var_count;
?>

<table class='ob_variables'>
<tr>
<td>

<?php
	$cnt = 0;
	foreach($vars as $alpha => $i)
	{
		echo '<h3>'.strtoupper($alpha).'</h3>';
		echo '<ul>';
		foreach ($i as $id => $var)
		{
			$ac = $var['act_count'];
			if ($ac == 0) $class = 'no_acts'; else $class = '';
			echo '<li class="'.$class.'"><a href="?id='.$var['ident'].'" title="'.$var['name'].' ('.$ac.' acts)">'.$var['name'].'</a></li>';
			$cnt++;
		}
		echo '</ul>';
		if ($cnt > ($var_count / 3 - 5))
		{
			$cnt = 0;
			echo '</td><td>';
		}
	}
?>

</td>
</tr>
</table>