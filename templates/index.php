<div id='upload_history' class='popup'>
</div>

<?php
	
	if (isset($get_vars['op']) && $get_vars['op'] == 'mean')
		render_partial('sorting', array('indices'=>$indices, 'get_vars'=>$get_vars, 'xtra_sort_options' => array('mean'=>wfMessage('text_mean')->text(), 'sd' => wfMessage('text_sd')->text()) ));
	else
		render_partial('sorting', array('indices'=>$indices, 'get_vars'=>$get_vars, 'xtra_sort_options' => array('result'=>wfMessage('text_result')->text())));
?>

<div id='smokescreen'></div>
<div id='loader'><!--<img src='<? echo $path; ?>images/ajax-loader_big2.gif' alt='loading...' />--></div>

<div id='base_wrapper'>
<?php render_partial('var_info', array('obj'=>$obj, 'idents'=>$idents)); ?>
<h3><?php echo wfMessage('text_available_indices')->text(); ?></h3>
<?php
render_partial('indices', array('indices'=>$indices, 'get_vars'=>$get_vars));
render_partial('output', array('results_cnt'=>$results_cnt,'obj'=>$obj,'sr'=>$sr,'get_vars'=>$get_vars,'samples'=>$samples, 'path'=>$path, 'vars'=>$vars));
?>

<br/>
<?php
	if ($sr && count($res) > 0)
	{
		if (isset($get_vars['op']) && $get_vars['op'] == 'mean')
			render_partial('means', array('res'=>$res, 'indices'=>$indices));		
		else
			render_partial('results', array('res'=>$res, 'indices'=>$indices, 'samples'=>$obj->samples));
		
		if ($limit !== false && isset($res) && count($res) >= $limit)
			echo '<p><a href="javascript:mw.OpasnetBase.refresh_results(true);">'.wfMessage('text_show_all_result_rows')->text().'</a></p>';
	}
	elseif ($sr)
		echo wfMessage('text_no_results')->text();
?>

</div>
<br/>

<?php if (is_array($indices) && $results_cnt === false){?>
<script type='text/javascript'>
	/* <![CDATA[ */

	function makeDoubleDelegate(function1, function2){
	    return function() {
	        if (function1)
	            function1();
	        if (function2)
	            function2();
	    }
	}

	function update_rc(){
		mw.OpasnetBase.update_row_count();
	}

	window.onload = makeDoubleDelegate(window.onload, update_rc);

/* ]]> */
</script>
<?php } ?>

