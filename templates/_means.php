<table>
<tr>
<!--<th>Object</th>-->
<th><?php echo wfMessage('text_cell')->text(); ?></th>
<?php
	foreach ($indices as $index)
#		if ($index['wiki_url'])
#			echo "<th><a href='".$index['wiki_url'].$index['wiki_page']."'>".$index['name']."</a></th>";
#		else
			echo "<th>".$index->name."</th>";

	echo "<th>".wfMessage('text_mean')->text()."</th>";
	echo "<th>".wfMessage('text_sd')->text()."</th>";

	echo "</tr>";

	foreach ($res as $key=>$row)
	{
		echo "<tr>";

		//echo "<td>".$row['obj']."</td>";
		echo "<td>".$key."</td>";
		
		foreach ($indices as $ik => $iv)
			echo "<td>".(isset($row[$ik]) ? $row[$ik] : '')."</td>";
	
		echo "<td>".$row['mean']."</td>";
		echo "<td>".$row['sd']."</td>";
		
		echo "</tr>";
	}

?>
</table>