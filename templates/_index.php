<form name='index_form_<?php echo $index_id; ?>' action="" method='get'>
<h2><?php echo wfMessage('text_index_options')->text(); ?></h2>

<strong><?php echo $index->name;?></strong><br/>
<br/>
<?php
 	 if ($index->type == 'entity') 	 	
 	 	include(dirname(__FILE__).'/_entity_filter.php');
 	 elseif ($index->type == 'number')
 	 	include(dirname(__FILE__).'/_number_filter.php');
 	 elseif ($index->type == 'time')
 	 	include(dirname(__FILE__).'/_time_filter.php');
 ?>

 </form>
 