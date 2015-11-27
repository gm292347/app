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
	echo $this->Form->create('ReleaseRawData', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'label' => array(
				'class' => 'col col-md-2 control-label'
			),
			'wrapInput' => 'col col-md-8',
			'class' => 'form-control'
		),
		'class' => 'well form-horizontal',
		'enctype' => 'multipart/form-data',
		'novalidate' => true
	));

	echo $this->Form->input('Document.', array('label' =>__('Upload Release Raw Data'),'type' => 'file', 'multiple'));

	echo "<div class='form-group'>";

	echo $this->Form->submit(__('Update'), array(
		'div' => 'col col-md-8 col-md-offset-2',
		'class' => 'btn btn-default'
	));

	echo "</div>";

	echo $this->Form->end();
?>