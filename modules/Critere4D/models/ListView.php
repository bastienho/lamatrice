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
 * Vtiger ListView Model Class
 */
class Critere4D_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		
		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule))
			switch($sourceModule){
				case 'Contacts':
					//ED150628 : related to view
					if(!empty($this->get('src_viewname'))){
						$queryGenerator = $this->get('query_generator');
						$viewQuery = $this->getRecordsQueryFromRequest();
						$viewQuery = 'SELECT vtiger_critere4dcontrel.critere4did
							FROM vtiger_critere4dcontrel
							JOIN (' . $viewQuery . ') source_contacts
								ON vtiger_critere4dcontrel.contactid = source_contacts.contactid';
						$queryGenerator->addUserSearchConditions(array('search_field' => 'id', 'search_text' => $viewQuery, 'operator' => 'vwi'));
					}
					break;
			}
		return parent::getListViewEntries($pagingModel);
	}
}