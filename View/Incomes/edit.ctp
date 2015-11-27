<?php
	echo $this->Form->create('Income', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'label' => array(
				'class' => 'col col-md-2 control-label'
			),
		),
		'class' => 'well form-horizontal',
		'enctype' => 'multipart/form-data',
		'novalidate' => true
	));
	echo $this->Form->input('Income.policy_no', array('label' => 'Policy No:','disabled' => 'disabled'));
	echo $this->Form->input('Income.client', array('label' => 'Client Name:','disabled' => 'disabled'));
	//echo $this->Form->input('Income.type');
	echo $this->Form->input('Income.type', array('type' => 'select','label' => 'Type:', 'options' => $incometypes,'value' => $type));
	echo $this->Form->input('Income.statement_date', array('label' => 'Statement Date:' ,'type' => 'text', 'disabled' => 'disabled'));
	//echo $this->Form->input('Income.currency', array('label' => 'Currency:'));
	echo $this->Form->input('Income.currency', array('type' => 'select','label' => 'Currency:', 'options' => $currencies,'value' => $currency));
	echo $this->Form->input('Income.amount', array('label' => 'Amount:'));
	echo $this->Form->submit('Save', array(
	'class' => 'btn btn-default'));
	echo $this->Form->end();
?>