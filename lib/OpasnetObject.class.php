<?php
class OpasnetObject {
	private $data;
	private $acts = array();
	private $act = null;
	private $indices = array();

	function __construct($ident, $run = null) {
				
		$resp = json_decode(file_get_contents(self::query_url(array('ident' => $ident))));
		
		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);
		
        $this->data['id'] =  $resp->object->id;
        $this->data['ident'] = $resp->object->ident;
  		if (! empty($resp->object->subset))
	        $this->data['ident'] .= '.' . $resp->object->subset;
        $this->data['name'] = $resp->object->name;
        $this->data['type'] = $resp->object->type;
        $this->data['page'] = $resp->object->page;
        $this->data['wiki_id'] = $resp->object->wiki_id;	
        $this->data['wiki_name'] = $resp->object->wiki_name;	
        $this->data['wiki_url'] = $resp->object->wiki_url;	
		
		$this->acts = $resp->acts;
		
		if ($run)
		{
			#print_r($this->acts);
			$this->act = $this->acts->{$run}->act;
			$this->indices = $this->acts->{$run}->indices;
		}
		else		
		{
			$this->act = reset($this->acts)->act;
			$this->indices = reset($this->acts)->indices;
		}
		
		/*
		$this->base = $base;
		$this->ident = $ident;

		//Init object's basic data FIRST
		$this->init_obj_data();
		$this->init_act_data($run);
			
		//Set wiki data
		$wiki_data = $this->get_wiki_data($this->data['wiki_id']);
		isset($wiki_data['url']) ? $this->data['wiki_url'] = $wiki_data['url'] : $this->data['wiki_url'] = '';
		isset($wiki_data['wname']) ? $this->data['wiki_name'] = $wiki_data['wname'] : $this->data['wiki_name'] = '';
		*/
	}

	function __get($var) {
		
		if ($var == 'unit')
			return $this->act->unit;
		
		if ($var == 'samples')
			return $this->act->samples;

		if ($var == 'act')
			return $this->act;

		if ($var == 'series_id')
			return $this->act->series_id;

		if ($var == 'indices')
			return $this->indices;

		if ($var == 'uploads')
			return $this->acts;

		/*
		if ($var == 'mean')
		{
			if ($this->mean === null)
				$this->mean = $this->avg_mean($this->series_id);
			return $this->mean;
		}
		*/
		
		return $this->data[$var];
	}
	
	static function do_request($url, $data, $optional_headers = null, $method = 'POST')
	{
		  $params = array('http' => array(
		              'method' => $method,
		              'content' => $data
		            ));
		  if ($optional_headers !== null) {
		    $params['http']['header'] = $optional_headers;
		  }
		  $ctx = stream_context_create($params);
		  $fp = @fopen($url, 'rb', false, $ctx);
			if (isset($php_errormsg))
				$err = $php_errormsg;
			else
				$err = '';
		  if (!$fp) {
		    throw new Exception("Problem with $url, $err");
		  }
		  $response = @stream_get_contents($fp);
		  if ($response === false) {
		    throw new Exception("Problem reading data from $url, $err");
		  }
		  return $response;
	}
	
	static function query_params($params)
	{
 		#global $obcDatabaseUsername, $obcDatabasePassword;
 		
 		$params['username'] = obcDatabaseUsername;
 		
 		# Generate password hash
 		$str = '';
 		if (isset($params['index']))
 			$str .= $params['index'];
 		if (isset($params['ident']))
 			$str .= $params['ident'];
 		if (isset($params['key']))
 			$str .= $params['key'];
 		$str .= obcDatabasePassword;
 		
 		$params['password'] = md5($str);
 		
 		# Sending GET via POST
 		$params['_method'] = 'GET';
 		
 		return(http_build_query($params));		
	}
	
	static function query_url($params)
	{
		$p = self::query_params($params);
	    $url = OB_INTERFACE_URL.'?'.$p;
	    return($url);
	}
	
	static function query($params)
	{
		$p = self::query_params($params);
		return json_decode(self::do_request(OB_INTERFACE_URL, $p));
	}
	
	static function objects()
	{
		#global $obcDatabaseUsername;
		
		$resp = json_decode(file_get_contents(OB_INTERFACE_URL));
		
		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);
	
		$ret = array ();

		$idents = array();
		
		# users and their wikis
		$wikis = array(1 => 'opasnet_en', 2 => 'opasnet_fi', 3 => 'heande', 4 => 'eractest');

		foreach ($resp->objects as $var)
		if (! in_array($var->ident, $idents) && isset($var->name[0]) && $wikis[$var->wiki_id] == obcDatabaseUsername)
		{
			if (! isset($ret[strtolower($var->name[0])]))
				$ret[strtolower($var->name[0])] = array();
			$ret[strtolower($var->name[0])][$var->id] = array('name'=>$var->name, 'ident'=>$var->ident, 'act_count' => 1);
			$idents[] = $var->ident;
		}
		return $ret;
	}
		
	static function idents($ident)
	{
		$resp = json_decode(file_get_contents(OB_INTERFACE_URL));
		
		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);

		$ret = array();

		foreach ($resp->objects as $var)		
			if (strtolower($var->ident) == strtolower($ident))
			{
				if (! empty( $var->subset))
					$ret[strtolower($var->ident) . '.' . strtolower($var->subset)] = $var->subset_name;
				else
					$ret[strtolower($var->ident)] = $var->name;
			}
		ksort($ret);
				
		return $ret;
	}
	
	static function t2b_check($ident)
	{
		$obj = self::is_t2b_upload($ident);
		if ($obj)
			return self::page_has_t2b($obj->page, $obj->subset_name);
		else
			return true;
	}
		
	static function is_t2b_upload($ident)
	{		
		$resp = json_decode(file_get_contents(self::query_url(array('ident'=>$ident))));
		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);
		$act = reset($resp->acts)->act;
		
		if (strrpos($act->comments, 'table2base' ) !== false)
			return $resp->object;
		 else
			return false;
	}
	
	static function page_has_t2b($page_id, $name)
	{
		$dbw = wfGetDB( DB_SLAVE );
/*
		$res = $dbw->select('page',array('page_id'),"page_title='{$page}' AND page_namespace=0 LIMIT 1",__METHOD__);
		foreach( $res as $row ) 
		   	$page_id = $row->page_id;
		    
		if (! $page_id)
			return '# Could not include code: '.$page.', '.$name.'. Page not found!';
	*/	    
		$res = $dbw->select('revision',array('rev_text_id'),"rev_page={$page_id} AND rev_deleted=FALSE ORDER BY rev_timestamp DESC LIMIT 1",__METHOD__);
		
		$text_id = null;
		
		foreach( $res as $row ) 
	       	$text_id = $row->rev_text_id;

		if ($text_id === null) return false;

		$res = $dbw->select('text',array('old_text'),"old_id = {$text_id} LIMIT 1",__METHOD__);
		
		foreach( $res as $row ) 
	    	$input = $row->old_text;

		if ($name == '')
		{
			$regexp = '/<t2b[^>]+>/';
			preg_match_all($regexp, $input, $matches, PREG_SET_ORDER);
			foreach($matches as $match)
				if (strpos($match[0],"name=") === false)
					return true;
		}
		else
		{
			$regexp = '/<t2b[^>]+name=["\']?'.preg_quote($name).'["\']?[^>]*>/';
			$ret = preg_match($regexp, $input, $matches);
			return $ret;
		}

#		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER))
		#	foreach($matches as $match)
			#	if (count($match) == 4 && trim($match[2]) == trim($name))
			#		return true;
		
	}
	
		
	function act_cell_count($act)
	{
		$url = self::query_url(array('ident'=>$this->data['ident'],'act'=>$act,'distinct_results_count'=>'1'));		
		$resp = json_decode(file_get_contents($url));

		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);		

		return $resp->results_count;
	}

	function index($index_id) {
		foreach ($this->indices as $i)
			if ($i->id == $index_id)
				return $i;
		return false; 
	}
	
	function locations($index_id) {
		$resp = json_decode(file_get_contents(self::query_url(array('index' => $index_id))));
		
		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);
		
		return $resp;
	}

	function results_count($samples = null, $excludes = null, $ranges = null) {
	
		$params = array();
		
		$params['results_count'] = 1;
		$params['samples'] = $samples;
		
		$params['ident'] = $this->data['ident'];
		$params['act'] = $this->act->id;
		$params['exclude'] = $this->decode_excludes($excludes);
		$params['range'] = $this->decode_ranges($ranges);
		
		$resp = self::query($params);

		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);		

		return $resp->results_count;
	}

	function cell_count($excludes = null, $ranges = null)
	{
	$params = array();
		
		$params['results_count'] = 1;
		$params['samples'] = 1;
		$params['ident'] = $this->data['ident'];
		$params['act'] = $this->act->id;
		$params['exclude'] = $this->decode_excludes($excludes);
		$params['range'] = $this->decode_ranges($ranges);
				
		$resp = self::query($params);

		if (isset($resp->error))
			throw new Exception('Unable to initialize OpasnetObject: '.$resp->error);		

		return $resp->results_count;
	}

	function results($samples, $excludes, $ranges, $limit = false, $order_by = false) {
		
		#global $obcDatabaseUsername, $obcDatabasePassword;
		
		$params = array();
		
		#$params['order'] = $order_by;
		$params['samples'] = $samples;
		$params['ident'] = $this->data['ident'];
		$params['act'] = $this->act->id;
		$params['range'] = $this->decode_ranges($ranges);
		
	 	if($samples)
	 		$params['limit'] = ceil($limit / $samples);
	 	else
	 		$params['limit'] = ceil($limit / $this->samples);
	
		$params['exclude'] = $this->decode_excludes($excludes);
		
		$resp = self::query($params);

		if (! $resp)
			throw new Exception('Unable to get response from database server!');

		if (isset($resp->error))
			throw new Exception('Unable to get download key: '.$resp->error);

		if (! isset($resp->key))
			throw new Exception('Invalid download key!');

		$key = $resp->key;
			
		$ret = array();

		$i  = 1;
			
		do {
			$url = self::query_url(array('key'=>$key));
			$resp = json_decode(file_get_contents($url));
			
			//print_r($resp);
			
			if (! empty($resp->data))
			foreach (json_decode($resp->data) as $row)
			{
				$obs = 1;
				 
				if (isset($row->res) && is_array($row->res))
				{
					foreach($row->res as $res)
					{
						$ret[$i] = array('id' => $i, 'iter' => $obs, 'result' => $res);
						$obs ++;
						foreach($row as $k => $v)
						if ($k != 'sid' && $k != 'aid' && $k != 'mean' && $k != 'sd' && $k != 'res')
							$ret[$i][$k] = $v;
						$i++;

						# Break if on the limit!!!
						if ($limit && $obs > $limit)
							break;
					}		
				} else {
						isset($row->res) ? $res = $row->res : $res = '';					
						$ret[$i] = array('id' => $i, 'result' => $res);
						foreach($row as $k => $v)
						if ($k != 'sid' && $k != 'aid' && $k != 'mean' && $k != 'sd' && $k != 'res')
							$ret[$i][$k] = $v;
						$i++;					
				}
					
			}
		} while (! empty($resp->data));

		if (!empty ($order_by))
			$ret = $this->array_msort($ret, $order_by);

		return $ret;
	}

	function means($excludes, $ranges, $limit = false, $order_by = false) {
		$params = array();
		
		#$params['order'] = $order_by;
		$params['samples'] = 0;
		$params['ident'] = $this->data['ident'];
		$params['act'] = $this->act->id;
		$params['limit'] = $limit;
		$params['exclude'] = $this->decode_excludes($excludes);
		$params['range'] = $this->decode_ranges($ranges);
				
		$resp = self::query($params);

		if (! $resp)
			throw new Exception('Unable to get response from database server!');

		if (isset($resp->error))
			throw new Exception('Unable to get download key: '.$resp->error);

		$key = $resp->key;
			
		$ret = array();

		$i  = 1;
			
		do {
			$url = self::query_url(array('key'=>$key));
			$resp = json_decode(file_get_contents($url));

			if (! empty($resp->data))
			foreach (json_decode($resp->data) as $row)
			{
				$obs = 1;
				isset($row->mean) ? $mean = $row->mean : $mean = '';
				isset($row->sd) ? $sd = $row->sd : $sd = '';				
				$ret[$i] = array('id' => $i, 'mean' => $mean, 'sd' => $sd);
				foreach($row as $k => $v)
				if ($k != 'sid' && $k != 'aid' && $k != 'mean' && $k != 'sd' && $k != 'res')
					$ret[$i][$k] = $v;
				$i++;					
			}
		} while (! empty($resp->data));
	
		if (!empty ($order_by))
			$ret = $this->array_msort($ret, $order_by);
			
		return $ret;
	}

	/* PRIVATE FUNCTIONS >>> */

	private function array_msort($array, $cols) {
		$colarr = array ();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array ();
			foreach ($array as $k => $row) {
				$colarr[$col]['_' . $k] = (isset($row[$col]) ? strtolower($row[$col]) : null);
			}
		}
		$params = array ();
		foreach ($cols as $col => $order) {
			$params[] = & $colarr[$col];
			$params = array_merge($params, (array) $order);
		}
		call_user_func_array('array_multisort', $params);
		$ret = array ();
		$keys = array ();
		$first = true;
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				if ($first) {
					$keys[$k] = substr($k, 1);
				}
				$k = $keys[$k];
				if (!isset ($ret[$k]))
					$ret[$k] = $array[$k];
				$ret[$k][$col] = (isset($array[$k][$col]) ? $array[$k][$col] : null);
			}
			$first = false;
		}
		return $ret;
	}
	
	// Excludes are presented as order numbers and this function returns corresponding ids
	private function decode_excludes($excludes) {
		
		if (! is_array($excludes))
			return array();
		
		$i = 0;
		$ret = array ();
		
		foreach ($excludes as $x => $ls)
		{
			$loc = $this->locations($this->indices[$x]->id);
			
			#print_r($loc->locations);
			
			#print_r($this->indices[$x]);
			
			$tmp = array();
			foreach ($loc->locations as $k => $v)
				$tmp[] = $k;
			
			$tmp2 = array($this->indices[$x]->ident);
			foreach ($ls as $l)
				$tmp2[] = $tmp[$l-1];
			$ret[] = join(',',$tmp2);
		}
					
		return $ret;
	}

	private function decode_ranges($ranges) {

		if (! is_array($ranges))
			return array();
		
		$ret = array();
			
		foreach ($ranges as $k => $v)
			$ret[] = $this->indices[$k]->ident . ';' . $v[0] . ';' . $v[1];
		
		return $ret;
	}



}
?>