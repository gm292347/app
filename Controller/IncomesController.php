<?php
App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php')); 
class IncomesController extends AppController {
	private function findtypename($type){
		//$type = 'renewal';
		$this->loadModel('Type');
		$data = $this->Type->find('first',array('conditions' => array('short_form LIKE' => $type)));
		if ($data) {
			return $data['Type']['name'];
		}
		else {
			return $type;
		}
	}
	private function originReferer(){
		return $this->Session->read('Search.referer');
	}
	
	private function setReferer(){
		$this->Session->write('Search.referer', $this->referer());
	}


	public function main(){
		if($this->Session->read('Auth.Role') != 'staff'){
		
			throw new ForbiddenException(__("You are not allowed to view this information"));

		}

		$this->loadModel('UniversalBatch');
		// debug($this->UniversalBatch->find('all'));
		$this->Paginator->settings = array(
				'findType' => 'all',
				'order' => array('UniversalBatch.id' => 'desc'),		
				'limit' => 10, 	
				'contain' => array('UniversalBatchType')


			);
		// $this->UniversalBatch->recursive = -1;
		//$this->Income->IncomeStatement->contain = array('Income');
		$universalBatch = $this->Paginator->paginate('UniversalBatch');
		// debug($universalBatch);
		$this->set('universalBatch',$universalBatch);
	}
	public function searchstatement(){
		if ($this->Session->read('Auth.Role') != 'staff') {
		
			throw new ForbiddenException(__("You are not allowed to edit this information"));

		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$conditions = array();
						
			$fields = $this->request->data;
			//debug($fields); exit;
			if ($fields['IncomeStatement']['statement_no']) {
				$conditions['IncomeStatement.statement_no LIKE'] = '%'.$fields['IncomeStatement']['statement_no'].'%';
			}
			//$date = new DateTime();
			//$date->setDate($fields['Income']['release_date']['year'], $fields['Income']['release_date']['month'], $fields['Income']['release_date']['day']);
			//$date->format('Y-m-d');
			//$date = $fields['Income']['release_date']['year'].'-'.$fields['Income']['release_date']['month'].'-'.$fields['Income']['release_date']['day'];
			//debug($date);
			$conditions['IncomeStatement.rel_date'] = $fields['Income']['release_date'];
			//debug($conditions);
			$temp = $this->Session->Read('Search.conditions');
					
			$conditions = Hash::merge($temp,$conditions);
			$this->Session->delete('Search.conditions');
			//$this->Session->clear();
			$this->Session->Write( 'Search.conditions', $conditions );
					
			return $this->redirect(array('action' => 'search_result'));
		}
		//debug($data);
	}

	public function search_result(){
		if($this->Session->read('Auth.Role') != 'staff'){
		
			throw new ForbiddenException(__("You are not allowed to view this information"));

		}

		$this->Paginator->settings = array(
				'findType' => 'all',
				//'order' => array('Policy.policy_number' => 'asc'),		
				'callbacks' => 'none',
				'limit' => 10,
				// 'contain' => array('Income.amount')

			);
		//$this->Income->IncomeStatement->contain = array('Income');
		$incomestatements = $this->Paginator->paginate('IncomeStatement',
				
				//original full search  , was PolicySearch.conditions, now changed to match client search conditions
				$this->Session->Read('Search.conditions')
				
			);

		debug($incomestatements);
		$this->set('incomestatements',$incomestatements);
	}
	public function deleteAll($statementId){
		/*$this->Income->deleteAll();
		$a = $this->Income->IncomeStatement->find('first',array('contain' => array('Income'),'conditions' => array('IncomeStatement.id' => $statementId)));
		debug($a);*/
	}
	public function outputexcel(){
		//debug($this->Session->Read('Search.conditions'));
		$data = $this->Income->IncomeStatement->find('all',array('conditions' => $this->Session->Read('Search.conditions')));
		// debug($data);
		$currenciesname = array('HKD',
								'USD',
								'GBP',
								'JPY',
								'EUR'
								);
		$reldate = $this->Session->Read('Search.conditions');
		$reldate = $reldate['IncomeStatement.rel_date'];
		$rows[] = array('release_date:',$reldate);
		$rows[] = array_merge(array('Statement No','Company','Statement Date'),$currenciesname);
		foreach ($data as $key => $incomestatement) {
			$amount = array_sum(hash::extract($incomestatement,'Income.{n}.amount'));
			$rows[] = array($incomestatement['IncomeStatement']['statement_no'],
						$incomestatement['IncomeStatement']['company'],
						$incomestatement['IncomeStatement']['statement_date'],
						$amount);
			$spaces = array_search($incomestatement['Income'][0]['currency'],$currenciesname);
			while ($spaces > 0){
				array_splice($rows[$key],3,0,'');
				$spaces--;
			}


		}
		$this->layout = null;
    	$this->autoLayout = false;
    	Configure::write('debug','0');
    	//error_reporting(E_ALL);
		$objPHPExcel = new PHPExcel();
	 
		$objPHPExcel->getProperties()->setCreator("Rhoba")
				 ->setLastModifiedBy("Rhoba")
				 ->setTitle("News Report")
				 ->setSubject("News Report");
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
		foreach ($rows as $key => $row) {
				$col = 0;
				debug($row);
				foreach($row as $key2 => $element){
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key2,$key +1,$element);
					$col++;
				}
		}

		foreach(range('D','H') as $col){
			$objPHPExcel->getActiveSheet()
					    ->getStyle($col.'2')
					    ->getAlignment()
					    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$range = $col.'2:'.$col.($key+2);

		}
		foreach(range('A','H') as $col){
				$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}

		$filename = 'search_result';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='.$filename.'.xls');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_clean();
		$objWriter->save('php://output');
		$this->render(false);
		//debug($rows);

	}
	public function viewstatement($incomestatementid){
		if($this->Session->read('Auth.Role') != 'staff'){
		
			throw new ForbiddenException(__("You are not allowed to view this information"));

		}

		$data = $this->Income->IncomeStatement->find('first',array('contain' => array('Income'),'conditions' => array('IncomeStatement.id' => $incomestatementid)));
		$this->set('statementno',$data['IncomeStatement']['statement_no']);
		debug($data);
		$incomes = hash::extract($data,'Income.{n}');
		$this->set('incomes',$incomes);
		$this->Session->write('Search.mainpage',$this->referer());
		$this->set('lastpage',$this->Session->read('Search.mainpage'));
	}
	public function delete($incomeid){
		if($this->Session->read('Auth.Role') != 'staff'){
		
			throw new ForbiddenException(__("You are not allowed to view this information"));

		}
		if ($this->Income->delete($incomeid)){
			$this->Session->setFlash('deleted');
		}
		return $this->redirect($this->referer());

	}
	public function edit($incomeid){
		if($this->Session->read('Auth.Role') != 'staff'){
		
			throw new ForbiddenException(__("You are not allowed to view this information"));

		}
		$this->loadModel('Type');
		$this->loadModel('Currency');
		$income = $this->Income->findById($incomeid);
		$incometypes = $this->Type->find('list',array('fields' => array('name'),'callbacks' => 'none'));
		$incometypes = hash::combine($incometypes,'{n}','{n}');
		$currencies = $this->Currency->find('list',array('fields' => array('name'),'callbacks' => 'none'));
		$currencies = hash::combine($currencies,'{n}','{n}');
		$this->set('incometypes',$incometypes);
		$this->set('currencies',$currencies);
		if ($this->request->is('post') || $this->request->is('put')) {
        	$this->Income->id = $incomeid;
	        if ($this->Income->save($this->request->data)) {
	            $this->Session->setFlash(__('Your edit has been updated.'));
	            return $this->redirect($this->originReferer());
	        }
       		$this->Session->setFlash(__('Unable to update your change.'));
   		}
		else {
			//debug($income);	
        	$this->request->data = $income;
        	$this->set('type',$income['Income']['type']);
        	$this->set('currency',$income['Income']['currency']);
        	$this->setReferer();
    	}

	}
	public function uploadtentativeschedule(){
		if ($this->Session->read('Auth.Role') != 'staff') {
		
			throw new ForbiddenException(__("You are not allowed to edit this information"));

		}
		$this->loadModel('UniversalBatch');
		$folderToSaveFiles = '/var/www/rb_upload/test2/' ;
		// debug($folderToSaveFiles);

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if($this->isUploadedFile($this->request->data['TentativeReleaseSchedule']['Document'])){
				debug($this->request->data);
				$file = $this->request->data['TentativeReleaseSchedule']['Document'][0];
				$extensionlen = strlen($file['name'])-strpos($file['name'],'.');
				// debug($extensionlen);
				$newFilename = substr($file['name'],0,strlen($file['name'])-$extensionlen);
				// debug($newFilename);
				$newFilePath = $folderToSaveFiles . $file['name'];
				$result = move_uploaded_file( $file['tmp_name'], $newFilePath);
				// debug($result);
				if ($result){
					$tentativeScheduleBatch = array('file_name' => $newFilename,
															'file_path' => $newFilePath,
															'upload_time' => date('Y-m-d H:i:s', time()),
															'universal_batch_type_id' => 2);
					$this->UniversalBatch->save($tentativeScheduleBatch);
					$command = 'cd /var/www/html/clare/app;Console/cake uploadincome uploadTentativeSchedule '.$this->UniversalBatch->id.' > test.txt &';
					pclose(popen($command,'r'));
					// return $this->redirect(array('controller' => 'incomeBatches','action' => 'result',$this->Income->IncomeBatch->id));
				}
			}
		}
	}
	public function uploadreleaserawdata(){
		if ($this->Session->read('Auth.Role') != 'staff') {
		
			throw new ForbiddenException(__("You are not allowed to edit this information"));

		}
		$this->loadModel('UniversalBatch');
		$folderToSaveFiles = '/var/www/rb_upload/test2/' ;
		// debug($folderToSaveFiles);

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if($this->isUploadedFile($this->request->data['ReleaseRawData']['Document'])){
				debug($this->request->data);
				$file = $this->request->data['ReleaseRawData']['Document'][0];
				$extensionlen = strlen($file['name'])-strpos($file['name'],'.');
				// debug($extensionlen);
				$newFilename = substr($file['name'],0,strlen($file['name'])-$extensionlen);
				// debug($newFilename);
				$newFilePath = $folderToSaveFiles . $file['name'];
				$result = move_uploaded_file( $file['tmp_name'], $newFilePath);
				// debug($result);
				if ($result){
					$releaseRawDataBatch = array('file_name' => $newFilename,
															'file_path' => $newFilePath,
															'upload_time' => date('Y-m-d H:i:s', time()),
															'universal_batch_type_id' => 2);
					$this->UniversalBatch->save($releaseRawDataBatch);
					$command = 'cd /var/www/html/clare/app;Console/cake uploadincome uploadReleaseRawData '.$this->UniversalBatch->id.' > test.txt &';
					pclose(popen($command,'r'));
					// return $this->redirect(array('controller' => 'incomeBatches','action' => 'result',$this->Income->IncomeBatch->id));
				}
			}
		}

	}
	public function uploadincome() {
		
		
		$folderToSaveFiles = '/var/www/rb_upload/test2/' ;
		debug($folderToSaveFiles);

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if($this->isUploadedFile($this->request->data['Income']['Document'])){
				debug($this->request->data);
				$file = $this->request->data['Income']['Document'][0];
				$extensionlen = strlen($file['name'])-strpos($file['name'],'.');
				debug($extensionlen);
				$newFilename = substr($file['name'],0,strlen($file['name'])-$extensionlen);
				debug($newFilename);
				$newFilePath = $folderToSaveFiles . $file['name'];
				$result = move_uploaded_file( $file['tmp_name'], $newFilePath);
				debug($result);
				if ($result){
					$incomeBatch = array('file_name' => $newFilename,
										'file_path' => $newFilePath,
										'upload_time' => date('Y-m-d H:i:s', time()),
										'universal_batch_type_id' => 1);
					 debug($this->Income->UniversalBatch->save($incomeBatch));

					$command = 'cd /var/www/html/clare/app;Console/cake uploadincome importincome2 '.$this->Income->UniversalBatch->id.' > test.txt &';
					pclose(popen($command,'r'));
					return $this->redirect(array('controller' => 'universalBatches','action' => 'result',$this->Income->UniversalBatch->id));
				}
			}
		}
	}
	public function viewincome(){
		$this->loadModel('MonthEnd');
		$searchDate = $this->MonthEnd->find('first',array('order' => array('id' => 'DESC')));
		$searchDate = $searchDate['MonthEnd']['rel_date'];
		$data = $this->Income->IncomeStatement->find('all',array('recursive' => -1,'conditions' => array('rel_date' => $searchDate,'checked' => 1)));
		$this->set('statements',hash::extract($data,'{n}.IncomeStatement'));
	}
	public function approvestatement(){
		$this->loadModel('MonthEnd');
		$searchDate = $this->MonthEnd->find('first',array('order' => array('id' => 'DESC')));
		$searchDate = $searchDate['MonthEnd']['rel_date'];
		$data = $this->Income->IncomeStatement->find('all',array('recursive' => -1,'order' => array('statement_no' => 'ASC'),'conditions' => array('rel_date' => $searchDate,'checked' => 0)));
		$this->set('statements',hash::extract($data,'{n}.IncomeStatement'));
		if ($this->request->is('post') || $this->request->is('put')){
			$userId = $this->Session->read('Auth.User.id');
			debug($userId);
        	foreach($this->request->data['check'] as $id => $value){
        		$checkLog = array('income_statement_id' => $id,'user_id' => $userId);
        		$this->Income->IncomeStatement->IncomeStatementCheck->save($checkLog);
        		$this->Income->IncomeStatement->IncomeStatementCheck->clear();
        		$this->Income->IncomeStatement->id = $id;
        		$this->Income->IncomeStatement->saveField('checked',1);
        		$this->Income->IncomeStatement->clear();
        	}
        	return $this->redirect(array('controller' => 'incomes','action' => 'approvestatement'));
	       
   		}

	}
	public function releasetentativerelease(){
		ini_set("memory_limit", "-1");
		set_time_limit(0);
		// Configure::write('debug',0);
		$this->loadModel('TentativeRelSchedule');
		$this->loadModel('MonthEnd');
		$searchDate = $this->MonthEnd->find('first',array('order' => array('id' => 'DESC')));

		$searchDate = $searchDate['MonthEnd']['rel_date'];
		// $this->Income->recursive = -1;
		$incomeStatementList = $this->Income->IncomeStatement->find('list',array('recursive' => -1,
																				'fields' => array('statement_no','id'),
																				'order' => array('statement_no' => 'ASC'),
																				'conditions' => array('rel_date' => $searchDate,'checked' => 1)
																				)
																	);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Rhoba")
				    ->setLastModifiedBy("Rhoba")
				    ->setSubject("Tentative_Release_".$searchDate);
		$sheetCount = 0;

		foreach ($incomeStatementList as $key => $statementId) {
			$notEmpty = false;
			$objWorkSheet = $objPHPExcel->createSheet($sheetCount);
			$objWorkSheet->setTitle($key);
			$objPHPExcel->setActiveSheetIndex($sheetCount);
			$rowCount = 1;
			$objPHPExcel->getActiveSheet()->fromArray(array("Statement NO.", $key), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->fromArray(array("***BEGIN"), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->fromArray(array('seq_no',
															'approved',
															'income_id',
															'policy_no',
															'rel_date',
															'consultant',
															'commenced_date',
															'plan_currency',
															'premium',
															'plan',
															'exchange_rate',
															'entitle_percentage',
															'rel_currency',
															'income_currency',
															'income',
															'rel_amount',
															'remarks',
															'',
															'temp_rel_type',
															'inc_type',
															'ten_rel_date',
															'tentative_rel_schedule_id'), NULL, 'A'.$rowCount++);

			$incomeList = $this->Income->find('list',array('recursive' => -1,
											  'fields' => array('id','policy_id'),
											  'order' => array('id' => 'ASC'),
											  'conditions' => array('income_statement_id' => $statementId,'Income.processed' => 0)));

			foreach ($incomeList as $incomeId => $policyId) {
				$tempdate = $this->TentativeRelSchedule->find('first',array('recursive' => -1,
																			'fields' => array('date'),
																			'order' => array('TentativeRelSchedule.date' => 'asc'),
																			'conditions' => array('policy_id' => $policyId,'actualized' => false),
																			'callbacks' => 'before')
																			);
				if ($tempdate == null){
					continue;
				}
				else {
					$notEmpty = true;
					$income = $this->Income->find('first',array('recursive' => -1,
															'fields' => array('release_date','seq_no','id','policy_no','consultant','commenced_date','plan_currency','premium','plan_name','exchange_rate','currency','amount','type'),
															'conditions' => array('id' => $incomeId)));

					$income = hash::extract($income,'Income');
					// debug($income);
					$tentativerelschedule = $this->TentativeRelSchedule->find('all',array(
																						'recursive' => -1,
																						'fields' => array('entitle_percentage','rel_currency','amount','remarks','type','date','id'),
																						'conditions' => array('policy_id' => $policyId,
																											'actualized' => false,
																											'date' => $tempdate['TentativeRelSchedule']['date']
																											),
																						'callbacks' => 'before')
																								);
					foreach ($tentativerelschedule as $value) {
						$value = hash::extract($value,'TentativeRelSchedule');
						// debug($value);
						$objPHPExcel->getActiveSheet()->fromArray(array($income['seq_no'],
																	'',
																	$income['id'],
																	$income['policy_no'],
																	$income['release_date'],
																	$income['consultant'],
																	$income['commenced_date'],
																	$income['plan_currency'],
																	$income['premium'],
																	$income['plan_name'],
																	$income['exchange_rate'],
																	$value['entitle_percentage'],
																	$value['rel_currency'],
																	$income['currency'],
																	$income['amount'],
																	$value['amount'],
																	$value['remarks'],
																	'',
																	$this->findtypename($income['type']),
																	$value['type'],
																	$value['date'],
																	$value['id']), NULL, 'A'.$rowCount++);
					}
				}
			}
			if (!($notEmpty)){
				$objPHPExcel->removeSheetByIndex($sheetCount);
				continue;
			}
			else {
				$sheetCount++;
			}
			// $sheetCount++;
			$objPHPExcel->getActiveSheet()->fromArray(array("***END"), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->getStyle("D1:D".($rowCount+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle("I1:I".($rowCount+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			$objPHPExcel->getActiveSheet()->getStyle("O1:O".($rowCount+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			$objPHPExcel->getActiveSheet()->getStyle("P1:P".($rowCount+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			foreach(range('A','S') as $col){
				$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}
		}
		$objPHPExcel->setActiveSheetIndex(0);
		if ($sheetCount != 0){
			$objPHPExcel->removeSheetByIndex($sheetCount);
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=.'.'TentativeRelease_'.$searchDate.'.xlsx');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_clean();
		$objWriter->save('php://output');
		exit();
		// $this->render(false);


	}
	public function uploadtentativerelease(){
		// $this->UniversalBatch->UniversalBatchError->save(array('message' => 'o','universal_batch_id' => '3'));

		$folderToSaveFiles = '/var/www/rb_upload/test2/' ;
		debug($folderToSaveFiles);
		$this->loadModel('UniversalBatch');
		if ($this->request->is('post') || $this->request->is('put')) {
			
			if($this->isUploadedFile($this->request->data['TentativeRelease']['Document'])){
				debug($this->request->data);
				$file = $this->request->data['TentativeRelease']['Document'][0];
				$extensionlen = strlen($file['name'])-strpos($file['name'],'.');
				debug($extensionlen);
				$newFilename = substr($file['name'],0,strlen($file['name'])-$extensionlen);
				debug($newFilename);
				$newFilePath = $folderToSaveFiles . $file['name'];
				$result = move_uploaded_file($file['tmp_name'], $newFilePath);
				debug($result);
				if ($result){
					$tentativeScheduleBatch = array('file_name' => $newFilename,
										'file_path' => $newFilePath,
										'upload_time' => date('Y-m-d H:i:s', time()),
										'universal_batch_type_id' => 3);
					$this->UniversalBatch->save($tentativeScheduleBatch);
					$command = 'cd /var/www/html/clare/app;Console/cake uploadincome uploadTentativeRel '.$this->UniversalBatch->id.' > test.txt &';
					pclose(popen($command,'r'));
					// return $this->redirect(array('controller' => 'incomeBatches','action' => 'result',$this->Income->IncomeBatch->id));
				}
			}
		}
	}
	public function releaserawdata(){
		// ob_start();
		ini_set("memory_limit", "-1");
		set_time_limit(0);
		Configure::write('debug',0); 	
		$this->loadModel('MonthEnd');
		$releaseDate = $this->MonthEnd->find('first',array('order' => array('id' => 'DESC')));
		$releaseDate = $releaseDate['MonthEnd']['rel_date'];

		$incomeStatementList = $this->Income->IncomeStatement->find('list',array('recursive' => -1,
																				'fields' => array('statement_no','id'),
																				'order' => array('statement_no' => 'ASC'),
																				'conditions' => array('rel_date' => $releaseDate,'checked' => 1)
																				)
																	);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Rhoba")
				    ->setLastModifiedBy("Rhoba")
				    ->setSubject("Tentative_Release_".$releaseDate);
		$sheetCount = 0;

		foreach ($incomeStatementList as $key => $statementId) {
			$notEmpty = false;
			$objWorkSheet = $objPHPExcel->createSheet($sheetCount);
			$objWorkSheet->setTitle($key);
			$objPHPExcel->setActiveSheetIndex($sheetCount);
			$rowCount = 1;
			$objPHPExcel->getActiveSheet()->fromArray(array("Statement NO.", $key), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->fromArray(array("***BEGIN"), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->fromArray(array(
															'income_id',
															'policy_no',
															'rel_date',
															'consultant',
															'commenced_date',
															'plan_currency',
															'premium',
															'plan',
															'exchange_rate',
															'entitle_percentage',
															'rel_currency',
															'income_currency',
															'income',
															'type',
															'rel_amount',
															'remarks',), NULL, 'A'.$rowCount++);

			$incomeList = $this->Income->find('all',array('contain' => array('Policy.consultant_id'),
											  'fields' => array('Income.id','type_id','plan_id'),
											  'order' => array('Income.id' => 'ASC'),
											  'conditions' => array('Income.income_statement_id' => $statementId,'Income.processed' => 0)));

			foreach ($incomeList as $key => $income){
				$result = $this->Income->getreleaserecords($income['Income']['type_id'],$income['Income']['plan_id'],$income['Policy']['consultant_id']);
				if($result){
					$notEmpty = true;
					$incomerecord = $this->Income->find('first',array('recursive' => -1,
																		'fields' => array('id','policy_no','release_date','consultant','commenced_date','plan_currency','premium','plan_name','exchange_rate','currency','amount','type'),
																		'conditions' => array('Income.id' => $income['Income']['id']),
																		));
					$remark = $incomerecord['Income']['type'].": ".$incomerecord['Income']['amount']."*".$result."%";

					$objPHPExcel->getActiveSheet()->fromArray(array(
																	$incomerecord['Income']['id'],
																	$incomerecord['Income']['policy_no'],
																	$incomerecord['Income']['release_date'],
																	$incomerecord['Income']['consultant'],
																	$incomerecord['Income']['commenced_date'],
																	$incomerecord['Income']['plan_currency'],
																	$incomerecord['Income']['premium'],
																	$incomerecord['Income']['plan_name'],
																	$incomerecord['Income']['exchange_rate'],
																	$result,
																	$incomerecord['Income']['currency'],
																	// $tentativeRelease['rel_currency'],
																	$incomerecord['Income']['currency'],
																	$incomerecord['Income']['amount'],
																	$incomerecord['Income']['type'],
																	($incomerecord['Income']['amount']*($result/100)),
																	$remark), NULL, 'A'.$rowCount++);
				}
			}
			if (!($notEmpty)){
				$objPHPExcel->removeSheetByIndex($sheetCount);
				continue;
			}
			else {
				$sheetCount++;
			}
			$objPHPExcel->getActiveSheet()->fromArray(array("***END"), NULL, 'A'.$rowCount++);
			$objPHPExcel->getActiveSheet()->getStyle("B1:B".($key+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle("G1:G".($key+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			$objPHPExcel->getActiveSheet()->getStyle("L1:L".($key+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			$objPHPExcel->getActiveSheet()->getStyle("N1:N".($key+3))->getNumberFormat()->setFormatCode("#,##0.00_);(#,##0.00)");
			foreach(range('A','S') as $col){
				$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}
		}
		$objPHPExcel->setActiveSheetIndex(0);
		if ($sheetCount != 0){
			$objPHPExcel->removeSheetByIndex($sheetCount);
		}
		// exit;
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=.'.'ReleaseRecord_'.$releaseDate.'.xlsx');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_clean();
		$objWriter->save('php://output');
		exit();

		
	}
	public function deletestatement($id){
		$log = $this->Income->IncomeStatement->find('first',array('recursive' => -1,'conditions' => array('IncomeStatement.id' => $id)));
		$userId = $this->Session->read('Auth.User.id'); 
		$this->loadModel('IncomeStatementDeletedLog');
		unset($log['IncomeStatement']['id']);
		$log = $log['IncomeStatement'] + array('previous_income_statement_id' => $id,'user_id' => $userId);
		$this->IncomeStatementDeletedLog->save($log);
		$this->Income->IncomeStatement->delete($id,false);
		$this->Income->deleteAll(array('income_statement_id' => $id),false);
		$this->Income->IncomeProcessingLog->deleteAll(array('IncomeProcessingLog.income_statement_id' => $id),false);
		return $this->redirect(array('controller' => 'incomes','action' => 'viewdeletestatement'));
	}
	public function viewdeletestatement(){
		$this->loadModel('MonthEnd');
		$searchDate = $this->MonthEnd->find('first',array('order' => array('id' => 'DESC')));
		$searchDate = $searchDate['MonthEnd']['rel_date'];
		$data = $this->Income->IncomeStatement->find('all',array('recursive' => -1,'order' => array('statement_no' => 'ASC'),'conditions' => array('rel_date' => $searchDate,'checked' => 0)));
		$this->set('statements',hash::extract($data,'{n}.IncomeStatement'));
	}
	public function createtentativescheduleWB(){
		
		ini_set("memory_limit", "-1");
		set_time_limit(0);
		Configure::write('debug',0);
		$plan = "AIA";
		$currency = "HKD";
		$date = '2015-05-20';
		// $date = str_replace('/', '-', $date);
		$policy_no = "504-7716823";
		$exchange_rate = 1;
		$premium = 1234;

		$consultantRatios = array("co ratio" => array(45.00),
						"Linda Wong" => array(48.00,39.3714,30),
						"R009" => array(42.5905,45.5464,62.45456)); 

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Rhoba")
				    ->setLastModifiedBy("Rhoba")
				    ->setSubject("Tentative_Release_".$releaseDate);
		$sheetCount = 0;


		$objWorkSheet = $objPHPExcel->createSheet($sheetCount);
		$objWorkSheet->setTitle("hi");
		$objPHPExcel->setActiveSheetIndex($sheetCount);
		$rowCount = 1;
		$objPHPExcel->getActiveSheet()->fromArray(array("***BEGIN"), NULL, 'A'.$rowCount++);
		foreach ($consultantRatios as $name => $consultantRatio){
			$count = 0;
			$tempDate = DateTime::createFromFormat('Y-m-d', $date);
			foreach ($consultantRatio as $ratio){
				$count++;
				$tempDate->modify("+1 month");
				if ($count == 1){
					$type = "New Business";
				}
				else {
					$type = "renewal";
				}
				$remark = $type.": ".$premium."*".$exchange_rate."*".$ratio.'%';
				$amount = $premium * $exchange_rate * ($ratio/100);
				$finaldate = PHPExcel_Shared_Date::stringtoExcel($tempDate->format('Y-m-d'));
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rowCount,$finaldate);
				$objPHPExcel->getActiveSheet()        // Format as date and time
						    ->getStyle('A'.$rowCount)
						    ->getNumberFormat()
						    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
				$objPHPExcel->getActiveSheet()->fromArray(array($policy_no,$currency,$exchange_rate,$currency,$amount,$name,$type,$remark), NULL, 'B'.$rowCount++);
			}
			
		}
		$objPHPExcel->getActiveSheet()->fromArray(array("***END"), NULL, 'A'.$rowCount++);
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=.'.'ReleaseRecord_'.$releaseDate.'.xlsx');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_clean();
		$objWriter->save('php://output');
		exit();

		
	}
	public function createtentativeschedule(){
		$this->loadModel('Company');
		$companyList = $this->Company->find('list',array('recursive' => -1,'fields' => array('name')));
		$this->set('companyList',$companyList);
		if ($this->request->is('post') || $this->request->is('put')) {
			debug($this->request->data);
		}
	}


}
?>