<ul class="nav nav-pills">
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Income
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Income',array('controller' => 'incomes','action' => 'uploadincome')); ?></a></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Search Income',array('controller' => 'incomes','action' => 'searchstatement')); ?></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('View Income Statements',array('controller' => 'incomes','action' => 'viewincome')); ?></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Approve Income Statements',array('controller' => 'incomes','action' => 'approvestatement')); ?></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Delete Income Statements',array('controller' => 'incomes','action' => 'viewdeletestatement')); ?></li>
		</ul>
	</li>
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Tentative Release Schedule
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Tentative Schedule',array('controller' => 'incomes','action' => 'uploadtentativeschedule')); ?></a></li>
		</ul>
	</li>
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Tentative Release
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Create Tentative Release',array('controller' => 'incomes','action' => 'releaseTentativeRelease')); ?></a></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Tentative Release',array('controller' => 'incomes','action' => 'uploadtentativerelease')); ?></a></li>
		</ul>
	</li>
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Release Raw Data
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Release Raw Data',array('controller' => 'incomes','action' => 'releaserawdata')); ?></a></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Release Raw Data',array('controller' => 'incomes','action' => 'uploadreleaserawdata')); ?></a></li>
		</ul>
	</li>
</ul>
<?php
	$tableHeader = array('id','type','file_name','Upload Time');
	$tableCell = array();
	foreach ($universalBatch as $record){
		// debug($record);
		$tableCell[] = array($record['UniversalBatch']['id'],$record['UniversalBatchType']['name'],$record['UniversalBatch']['file_name'],$record['UniversalBatch']['upload_time']);
	}
	$paginationBar = $this->Paginator->pagination(array('ul' => 'pagination'));

	$counter = $this->Paginator->counter();
	echo '<h3>Recent Upload Record</h3>';
	echo $this->SearchResult->generateTable($tableHeader, $tableCell, $paginationBar, $counter);
?>

