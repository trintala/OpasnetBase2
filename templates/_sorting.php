<div id='sort_options' class='popup' style='display: none;'>
	
	<form name='sort_form' action='' method='get'>
		<h2><?php echo wfMessage('text_sort_by_indices')->text();?></h2>
		<table class='clean'>
		<?php
			$i = 0;
			$sort_options = array();
			
			foreach ($indices as $ind)
				$sort_options[$ind->ident] = array('name' => $ind->name);			
			
			foreach ($xtra_sort_options as $k => $v)
			{
				$sort_options[$k] = array('name' => $v);	
			}
			$so = (isset($get_vars['srt']) ? explode('x',$get_vars['srt']) : array());
			foreach ($sort_options as $k=>$v)
			{
				echo "<tr>";
				echo "<td>";
				echo $i.". <select name='sf_".$i."'>";
				echo "<option value=''> - </option>";
				foreach ($sort_options as $k2=>$v2)
				{
					echo "<option ";
					echo ((isset($so[($i*2)]) && $so[($i*2)] == $k2) ? 'selected="selected"' : '');
					echo " value='".$k2."'>".$v2['name']."</option>\n";
				}
				echo "</select>";
				echo "</td>";
				echo "<td>";
				echo "<select name='sd_".$i."'>";
				echo "<option ".((isset($so[($i*2)+1])&& $so[($i*2)+1] == 'a') ? 'selected="selected"' : '')." value='a'>".wfMessage('text_asc')->text()."</option>";
				echo "<option ".((isset($so[($i*2)+1])&& $so[($i*2)+1] == 'd') ? 'selected="selected"' : '')." value='d'/>".wfMessage('text_desc')->text()."</option>";
				echo "</select>";
				echo "</td>";
				echo "</tr>";
				$i++;
			}
		?>
		</table>
		<div class='popup_actions'>
			<input type='button' onclick='mw.OpasnetBase.apply_sorting(<?php echo count($sort_options);?>)' value='<?php echo wfMessage('text_apply')->text(); ?>' /> <input type='button' onclick='mw.OpasnetBase.hide_sorting()' value='<?php echo wfMessage('text_cancel')->text(); ?>' />
		</div>
	</form>
</div>
