<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger QuickCreate Record Structure Model
 */
class Vtiger_QuickCreateRecordStructure_Model extends Vtiger_RecordStructure_Model {

	/**
	 * Function to get the values in stuctured format
	 * @return <array> - values in structure array('block'=>array(fieldinfo));
	 */
	public function getStructure() {
		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}

		$values = array();
		$recordModel = $this->getRecord();
		$moduleModel = $this->getModule();

		$fieldModelList = $moduleModel->getQuickCreateFields();
		foreach($fieldModelList as $fieldName=>$fieldModel) {
			$recordModelFieldValue = $recordModel->get($fieldName);
			if(!empty($recordModelFieldValue)) {
			    $fieldModel->set('fieldvalue', $recordModelFieldValue);
			}else{
			    $defaultValue = $fieldModel->getDefaultFieldValue();
			    if($defaultValue) {
				$fieldModel->set('fieldvalue', $defaultValue);
			    }
			}
			$values[$fieldName] = $fieldModel;
		}
		$this->structuredValues = $values;
		return $values;
	}
}