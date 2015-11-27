<?php
	$currency = $incomestatements[0]['Income'][0]['currency'];
	$currenciesnames = array('HKD',
						'USD',
						'GBP',
						'JPY',
						'EUR'
						);
	$tableHeader = array_merge(array('Statement No',
						'Company',
						'Statement Date',
						),$currenciesnames);
	$tableHeader[] = 'Delete All';
	foreach ($incomestatements as $key => $incomestatement){
		$total = array_sum(hash::extract($incomestatement,'Income.{n}.amount'));
		$hkd = 0;
		$usd = 0;
		$gbp = 0;
		$jpy = 0;
		$eur = 0; 
		switch ($incomestatement['Income'][0]['currency']){
			case "HKD":
				$hkd = $total;
				break;
			case "USD":
				$usd = $total;
				break;
			case "GBP":
				$gbp = $total;
				break;
			case "JPY":
				$jpy = $total;
				break;
			case "EUR":
				$eur = $total;
				break;
		}
		// $spaces = array_search($incomestatement['Income'][0]['currency'], $currenciesnames);
		$incomestatement = hash::extract($incomestatement,'IncomeStatement');
		$tableCell[] = array(
							$this->Html->link($incomestatement['statement_no'],array('controller' => 'Incomes','action' => 'viewstatement',$incomestatement['id'])),
							$incomestatement['company'],
							$incomestatement['statement_date'],
							$hkd,
							$usd,
							$gbp,
							$jpy,
							$eur,
							$this->Html->link('Delete',array('controller' => 'Incomes','action' => 'deleteAll',$incomestatement['id']),array('class' => "btn btn-default",'onclick' => "return confirm(\"Are you Sure?\");"))
							);
		/*while ($spaces > 0){
			array_splice($tableCell[$key],4,0,'');
			$spaces--;
		}*/
		//debug($incomestatement);
	}
		
	echo $this->Html->link('export',array('action' => 'outputexcel'),array('class' => 'btn btn-default'));
	$paginationBar = $this->Paginator->pagination(array('ul' => 'pagination'));

	$counter = $this->Paginator->counter();

	echo $this->SearchResult->generateTable($tableHeader, $tableCell, $paginationBar, $counter);
?>