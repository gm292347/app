<ul class="nav nav-pills">
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Income
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Income',array('controller' => 'incomes','action' => 'uploadincome')); ?></a></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Search Income',array('controller' => 'incomes','action' => 'searchstatement')); ?></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('View Income Statements',array('controller' => 'incomes','action' => 'viewincome')); ?></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Check Income Statements',array('controller' => 'incomes','action' => 'checkstatement')); ?></li>
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
	<li role="presentation" class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-size: 120%;color: #503C19;font-family: Palatino Linotype, Book Antiqua, Palatino, serif">
			Release Records
		</a>
		<ul class="dropdown-menu" style="background-color: #38BDA2; border: 2px solid #38BDA2; border-radius:10px; margin-top: 0px;">
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Release Record',array('controller' => 'incomes','action' => 'releaserecords')); ?></a></li>
			<li style="background-color:#38BDA2;"><?php echo $this->Html->link('Upload Release Record',array('controller' => 'incomes','action' => 'uploadreleaserecord')); ?></a></li>
		</ul>
	</li>
</ul>
<?php
	$total = array_sum(hash::extract($incomes,'{n}.amount'));
	$currency = $incomes[0]['currency'];
	$tableheader = $this->Html->tableHeaders(array('Policy No.','Client Name','Type','Statement Date','Currency','Amount','Edit','Delete'));;
	$tablecells = '';
	foreach($incomes as $income){
		$tablecells .= $this->Html->tableCells(array(
												$income['policy_no'],
												$income['client'],
												$income['type'],
												$income['statement_date'],
												$income['currency'],
												$income['amount'],
												$this->Html->link('Edit',array('controller' => 'Incomes','action' => 'edit',$income['id']),array('class' => 'btn btn-default')),
												$this->Html->link('Delete',array('controller' => 'Incomes','action' => 'delete',$income['id']),array('class' => 'btn btn-default','onclick' => "return confirm(\"Are you Sure?\");"))
												));
	}
	$totalamount = $currency.' '.$total;
	echo <<<EOT
		<h2> Statement No: $statementno </h2>
		$totalamount
		<table class='table table-striped table-hover table-responsive'>
			$tableheader
			$tablecells
		</table>
		$totalamount

EOT;
	echo "<br><br>";
	echo $this->Html->link('Go back to last page',$lastpage,array('class' => 'btn btn-default'));
?>