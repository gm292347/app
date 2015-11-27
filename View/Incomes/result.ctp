<?php
	if ($result['finished']){
		$headers = $this->Html->tableHeaders(array('File Name',
													'Upload Time',
													'Total Sheets',
													'Skipped Sheets',
													'Processed Workbook',
													'Statement Log',
													'Income Processing Log'));
		$details = $this->Html->tableCells(array(
												$result['file_name'],
												$result['upload_time'],
												$result['total_sheets'],
												$result['skipped_sheets'],
												$this->Html->link('Download',array('action' => 'exportIncome',$result['id'])),
												$this->Html->link('Download',array('action' => 'statementlog',$result['id'])),
												$this->Html->link('Download',array('action' => 'incomeprocessinglog',$result['id']))
												)
											);
	}
	else{
		$headers = $this->Html->tableHeaders(array(
													'File Name',
													'Upload Time',
													'Total Sheets',
													'Skipped Sheets',
													'Remaining Sheets',
													'Processed Sheets'
													)
											);
		$details = $this->Html->tableCells(array(
												$result['file_name'],
												$result['upload_time'],
												$result['total_sheets'],
												$result['skipped_sheets'],
												$result['remaining_sheets'],
												$result['total_sheets']-$result['remaining_sheets']
												)
											);

	}
	echo $this->Html->link('Download Month\'s end Income Workbook',array('action' => 'currIncomebook'),array('class' => 'btn btn-default'));
	echo <<<table
		<table class="table table-hover table-responsive table-striped">
		$headers
		$details
		</table>
table;
	if (!$result['total_sheets']){
		echo "<h3>Loading...</h3>";
	}
	$headers2 = $this->Html->tableHeaders(array('id','Time','Message'));
	$details2 = '';
	foreach($logs as $log){
		$details2 .= $this->Html->tableCells(array(
												$log['id'],
												$log['updated_time'],
												$log['message']
												)
											);
	}
	echo "<h3>Logs</h3>";
	echo <<<table2
		<table class="table table-hover table-responsive table-striped">
		$headers2
		$details2
		</table>
table2;

?>