<table>
<tr>
<th><?php echo wfMessage('text_id')->text(); ?></th>
<?php if ($samples > 1) echo "<th>".wfMessage('text_iter')->text()."</th>"; ?>
<?php
	foreach ($indices as $index)
		#if ($index->wiki_url)
		#	echo "<th><a href='".$index['wiki_url'].$index['wiki_page']."'>".$index['name']."</a></th>";
		#else
			echo "<th>".$index->name."</th>";

	echo "<th>".wfMessage('text_result')->text()."</th>";

	echo "</tr>";
	

	foreach ($res as $key=>$row)
	{
		echo "<tr>";

		echo "<td>".$key."</td>";
		if ($samples > 1)
			echo "<td>".$row['iter']."</td>";
		
		foreach ($indices as $index)
			echo "<td>".(isset($row[$index->ident]) ? $row[$index->ident] : '')."</td>";
	
		echo "<td>".$row['result']."</td>";
		
		echo "</tr>";
	}

?>
</table>