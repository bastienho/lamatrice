<?php

/* +***********************************************************************************
 * *********************************************************************************** */

 //TODO On ne passe jamais par ici !!!
 // à voir si même pblm que l'absence de menu ContactAddresses
class ContactEmails_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$recordModel = $this->record;
		if(!$recordModel){
			/*ED150312*/
			if(!empty($recordId) && $request->get('isDuplicate') == true
			   && $request->get('source_module') == 'Contacts') {
				$sourceModule = Vtiger_Module_Model::getInstance('Contacts');
				$sourceRecord = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
				$recordModel = $sourceRecord->createContactEmailsRecord(false);
				$request->set('isDuplicate', false);
				$request->set('record', null);
			}
			elseif (!empty($recordId)) {
				$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			} else {
				$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			}
		    $this->record = $recordModel;
		}

		parent::process($request);
	}

}