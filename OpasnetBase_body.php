<?php
	require_once(dirname(__FILE__) . '/config.php');   # Configuration
	global $obcDebug;

	if (isset($obcDebug) && $obcDebug)
	{
		//error_reporting(E_ALL);
		//ini_set("display_errors", 0);
	}
	
	#require_once dirname(__FILE__).'/lib/OpasnetObject.class.php';

	class OpasnetBase extends SpecialPage
	{
		private $connection;
		private $start_time;
		
		// List of valid variables
		private $vars = array('id','sr','smp','el','srt','op','run','rng');
		
		// Set this to true to echo clean output, no wgOut
		private $clean_output;
				
		function __construct()
		{
			parent::__construct( 'OpasnetBase' );
		//	wfLoadExtensionMessages('OpasnetBase');
			$this->clean_output = false;
			
			
		}

		function execute( $par ) {
			global $wgRequest, $wgOut, $wgServer, $wgScriptPath;

			$this->setHeaders();
			$this->start_time = microtime(true);
			
			$wgOut->addModules( 'ext.OpasnetBase' );
	 
			if (! isset($_GET['id']))
			{
				$objs = OpasnetObject::objects();
				$cnt = 0;
				foreach ($objs as $alpha)
					$cnt += count($alpha); 
				$params = array(
					'vars' => $objs,
					'var_count' => $cnt,
					'path' => $wgServer.$wgScriptPath.'/extensions/OpasnetBase/'
				);
					
				$this->render('variables', $params);
			}
			else
			try {		
				isset($_GET['run']) ? $run = intval($_GET['run']) : $run = null;
				
				$tmp = explode('.',$wgRequest->getText('id'));
				
				$idents = OpasnetObject::idents(strtolower($tmp[0]));
				
				if (count($idents) == 0)
					throw new Exception('No object for given ident was found!!!');
				
				$tmp = array_keys($idents);
				
				if (! in_array($wgRequest->getText('id'), array_keys($idents)))
					$id = $tmp[0];
				else
					$id = $wgRequest->getText('id');
				
				$obj = new OpasnetObject($id, $run);
				$order_by = array();
				
				$samples = null;
				
				if (isset($_GET['smp']) && $_GET['smp'] != '' && (int)$_GET['smp'] != (int)$obj->samples)
					$samples = $_GET['smp'];
					
				//elseif (! $wgRequest->getText('uploads_only') && ! $wgRequest->getText('locations_only'))
				//	$samples = $obj->samples;
				
				if (isset($_GET['el']) && $_GET['el'] != '')
					$excludes = $this->decode_excluded_locations($_GET['el']);
				else
					$excludes = null;

				if (isset($_GET['rng']) && $_GET['rng'] != '')
					$ranges = $this->decode_ranges($_GET['rng']);
				else
					$ranges = null;
					
				if (isset($_GET['srt']) && $_GET['srt'] != '')
				{
					$tmp = explode('x',$_GET['srt']);
					for ($i = 0; $i < count($tmp); $i+=2)
						if ($tmp[$i+1] == 'a')
							$order_by[$tmp[$i]] = SORT_ASC;
						else
							$order_by[$tmp[$i]] = SORT_DESC;
					if (isset($_GET['op']) && $_GET['op'] == 'mean')
						$order_by['cell_id'] = SORT_ASC;	
					else
						$order_by['id'] = SORT_ASC;	
				 }
				 
				 # Limit the result or show it all
				 if (! $wgRequest->getText('sa') and ! $wgRequest->getText('csv'))
				 	$limit = 100;
				 else
				 	$limit = false;				 

				$res = array();
				
				if (! $wgRequest->getText('cnt_only') && ! $wgRequest->getText('uploads_only') && ($wgRequest->getText('sr') == '1' || $wgRequest->getText('csv')|| $wgRequest->getText('json')))
					$get_data = true;
				else
					$get_data = false;

				$results_cnt = false;

				// Get the data? Get result count
				if (isset($_GET['op']) && $_GET['op'] == 'mean')
				{
					if ($get_data)
					{
						$res = $obj->means($excludes, $ranges, $limit, $order_by);
						#$results_cnt = $obj->cell_count($excludes);
					}
					elseif ($wgRequest->getText('cnt_only'))
						$results_cnt = $obj->cell_count($excludes, $ranges);
				}
				else
				{
					if ($get_data)
					{
						$res = $obj->results($samples, $excludes, $ranges, $limit, $order_by);
						#$results_cnt = $obj->results_count($samples, $excludes);
					}
					elseif ($wgRequest->getText('cnt_only'))
						$results_cnt = $obj->results_count($samples, $excludes, $ranges);
				}
				
				$locations = array();

				if ($wgRequest->getText('index_only'))
				{
					$index_id = $wgRequest->getText('index_only');
					$index = $obj->index($index_id);
					if ($index->type == 'entity')	
						$locations = $obj->locations($index_id);
				}
				else
				{
					$index_id = 0;
					$index = false;
				}
				
				if ($wgRequest->getText('cnt_only'))
					$params = array();			
				else
				{
					$get_vars = array();
					foreach ($this->vars as $v)
						if (isset($_GET[$v]) && $_GET[$v] != '')
							$get_vars[$v] = $_GET[$v];				
							
					$params  = array(
						'filter'=>$this->decode_filter(),
						'idents' => $idents,
						'vars'=>$this->vars,
						'get_vars'=>$get_vars,
						'res'=>$res,
						'obj'=>$obj,
						'indices'=>$obj->indices,
						'locations'=>$locations,
						'index_id'=>$index_id,
						'index' => $index,
						'samples'=>$samples,
						'excludes' => $excludes,
						'ranges' => $ranges,
						'order_by' => $order_by,
						'results_cnt' => $results_cnt,
						'limit' => $limit,
						'sr' => $get_data,
				//		'results_cnt' => $results_cnt,
						'path' => $wgServer.$wgScriptPath.'/extensions/OpasnetBase/',
					);
				}
		
				# Output
				if ($wgRequest->getText('csv'))
					$this->generate_csv($params);
				elseif ($wgRequest->getText('uploads_only'))
				{
					$this->clean_output = true;
					$this->render('_upload_history',$params);					
				}
				elseif ($wgRequest->getText('index_only'))
				{
					$this->clean_output = true;
					$this->render('_index',$params);					
				}
				elseif ($wgRequest->getText('cnt_only'))
					$this->render_row_count($results_cnt);
				elseif ($wgRequest->getText('json'))
					$this->render_json($params);
				else
					$this->render('index',$params);
				
			}
			catch (Exception $e)
			{
				$this->render_error($e->getMessage());
			}
			
		}
		
		function render_json($vars=null)
		{
			include 'templates/helpers.php';
		    if (is_array($vars) && !empty($vars)) {
		        extract($vars);
		    }
		    include 'templates/json.php';
		    // Now stop the run to send ONLY csv data, no wiki page 
			die;
		}
				
		// This method is used when row count is requested via AJAX
		function render_row_count($cnt)
		{
			echo $cnt;
		    // Now stop the run to send ONLY count data, no wiki page 
			die;			
		}
		
		function generate_csv($vars=null)
		{
		    if (is_array($vars) && !empty($vars)) {
		        extract($vars);
		    }
		    include 'templates/csv.php';
		    // Now stop the run to send ONLY csv data, no wiki page 
			die;
		}
				
		function render($target, $vars=null, $skip_debug = false)
		{
			global $obcDebug;
			
		    if (is_array($vars) && !empty($vars)) {
		        extract($vars);
		    }
		    
		    ob_start();
		    include 'templates/helpers.php';
		    include 'templates/'.$target.'.php';
	    	$this->output(ob_get_clean());
		    
		    // DEBUG??
		    /*
		    if (!$skip_debug && $qs = $this->connection->get_queries())
		    	foreach ($qs as $q)
		    	{
					$this->output("<div class='query_debug'>".$q[0]);
					$this->output("<div class='gentime'>".$q[1].'s</div></div>');
		    	}
		    */
		    
		    if (!$skip_debug and $obcDebug == true)
				$this->output("<div class='gentime'>".round(microtime(true) - $this->start_time, 5) . 's</div>');

			if ($this->clean_output)
			{
			    // Now stop the run to send ONLY content, no wiki page 
				die;
			}		
		}
		
		function render_error($msg)
		{
			global $wgOut;
			if ($this->clean_output)
				echo '<strong>'.$msg.'</strong>';
			else
				$wgOut->addHTML('<strong>'.$msg.'</strong>');
		}
		
		function output($content)
		{
			global $wgOut;
			if ($this->clean_output)
				echo $content;
			else
				$wgOut->addHTML($content);
		}
		
		function decode_ranges($str)
		{
			$ret = array();
			$tmp = explode('|',$str);
			$i = 0;
			foreach ($tmp as $t)
			{
				$r = explode(';',$t);
				if (count($r) == 2)
					$ret[$i] = array($r[0], $r[1]);
				$i++;
			}
			return $ret;
		}

		function decode_excluded_locations($hex)
		{
			$arr = explode('x',$hex);
			$ret = array();

			$j = 0;
			
			foreach ($arr as $ind)
			{
				if ($ind != '')
					$ret[$j] = array();
				$bin = '';
				foreach (str_split($ind) as $h)
				{
					$tmp = decbin(hexdec($h));
					for ($i = 0; $i < (4 - strlen($tmp)); $i ++)
						$bin .= '0';
					$bin .= $tmp;
				}
				$bin = str_split($bin);
				for ($i = 0; $i < count($bin); $i ++)
					if ($bin[$i] == '1')
						$ret[$j][] = $i;
				$j ++;
			}
			return $ret;
		}

		private function decode_filter()
		{
			global $wgRequest;
			
			if (! ($f = $wgRequest->getText('filter')))
				return false;

			$ret = array();

			foreach(explode(";",$f) as $fi)
			{
				$t = explode(":",$fi);
				$key = $t[0];
				$value = explode(",",$t[1]);
				$ret[$key] = $value;
			}
			
			return $ret;
		}

	
	}



