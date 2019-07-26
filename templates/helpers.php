<?php
	/*
	function url_params($get_vars, $excludes = null)
	{
		$tmp = array();
		
		foreach ($get_vars as $gk=>$gv)
			if ($excludes === null or ! in_array($gk, $excludes))
				$tmp[$gk] = $gv;
		return "?".http_build_query($tmp);
	}
	*/
	function hidden_params($vars, $excludes = null)
	{
		$ret = '';
		
		//print_r($get_vars);
		
		foreach ($vars as $gk)
		{
			if (isset($_GET[$gk]))
				$gv = $_GET[$gk];
			else
				$gv = '';
			if ($excludes === null or ! in_array($gk, $excludes))
				if (is_array($gv))
					for ($i = 0;  $i < count($gv); $i ++)
						$ret .= '<input type="hidden" name="'.$gk.'['.$i.']" value="'.$gv[$i]."\" />\n";
				else
					$ret .= '<input type="hidden" name="'.$gk.'" value="'.$gv."\" />\n";
		}
		
		return $ret;		
	}
	
	
	/* Prints out data row in standard CSV-format */
	function csv_row($row)
	{
		$i = 0;
		foreach ($row as $cell)
		{
			echo '"'.str_replace('"','""',$cell).'"';	
			if ($i++ < count($row) - 1)
				echo ',';
		}
		echo "\n";
	}
	
	function render_partial($page, $vars)
	{
		if (is_array($vars) && !empty($vars)) {
			extract($vars);
		}
	    include '_'.$page.'.php';		
	}
	
	function format_dt($datetime)
	{
		if ($datetime == '0000-00-00 00:00:00')
			return '-';
		else
			return $datetime;	
	}
		
?>