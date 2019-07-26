<?php
# Headers
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$arr = array();

# Info

$arr[0] = array();
$arr[0]['obj_name'] =  $obj->name;
$arr[0]['obj_type'] = $obj->type;
if ($obj->samples > 1)
	$arr[0]['obj_samples'] = $obj->samples;
$arr[0]['obj_unit'] = $obj->unit;
$arr[0]['obj_url'] = $obj->wiki_url.$obj->page;
$latest_run = $obj->act;
$arr[0]['obj_act'] = $latest_run->who .", " . format_dt($latest_run->when) . " - " . $latest_run->comments;

# THE DATA

$r = 1;

$keys = array();

$kp = 0;

$keys[$kp++] = 'id';
	
	if ($samples > 1) $keys[$kp++] = 'obs';

	foreach ($indices as $index)
		$keys[$kp++] =  $index->name;

	$keys[$kp] = 'result';


	foreach ($res as $key=>$row)
	{
//		$arr[++$r] = array();

		$tmp = array();

		$kp = 0;
		$tmp[$keys[$kp++]] = $key;
		if ($samples > 1)
			$tmp[$keys[$kp++]] = $row['obs'];
		
		$skip_row = false;
		
		foreach ($indices as $ik => $iv)
		{
			$v = (isset($row[$ik]) ? $row[$ik] : '');
			
			if (isset($filter) && ! empty($filter) && isset($filter[$keys[$kp]]) && ! in_array(mb_strtolower($v),$filter[$keys[$kp]]))
				$skip_row = true;
			$tmp[$keys[$kp++]] = $v;
		}

		$tmp[$keys[$kp]] =  $row['result'];

	
		if (! $skip_row)
			$arr[$r++] = $tmp;
	}
	
	//print_r($arr);

	echo json_encode($arr);

?>
