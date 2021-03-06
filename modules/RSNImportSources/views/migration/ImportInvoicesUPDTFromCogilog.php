<?php

require_once ('modules/RSNImportSources/views/ImportFromDBCogilog.php');

// Mise à jour des références boutique

class RSNImportSources_ImportInvoicesUPDTFromCogilog_View extends RSNImportSources_ImportFromDBCogilog_View {

	/**
	 * Method to get the source import label to display.
	 * @return string - The label.
	 */
	public function getSource() {
		return 'LBL_COGILOG_INVOICES';
	}


	/**
	 * Method to default max query rows for this import.
	 *  This method should be overload in the child class.
	 * @return string - the default db port.
	 */
	public function getDefaultMaxQueryRows() {
		return 30000;
	}

	/**
	 * Method to get the modules that are concerned by the import.
	 * @return array - An array containing concerned module names.
	 */
	public function getImportModules() {
		return array(/*'Contacts', */'Invoice');
	}

	/**
	 * Method to upload the file in the temporary location.
	 */
	function getDBQuery() {
		/* factures déjà importés */
		$query = "SELECT MAX(CAST(SUBSTR(invoice_no,4) AS UNSIGNED)) AS codefacture_max
			FROM vtiger_invoice
			JOIN vtiger_crmentity
				ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
			WHERE vtiger_crmentity.deleted = 0
			AND invoice_no LIKE 'COG%'
			AND vtiger_crmentity.deleted = 0
			AND importsourceid IS NULL
		";
		$db = PearDatabase::getInstance();
		//$db->setDebug(true);
		$result = $db->pquery($query, array());
		if($db->num_rows($result)){
			$row = $db->fetch_row($result, 0);
			$factMax = intval(substr($row['codefacture_max'], 2));
			$anneeMax = intval(substr($row['codefacture_max'], 0, strlen($row['codefacture_max']) - 5)) + 2000;
			echo('<pre>Dernière facture existante : ' . $anneeMax . ', n° '. $factMax .'</pre>');
		}
		else
			$factMax = false;
		
		$query = 'SELECT facture.numero, facture.annee, facture.reference, facture.datepiece
			FROM gfactu00002 facture
		';
		if(false)
			$query .= ' WHERE facture.numero IN ( 6130)
				AND annee = 2015
			';
		else {
			/* Attention à ne pas importer une facture en cours de saisie */
			$query .= ' WHERE facture.datepiece < CURRENT_DATE 
			';
			if($factMax)
				$query .= ' AND ((facture.numero > '.$factMax.' AND facture.annee = '.$anneeMax.')
				OR facture.annee > '.$anneeMax.')';
		}
		$query .= ' AND facture.reference IS NOT NULL AND facture.reference <> \'\'
			ORDER BY facture.annee, facture.numero
			OFFSET ' . $this->getQueryLimitStart().'
			LIMIT  ' . $this->getMaxQueryRows() ;
		//echo("<pre>$query</pre>");
		return $query;
	}
	
	/**
	 * Method to get db data.
	 */
	function getDBRows() {
		$rows = parent::getDBRows();
		if(!$rows)
			return $rows;
				
		//Identifie les lignes qui sont les en-têtes de factures ou les lignes suivantes de produits
		$fieldName = '_header_';
		$this->columnName_indexes[$fieldName] = count($this->columnName_indexes);
		
		$fieldName = 'numero';
		$id_column = $this->columnName_indexes[$fieldName];
		$previous_id = false;
		$previous_row = -1;
		$new_rows = array();
		$line = 0;
		foreach($rows as $row){
			if($row[$id_column] == $previous_id)
				$row[] = false;//!_header_
			else {
				$row[] = true;//_header_
				$previous_id = $row[$id_column];
				$previous_row = $line;
			}
			$new_rows[] = $row;
			$line++;
		}
		//Supprime la dernière facture car potentiellement toutes les lignes ne sont pas fournies à cause du LIMIT
		if($previous_row > 0
		&& count($new_rows) >= max($this->getMaxQueryRows() - 200, $this->getMaxQueryRows() * 0.8)){ //en espérant qu'il n'y a pas de dernière facture de 201 lignes...
			//$skipped_rows = array_slice($new_rows, $previous_row);
			$new_rows = array_slice($new_rows, 0, $previous_row);
		}
		if(count($new_rows))
			echo "\rNouvelles factures de Cogilog à importer : " . count($new_rows);
		return $new_rows;
	}

	/**
	 * Method to get the imported fields for the invoice module.
	 * @return array - the imported fields for the invoice module.
	 */
	function getInvoiceFields() {
		return array(
			//header
			'sourceid',
			'numero',
			'reference',
			'datepiece',
		);
	}

	/**
	 * Method to process to the import of the invoice module.
	 * @param RSNImportSources_Data_Action $importDataController : an instance of the import data controller.
	 */
	function importInvoice($importDataController) {
		global $VTIGER_BULK_SAVE_MODE;
		$VTIGER_BULK_SAVE_MODE = true;
		$adb = PearDatabase::getInstance();
		$tableName = Import_Utils_Helper::getDbTableName($this->user, 'Invoice');
		$sql = 'SELECT * FROM ' . $tableName . ' WHERE status = '. RSNImportSources_Data_Action::$IMPORT_RECORD_NONE . ' ORDER BY id';

		$result = $adb->query($sql);
		$numberOfRecords = $adb->num_rows($result);

		if ($numberOfRecords <= 0) {
			return;
		}

		$row = $adb->raw_query_result_rowdata($result, 0);
		$previousInvoiceNo = $row['sourceid'];//tmp subject, use invoice_no ???
		$invoiceData = array($row);

		$perf = new RSN_Performance_Helper($numberOfRecords);
		for ($i = 1; $i < $numberOfRecords; ++$i) {
			$row = $adb->raw_query_result_rowdata($result, $i);
			$invoiceNo = $row['sourceid'];

			if ($previousInvoiceNo == $invoiceNo) {
				array_push($invoiceData, $row);
			} else {
				$this->importOneInvoice($invoiceData, $importDataController);
				$invoiceData = array($row);
				$previousInvoiceNo = $invoiceNo;
			}
			
			//perf
			$perf->tick();
			if(Import_Utils_Helper::isMemoryUsageToHigh()){
				$this->skipNextScheduledImports = true;
				$keepScheduledImport = true;
				break;
			}
		}

		//dernière facture
		$this->importOneInvoice($invoiceData, $importDataController);

		$perf->terminate();

	}

	/**
	 * Method to process to the import of a one invoice.
	 * @param $invoiceData : the data of the invoice to import
	 * @param RSNImportSources_Data_Action $importDataController : an instance of the import data controller.
	 */
	function importOneInvoice($invoiceData, $importDataController) {
					
		global $log;
		
		//TODO check sizeof $invoiceata
		$contact = $this->getContact($invoiceData);
		if ($contact != null) {
			$account = $contact->getAccountRecordModel();

			if ($account != null) {
				$sourceId = $invoiceData[0]['sourceid'];
		
				//test sur invoice_no == $sourceId
				$query = "SELECT crmid, invoiceid
					FROM vtiger_invoice
					JOIN vtiger_crmentity
					    ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
					WHERE invoice_no = ? AND deleted = FALSE
					LIMIT 1
				";
				$db = PearDatabase::getInstance();
				$result = $db->pquery($query, array($sourceId));//$invoiceData[0]['subject']
				if($db->num_rows($result)){
					//already imported !!
					$row = $db->fetch_row($result, 0); 
					$entryId = $this->getEntryId("Invoice", $row['crmid']);
					foreach ($invoiceData as $invoiceLine) {
						$entityInfo = array(
							'status'	=> RSNImportSources_Data_Action::$IMPORT_RECORD_SKIPPED,
							'id'		=> $entryId
						);
						
						//TODO update all with array
						$importDataController->updateImportStatus($invoiceLine[id], $entityInfo);
					}
				}
				else {
					$record = Vtiger_Record_Model::getCleanInstance('Invoice');
					$record->set('mode', 'create');
					$record->set('bill_street', $invoiceData[0]['street']);
					$record->set('bill_street2', $invoiceData[0]['street2']);
					$record->set('bill_street3', $invoiceData[0]['street3']);
					$record->set('bill_city', $invoiceData[0]['city']);
					$record->set('bill_code', $invoiceData[0]['zip']);
					$record->set('bill_country', $invoiceData[0]['country']);
					$record->set('subject', $invoiceData[0]['subject']);
					//$record->set('description', $srcRow['notes']);
					$record->set('invoicedate', $invoiceData[0]['invoicedate']);
					$record->set('duedate', $invoiceData[0]['invoicedate']);
					$record->set('contact_id', $contact->getId());
					$record->set('account_id', $account->getId());
					//$record->set('received', str_replace('.', ',', $srcRow['netht']+$srcRow['nettva']));
					//$record->set('hdnGrandTotal', $srcRow['netht']+$srcRow['nettva']);//TODO non enregistré : à cause de l'absence de ligne ?
					$record->set('typedossier', 'Facture'); //TODO
					/*if($invoiceData[0]['solde'] == 0)
						$record->set('invoicestatus', 'Paid');
					else
						$record->set('invoicestatus', 'Approved');//TODO*/
					$record->set('invoicestatus', 'Compta');
					
					$record->set('receivedmoderegl', $invoiceData[0]['modereglement']);
					$record->set('receivedcomments', $srcRow['_receivedcomments']);
					$record->set('currency_id', CURRENCY_ID);
					$record->set('conversion_rate', CONVERSION_RATE);
					$record->set('hdnTaxType', 'individual');
		                    
					$record->set('sent2compta', $invoiceData[0]['invoicedate']);//TODO field displaytype === 2
					
					//Coupon et campagne
					$coupon = $this->getCoupon($invoiceData[0]['affaire_code']);
					if($coupon){
						$record->set('notesid', $coupon->getId());
						$campagne = $this->getCampaign($invoiceData[0]['affaire_code'], $coupon);
						if($campagne)
							$record->set('campaign_no', $campagne->getId());
						
					}
					//Coupon introuvable dans la Matrice
					//TODO log 
					elseif($invoiceData[0]['affaire_code'])
						$record->set('description', 'Code affaire : ' + $invoiceData[0]['affaire_code']);
						
					//$db->setDebug(true);
					$record->saveInBulkMode();
					$invoiceId = $record->getId();

					if(!$invoiceId){
						//TODO: manage error
						echo "<pre><code>Impossible d'enregistrer la nouvelle facture</code></pre>";
						foreach ($invoiceData as $invoiceLine) {
							$entityInfo = array(
								'status'	=>	RSNImportSources_Data_Action::$IMPORT_RECORD_FAILED,
							);
							
							//TODO update all with array
							$importDataController->updateImportStatus($invoiceLine[id], $entityInfo);
						}

						return false;
					}
					
					
					$entryId = $this->getEntryId("Invoice", $invoiceId);
					$sequence = 0;
					$totalAmount = 0.0;
					$totalTax = 0.0;
					foreach ($invoiceData as $invoiceLine) {
						$this->importInvoiceLine($record, $invoiceLine, ++$sequence, $totalAmount, $totalTax);
						$entityInfo = array(
							'status'	=> RSNImportSources_Data_Action::$IMPORT_RECORD_CREATED,
							'id'		=> $entryId
						);
						//TODO update all with array
						$importDataController->updateImportStatus($invoiceLine[id], $entityInfo);
					}
					
					$record->set('mode','edit');
					//This field is not manage by save()
					// et ça fout la merde
					//$record->set('invoice_no',$sourceId);
					//$record->set('received', $totalAmount - $invoiceData[0]['solde']);
					//$record->save();
					
					//set invoice_no
					$query = "UPDATE vtiger_invoice
						JOIN vtiger_invoicecf
							ON vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
						JOIN vtiger_crmentity
							ON vtiger_crmentity.crmid = vtiger_invoice.invoiceid
						SET invoice_no = ?
						, total = ?
						, subtotal = ?
						, taxtype = ?
						, received = ?
						, sent2compta = ?
						, smownerid = ?
						, createdtime = ?
						, modifiedtime = ?
						WHERE vtiger_crmentity.crmid = ?
					";
					$total = round($totalAmount + $totalTax,2);
					$result = $db->pquery($query, array($sourceId
									    , $total
									    , $total
									    , 'individual'
									    , $total - $invoiceData[0]['solde']
									    , $invoiceData[0]['invoicedate']
									    , ASSIGNEDTO_ALL
									    , $invoiceData[0]['invoicedate']
									    , $invoiceData[0]['invoicedate']
									    , $invoiceId));
					
					$log->debug("" . basename(__FILE__) . " update imported invoice (id=" . $record->getId() . ", sourceId=$sourceId , total=$total, date=" . $invoiceData[0]['invoicedate']
						    . ", result=" . ($result ? " true" : "false"). " )");
					if( ! $result)
						$db->echoError();
						
					//raise trigger instead of ->save() whose need invoice rows
					/* ED150831 Migration : is bulk mode
					$log->debug("BEFORE " . basename(__FILE__) . " raise event handler(" . $record->getId() . ", " . $record->get('mode') . " )");
					//raise event handler
					$record->triggerEvent('vtiger.entity.aftersave');
					$log->debug("AFTER " . basename(__FILE__) . " raise event handler");
					*/
					
					return $record; 
				}
			} else {
				//TODO: manage error
				echo "<pre><code>Unable to find Account</code></pre>";
			}
		} else {
			foreach ($invoiceData as $invoiceLine) {//TODO: remove duplicated code
				$entityInfo = array(
					'status'	=>	RSNImportSources_Data_Action::$IMPORT_RECORD_FAILED,
				);
				
				$importDataController->updateImportStatus($invoiceLine[id], $entityInfo);
			}

			return false;
		}

		return true;
	}


	/**
	 * Method that pre import a contact.
	 * @param $contactValues : the values of the contact to import.
	 */
	function preImportContact($contactValues) {
		$contact = new RSNImportSources_Preimport_Model($contactValues, $this->user, 'Contacts');
		$contact->save();
	}

	/**
	 * Method that pre import an invoice.
	 *  It adone row in the temporary pre-import table by invoice line.
	 * @param $invoiceData : the data of the invoice to import.
	 */
	function preImportInvoice($invoiceData) {
		$invoiceValues = $this->getInvoiceValues($invoiceData);
		foreach ($invoiceValues as $invoiceLine) {
			$invoice = new RSNImportSources_Preimport_Model($invoiceLine, $this->user, 'Invoice');
			$invoice->save();
		}
	}
	
	/**
	 * Method to parse the uploaded file and save data to the temporary pre-import table.
	 * @param RSNImportSources_FileReader_Reader $filereader : the reader of the uploaded file.
	 * @return boolean - true if pre-import is ended successfully
	 */
	function parseAndSaveFile(RSNImportSources_FileReader_Reader $fileReader) {
		$this->clearPreImportTable();

		if($fileReader->open()) {
			if ($this->moveCursorToNextInvoice($fileReader)) {
				$i = 0;
				do {
					$invoice = $this->getNextInvoice($fileReader);
					if ($invoice != null) {
						$this->preImportInvoice($invoice);
					}
					$i++;
				} while ($invoice != null);
			}
			$fileReader->close();
			
			echo('preImportInvoice count : ' . print_r($i, true));

			return true;
		} else {
			//TODO: manage error
			echo "<code>le fichier n'a pas pu être ouvert...</code>";
		}
		
		return false;
	}
        

	/**
	 * Method that check if a line of the file is a client information line.
	 *  It assume that the line is a client information line only and only if the first data is a date.
	 * @param array $line : the data of the file line.
	 * @return boolean - true if the line is a client information line.
	 */
	function isRecordHeaderInformationLine($line) {
		if (sizeof($line) > 0 && $line[$this->columnName_indexes['_header_']]) {
			return true;
		}
		return false;
	}

	/**
	 * Method that move the cursor of the file reader to the beginning of the next found invoice.
	 * @param RSNImportSources_FileReader_Reader $filereader : the reader of the uploaded file.
	 * @return boolean - false if error or if no invoice found.
	 */
	function moveCursorToNextInvoice(RSNImportSources_FileReader_Reader $fileReader) {
		do {
			$cursorPosition = $fileReader->getCurentCursorPosition();
			$nextLine = $fileReader->readNextDataLine($fileReader);

			if ($nextLine == false) {
				return false;
			}

		} while(!$this->isRecordHeaderInformationLine($nextLine));

		$fileReader->moveCursorTo($cursorPosition);

		return true;
	}

	/**
	 * Method that return the information of the next first invoice found in the file.
	 * @param RSNImportSources_FileReader_Reader $filereader : the reader of the uploaded file.
	 * @return the invoice information | null if no invoice found.
	 */
	function getNextInvoice(RSNImportSources_FileReader_Reader $fileReader) {
		$nextLine = $fileReader->readNextDataLine($fileReader);
		if ($nextLine != false) {
			$invoice = array(
				'invoiceInformations' => $nextLine,
				'detail' => array($nextLine));
			do {
				$cursorPosition = $fileReader->getCurentCursorPosition();
				$nextLine = $fileReader->readNextDataLine($fileReader);

				if (!$this->isRecordHeaderInformationLine($nextLine)) {
				} else {
					break;
				}

			} while ($nextLine != false);

			if ($nextLine != false) {
				$fileReader->moveCursorTo($cursorPosition);
			}

			return $invoice;
		}

		return null;
	}
	/**
	 * Method that returns a formatted date for mysql (Y-m-d).
	 * @param string $string : the string to format.
	 * @return string - formated date.
	 */
	function getMySQLDate($string) {
		return $string;
		//$dateArray = preg_split('/[-\/]/', $string);
		//return $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
	}

	/**
	 * Method that return the formated information of an invoice found in the file.
	 * @param $invoice : the invoice data found in the file.
	 * @return array : the formated data of the invoice.
	 */
	function getInvoiceValues($invoice) {
		$invoiceInformations = $invoice['invoiceInformations'];
		$invoiceValues = array();
		$date = $this->getMySQLDate($invoiceInformations[$this->columnName_indexes['datepiece']]);
		$invoiceHeader = array(
			'sourceid'		=> 'COG' . substr($date, 2, 2) . str_pad ($invoiceInformations[$this->columnName_indexes['numero']], 5, '0', STR_PAD_LEFT),
			'numero'	=> $invoiceInformations[$this->columnName_indexes['numero']],
			'reference'	=> $invoiceInformations[$this->columnName_indexes['reference']],
			'datepiece'	=> $invoiceInformations[$this->columnName_indexes['datepiece']],
			
		);
		$invoiceValues[] = $invoiceHeader;
		//var_dump($invoiceValues);
		return $invoiceValues;
	}
	
	/**
	 * Method called after the file is processed.
	 *  This method must be overload in the child class.
	 */
	function postPreImportData() {
		// Pré-identifie les factures //
		
		$db = PearDatabase::getInstance();
		$tableName = RSNImportSources_Utils_Helper::getDbTableName($this->user, 'Invoice');
		
		/* création d'un index */
		$query = "ALTER TABLE `$tableName` ADD INDEX(`sourceid`)";
		$db->pquery($query);
		
		/* Identifie les factures déjà importées
		*/
		$query = "UPDATE $tableName
		JOIN  vtiger_invoice
			ON  vtiger_invoice.invoice_no = `$tableName`.sourceid
		JOIN  vtiger_invoicecf
			ON  vtiger_invoice.invoiceid = `vtiger_invoicecf`.invoiceid
		JOIN vtiger_crmentity
			ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
		";
		$query .= " SET `$tableName`.status = ?
		, `$tableName`.recordid = vtiger_invoice.invoiceid
		, `vtiger_invoicecf`.importsourceid = `$tableName`.reference";
		$query .= "
			WHERE vtiger_crmentity.deleted = 0
			AND `$tableName`.status = ".RSNImportSources_Data_Action::$IMPORT_RECORD_NONE."
		";
		$result = $db->pquery($query, array(RSNImportSources_Data_Action::$IMPORT_RECORD_UPDATED));
		if(!$result){
			echo '<br><br><br><br>';
			$db->echoError($query);
			echo("<pre>$query</pre>");
			die();
		}
		
	}
}
