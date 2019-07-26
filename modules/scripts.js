mw.OpasnetBase = {

row_count_timer: null,

refresh_results: function(show_all)
{
	if (show_all != null && show_all == true)
	{
		document.results_form.sa.value = '1';	
	}
	document.results_form.submit();
	this.show_loader();
},

// New binary version
apply_locations: function(id)
{
	var indices = document.indices_form.indices.value.split(',');
	var excluded = new Array(indices.length);
	
	// Copy existing exclude data
	var current_excludes = document.results_form.el.value.split('x');
	//alert(current_excludes);
	for (var i=0; i < current_excludes.length; i ++)
		excluded[i] = current_excludes[i];

	for (var i=0; i < indices.length; i++)
	if (indices[i] == id)
	{
		var tmp = new Array();
		$('input.index_'+indices[i]).each(function(i){
			if (! this.checked)
				tmp[this.value] = '1';
		});
		excluded[i] = this.encode_location_binary(tmp);
	}
		
	document.results_form.el.value = excluded.join('x');
	
	this.hide_popup('dimension_options_'+id);
	
	this.update_row_count();
	$('#refresh_results_button').attr("disabled",false);
	return false;
},

apply_number_range: function(id)
{
	var indices = document.indices_form.indices.value.split(',');
	var ranges = new Array(indices.length);
	
	// Copy existing
	var current_ranges = document.results_form.rng.value.split('|');
	for (var i=0; i < current_ranges.length; i ++)
		ranges[i] = current_ranges[i];

	var range = new Array();
	range[0] = '';
	range[1] = '';

	for (var i=0; i < indices.length; i++)
		if (indices[i] == id)
		{
			$('input.index_'+indices[i]+'.number_range_from').each(function(i){
				if (this.value != '')
					range[0] = this.value;
			});
			$('input.index_'+indices[i]+'.number_range_to').each(function(i){
				if (this.value != '')
					range[1] = this.value;
			});
			ranges[i] = range.join(';');
		}
	
	document.results_form.rng.value = ranges.join('|');
	
	this.hide_popup('dimension_options_'+id);
	
	this.update_row_count();
	$('#refresh_results_button').attr("disabled",false);
	return false;
},

check_all_locations: function(id)
{
	elems = $('input.index_'+id);
	elems.each(function(i){ this.checked = true;} );
	this.check_index(id, null);
},

uncheck_all_locations: function(id)
{
	elems = $('input.index_'+id);
	elems.each(function(i){ this.checked = false;} );
	this.check_index(id, null);
},

show_popup: function(id)
{
	$('#smokescreen').css('display','block');
	$('#'+id).css('display','block');	
},

hide_popup: function(id)
{
	$('#'+id).css('display','none');
	$('#smokescreen').css('display','none');		
},

hide_options: function(id)
{
	eval('document.index_form_'+id+'.reset()');
	this.check_index(id,null);
	this.hide_popup('dimension_options_'+id);		
},

hide_sorting: function()
{
	document.sort_form.reset();
	this.hide_popup('sort_options');
},

show_dimension: function(id)
{
	elem = $('#dimension_options_'+id);

	this.show_popup('dimension_options_'+id);
	//if (elem.innerHTML.length < 10)
	//{
		var basepath = document.results_form.basepath.value;
		var run = document.results_form.run.value;
		var obj_id = document.results_form.id.value;
		var el = document.results_form.el.value;
		var rng = document.results_form.rng.value;
		elem.html('<img style="vertical-align: top;" src="'+basepath+'images/ajax-loader.gif" alt="Loading..." />');
		$.ajax({
	  		url: '?id='+obj_id+'&index_only='+id+'&run='+run+'&el='+el+'&rng='+rng,
	  		cache: false,
	  		success: function(html){
    			elem.html(html);
	  		}
		});
	//}

},

show_sorting: function()
{
	this.show_popup('sort_options');
},

show_upload_history: function()
{
	var basepath = document.results_form.basepath.value;
	var run = document.results_form.run.value;
	var id = document.results_form.id.value;
	
	this.show_popup('upload_history');
	if ($('#upload_history').html().length < 10)
	{
		$('#upload_history').html('<img style="vertical-align: top;" src="'+basepath+'images/ajax-loader.gif" alt="Loading..." />');
		$.ajax({
	  		url: '?id='+id+'&uploads_only=1&run='+run,
	  		cache: false,
	  		success: function(html){
    			$("#upload_history").html(html);
	  		}
		});
	}
},

hide_upload_history: function()
{
	this.hide_popup('upload_history');	
},

check_index: function(id,checkbox)
{
	apply = $('#apply_dims_'+id);
	
	var ok = false;
	$('input.index_'+id).each(function(i){if ($(this).is(":checked")) ok = true;});
	if (! ok)
		apply.attr('disabled',true);
	else
		apply.attr('disabled',false);
	
	return false;
},

// Generates URL from results_form
url_with_params: function()
{
	var id = document.results_form.id.value;
	var el = document.results_form.el.value;
	var srt = document.results_form.srt.value;
	var rng = document.results_form.rng.value;
	var smp;
	if (document.results_form.smp)
		smp = document.results_form.smp.value;
	else
		smp = 1;
	var op = this.current_output();
	var sr = document.results_form.sr.value;
	var run = document.results_form.run.value;

	return '?id='+id+'&el='+el+'&srt='+srt+'&smp='+smp+'&op='+op+'&sr='+sr+'&run='+run+'&rng='+rng;
},

send_csv: function()
{
	document.location.href= this.url_with_params() + '&csv=1';
	return false;
},

set_run: function(run)
{
	document.results_form.rng.value = '';
	document.results_form.el.value = '';
	document.results_form.srt.value = '';
	if (document.results_form.smp)
		document.results_form.smp.value = '';
	document.results_form.op.value = '';
	document.results_form.sr.value = '0';
	document.results_form.run.value = run;
	document.results_form.submit();
	
	$('#upload_history').css('display','none');
	this.show_loader();
	return false;	
},

apply_sorting: function(cnt)
{
	var field;
	var direction;
	var srt = '';
	
	for (var i=0; i < cnt; i ++)
	{
		field = document.getElementsByName('sf_'+i)[0];
		direction = document.getElementsByName('sd_'+i)[0];
		if (field[field.selectedIndex].value != '')
			srt += 'x' + field[field.selectedIndex].value + 'x' + direction[direction.selectedIndex].value;
	}

	// Remove the x
	if (srt.length > 0)
		srt = srt.substring(1);

	document.results_form.srt.value = srt;
	
	$('#sort_options').css('display','none');
	document.results_form.submit();
	this.show_loader();
	
	return false;
},

check_int: function(elem, min, max)
{
	var valid = "0123456789";
	var v = elem.value;
	var nv = '';
	// Check for others than letters
	for (var i = 0; i < v.length; i ++)
		if (valid.indexOf(v.charAt(i)) != -1)
			nv += v.charAt(i);
	
	// Check boundaries
	if (parseInt(nv) < min)
		nv = min;
	else if (parseInt(nv) > max)
		nv = max;
	
	elem.value = nv;
},



timed_update_row_count: function()
{
	var samples = $('#smp').val();
	
	var op = this.current_output();
	var id = document.results_form.id.value;
	var el = document.results_form.el.value;
	var run = document.results_form.run.value;
	var rng = document.results_form.rng.value;

	//new Ajax.Updater('rows_count', '?id='+id+'&smp='+samples+'&cnt_only=1&op='+op+'&el='+el+'&run='+run, { method: 'get' });
	
	$.ajax({
  		url: '?id='+escape(id)+'&smp='+samples+'&cnt_only=1&op='+op+'&el='+el+'&run='+run+'&rng='+rng,
  		cache: false,
  		success: function(html){
  			if (html.length < 100)
    			$("#rows_count").html(html);
    		else
  	 			$("#rows_count").html('error');
   	},
  		error: function(html){
  			$("#rows_count").html('error');
  		}
	});
},

update_row_count: function()
{
	var basepath = document.results_form.basepath.value;
	$('#rows_count').html('<img style="vertical-align: top;" src="'+basepath+'images/ajax-loader.gif" alt="" />');
	$('#refresh_results_button').attr('disabled',false);
	
	if (this.row_count_timer != null)
		clearTimeout(this.row_count_timer);
		
	this.row_count_timer = setTimeout("mw.OpasnetBase.timed_update_row_count()", 1000);			
},

current_output: function()
{
	var op = document.results_form.op;
	
	if (op[0].checked) // Smp
	{
		return 'smp';
	}
	else if (op[1].checked) // Mean
	{
		return 'mean';
	}
},

show_loader: function()
{
	//$('smokescreen').style.display = 'block';
	//$('loader').style.display = 'block';
	//$('loader').innerHTML = $('loader').innerHTML;
},

// Encodes binary string to hexacode (4 bit sequences)
encode_location_binary: function(bin)
{
	var hex = '';
	var cbin;
	for (var i=0; i < bin.length;)
	{
		cbin='';
		for (var j=0; j < 4; j++, i++)
			if (typeof(bin[i]) != 'undefined')
				cbin += bin[i];
			else
				cbin += '0';
		tmp = parseInt(cbin, 2).toString(16);
		hex += tmp;
	}
	return hex;
}

}





