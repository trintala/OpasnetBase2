<?php
	require_once dirname(__FILE__).'/helpers.php';

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=results.csv");
	
	$csvrow = array();

	/* BEGIN PRINTING THE CSV */
	csv_row(array(wfMessage('text_name')->text().":",$obj->name));
	if ($obj->samples > 1)
		csv_row(array(wfMessage('text_samples')->text().":",$obj->samples));
	//csv_row(array(wfMessage('text_mean')->text().":",$obj->mean));
	csv_row(array(wfMessage('text_unit')->text().":",$obj->unit));
	csv_row(array(wfMessage('text_upload')->text().":", $obj->act->who.', '.format_dt($obj->act->when).' - '.$obj->act->comments));
	
	echo "\n\n";
	
	if (isset($_GET['op']) && $_GET['op'] == 'mean')
	{
		$csvrow[]= wfMessage('text_cell')->text();
	}
	else
	{
		$csvrow[]= wfMessage('text_id')->text();
		if ($obj->samples > 1)
			$csvrow[]= wfMessage('text_iter')->text();
	}

	foreach ($indices as $index)
		$csvrow[]= $index->name;

	if (isset($_GET['op']) && $_GET['op'] == 'mean')
	{
		$csvrow[]= wfMessage('text_mean')->text();
		$csvrow[]= wfMessage('text_sd')->text();
	}
	else
	{
		$csvrow[]= wfMessage('text_result')->text();		
	}
	
	csv_row($csvrow);

	foreach ($res as $key=>$row)
	{
		$csvrow = array();

		$csvrow[]= $key;
	
		if ($obj->samples > 1 && ! (isset($_GET['op']) && $_GET['op'] == 'mean'))
			$csvrow[]= $row['iter'];

		foreach ($indices as $ik => $iv)
			$csvrow[]= (isset($row[$ik]) ? $row[$ik] : '');
	
		if (isset($_GET['op']) && $_GET['op'] == 'mean')
		{
			$csvrow[]= $row['mean'];
			$csvrow[]= $row['sd'];			
		}		
		else
		{
			$csvrow[]= $row['result'];
		}
		csv_row($csvrow);
	}
?>