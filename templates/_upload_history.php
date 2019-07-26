	<form action='' method='get' name='uploads_form'>
		<h2><?php echo wfMessage('text_upload_history')->text(); ?></h2>
		<table class='upload_history'>
			<tr>
				<th><?php echo wfMessage('text_id')->text(); ?></th>
				<th><?php echo wfMessage('text_series_id')->text(); ?></th>
				<th><?php echo wfMessage('text_who')->text(); ?></th>
				<th><?php echo wfMessage('text_when')->text(); ?></th>
				<th><?php echo wfMessage('text_act_type')->text(); ?></th>
				<th><?php echo wfMessage('text_method')->text(); ?></th>
				<th><?php echo wfMessage('text_cells')->text(); ?></th>
				<th><?php echo wfMessage('text_samples')->text(); ?></th>
			</tr>
		<?php
		
			$uploads = $obj->uploads;
			
			$i = 0;

			$first = true;
	
			foreach ($uploads as $u)
			{
				$upload = $u->act;
				$current = '';
				if (isset($get_vars['run']) && $upload->id == $get_vars['run'])
					$current = 'current_run';
				else if (! isset($get_vars['run']) && $first)
					$current = 'current_run';				
				
				echo "<tr class='r".($i++)." ".$current." act_type_".$upload->type."' onclick='mw.OpasnetBase.set_run(\"".$upload->id."\")'>";
				echo "<td>".$upload->id."</td>";
				echo "<td>".$upload->series_id."</td>";
				echo "<td>".$upload->who."</td>";
				echo "<td style='white-space: nowrap;'>".format_dt($upload->when)."</td>";
				echo "<td>".$upload->type."</td>";
				echo "<td>".$upload->comments."</td>";
				echo "<td>".$upload->cells."</td>";
				echo "<td>".$upload->samples."</td>";
				echo "</tr>";
				$i %= 2;
				$first = false;
			}
			
		?>
		</table>
		<div class='popup_actions'>
			<input type='button' onclick='mw.OpasnetBase.hide_upload_history()' value='<?php echo wfMessage('text_cancel')->text(); ?>' />
		</div>
	</form>
