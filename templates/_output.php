<h3><?php echo wfMessage('text_output')->text(); ?></h3>
<form action='' method='get' name='results_form'>
	<!-- OUTPUT OPTIONS -->
	<input style='display: none;' type='button' name='basepath' value="<? echo $path; ?>" />
	<?php if ($obj->samples > 1) { ?>
		<input <?php echo ((isset($get_vars['op']) && $get_vars['op'] == 'smp') ? 'checked="checked"' : '') ?> onclick="$('#smp').attr('disabled',false); mw.OpasnetBase.update_row_count();" type='radio' name='op' value='smp'/>
		<?php echo wfMessage('text_samples')->text(); ?>
		<input <?php echo ((isset($get_vars['op']) && $get_vars['op'] == 'mean' || ! isset($get_vars['op'])) ? 'disabled="disabled"' : '') ?> id='smp' name='smp' type='text' value='<?php echo $samples ? $samples : $obj->samples; ?>' onkeyup="mw.OpasnetBase.check_int(this, 1,<?php echo $obj->samples; ?>); mw.OpasnetBase.update_row_count();" />
		<input <?php echo ((isset($get_vars['op']) && $get_vars['op'] == 'mean' || ! isset($get_vars['op'])) ? 'checked="checked"' : '') ?> onclick="$('#smp').attr('disabled',true); mw.OpasnetBase.update_row_count();" type='radio' name='op' value='mean' />
		<?php echo wfMessage('text_mean_and_sd')->text(); ?>
	<?php } else { ?>
		<input <?php echo ((isset($get_vars['op']) && $get_vars['op'] == 'smp' || ! isset($get_vars['op'])) ? 'checked="checked"' : '') ?> onclick="$('#smp').attr('disabled',false); mw.OpasnetBase.update_row_count();" type='radio' name='op' value='smp'/>
		<?php echo wfMessage('text_results')->text(); ?>
		<input <?php echo ((isset($get_vars['op']) && $get_vars['op'] == 'mean') ? 'checked="checked"' : '') ?> onclick="$('#smp').attr('disabled',true); mw.OpasnetBase.update_row_count();" type='radio' name='op' value='mean' />
		<?php echo wfMessage('text_mean_and_sd')->text(); ?>
	<?php } ?>
	<p>
	</p>
	<!-- RESULT BUTTONS -->
	<h2><?php echo wfMessage('text_results')->text(); ?></h2>
	<p><?php echo wfMessage('text_result_contains')->text(); ?> <span id='rows_count'><?php if ($results_cnt !== false) echo $results_cnt; ?></span> <?php echo wfMessage('text_rows')->text(); ?></p>
	<input id='refresh_results_button' <?php if($sr) echo "disabled='disabled'"; ?> type='button' value='<?php echo $sr ? wfMessage('text_refresh_results')->text() : wfMessage('text_show_results')->text(); ?>' onclick='mw.OpasnetBase.refresh_results();' />
	<input type='button' value='<?php echo wfMessage('text_download_csv')->text(); ?>' onclick='mw.OpasnetBase.send_csv()' />
	<input type='button' value='<?php echo wfMessage('text_sort')->text(); ?>' onclick='mw.OpasnetBase.show_sorting()' />
	<input type='hidden' name='sr' value='1' />
	<input type='hidden' name='sa' value='0' />
	<?php echo hidden_params($vars, array('sr','smp','op')); ?>
	<hr/>
</form>