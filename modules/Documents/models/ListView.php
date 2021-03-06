<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	public function getListViewLinks($linkParams) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

		$createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
		if($createPermission) {
			$basicLinks = array(
					array(
							'linktype' => 'LISTVIEWBASIC',
							'linklabel' => 'LBL_ADD_RECORD',
							'linkurl' => 'javascript:Vtiger_List_Js.triggerAddRecord(event, "'.$moduleModel->getCreateRecordUrl().'")',
							'linkicon' => ''
					),
					array(
							'linktype' => 'LISTVIEWBASIC',
							'linklabel' => 'LBL_ADD_FOLDER',
							'linkurl' => 'javascript:Documents_List_Js.triggerAddFolder("'.$moduleModel->getAddFolderUrl().'")',
							'linkicon' => ''
					)
			);
			foreach($basicLinks as $basicLink) {
				$links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
			}
		}

		$exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
		if($exportPermission) {
			$advancedLink = array(
					'linktype' => 'LISTVIEW',
					'linklabel' => 'LBL_EXPORT',
					'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$moduleModel->getExportUrl().'")',
					'linkicon' => ''
			);
			$links['LISTVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($advancedLink);
		}

		if($currentUserModel->isAdminUser()) {
			$settingsLinks = $this->getSettingLinks();
			foreach($settingsLinks as $settingsLink) {
				$links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
			}
		}
		return $links;
	}

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	public function getListViewMassActions($linkParams) {
		$currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		$moduleModel = $this->getModule();

		$linkTypes = array('LISTVIEWMASSACTION');
		$links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
		
		if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
			$massActionLink = array(
				'linktype' => 'LISTVIEWMASSACTION',
				'linklabel' => 'LBL_DELETE',
				'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module=' . $moduleModel->getName() . '&action=MassDelete");',
				'linkicon' => ''
			);

			$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
		}

		$massActionLink = array(
			'linktype' => 'LISTVIEWMASSACTION',
			'linklabel' => 'LBL_MOVE',
			'linkurl' => 'javascript:Documents_List_Js.massMove("index.php?module=' . $moduleModel->getName() . '&view=MoveDocuments");',
			'linkicon' => ''
		);

		$links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);

		return $links;
	}

	/** 
	 * Function to set the list view search conditions.
	 * @param Vtiger_Paging_Model $pagingModel
	 *
	 * add folder filter
	 */
	protected function setListViewSearchConditions($pagingModel = false) {

		$queryGenerator = $this->get('query_generator');

		$folderKey = $this->get('folder_id');
		$folderValue = $this->get('folder_value');
		if(!empty($folderValue)) {
		    $queryGenerator->addCondition($folderKey,$folderValue,'e'); //ED141018 Ici bug vtiger_attachmentsfolderfolderid		    
		}
		
		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)){
			switch($sourceModule){
			case 'Contacts':
				//ED150628 : related to view
				$src_viewname = $this->get('src_viewname');
				if(!empty($src_viewname)){
					$queryGenerator = $this->get('query_generator');
					$viewQuery = $this->getRecordsQueryFromRequest();
					$viewQuery = 'SELECT vtiger_senotesrel.notesid
						FROM vtiger_senotesrel
						JOIN (' . $viewQuery . ') source_contacts
							ON vtiger_senotesrel.crmid = source_contacts.contactid';
					$queryGenerator->addUserSearchConditions(array('search_field' => 'id', 'search_text' => $viewQuery, 'operator' => 'vwi'));
				}
				break;
			}
		}

		parent::setListViewSearchConditions($pagingModel);
	}
	
	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {

		$db = PearDatabase::getInstance();

		$moduleName = $this->getModule()->get('name');
		$moduleFocus = CRMEntity::getInstance($moduleName);
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		
		$listViewContoller = $this->get('listview_controller');
		
		$this->setListViewSearchConditions($pagingModel);
		
		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			$orderBy = 'modifiedtime';
			$sortOrder = 'DESC';
		}

		if(!empty($orderBy)){
		    $columnFieldMapping = $moduleModel->getColumnFieldMapping();
		    $orderByFieldName = $columnFieldMapping[$orderBy];
		    $orderByFieldModel = $moduleModel->getField($orderByFieldName);
		    if($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE){
				//IF it is reference add it in the where fields so that from clause will be having join of the table
				$queryGenerator = $this->get('query_generator');
				$queryGenerator->addWhereField($orderByFieldName);
				//$queryGenerator->whereFields[] = $orderByFieldName;
		    }
		}

		$listQuery = $this->getQuery();

		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		if(!empty($orderBy)) {
			if($orderByFieldModel && $orderByFieldModel->isReferenceField()){
			    $referenceModules = $orderByFieldModel->getReferenceList();
			    $referenceNameFieldOrderBy = array();
			    foreach($referenceModules as $referenceModuleName) {
					$referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
					$referenceNameFields = $referenceModuleModel->getNameFields();
			
					$columnList = array();
					foreach($referenceNameFields as $nameField) {
						$fieldModel = $referenceModuleModel->getField($nameField);
						$columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
					}
					if(count($columnList) > 1) {
						$referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users').' '.$sortOrder;
					} else {
						$referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
					}
			    }
			    $listQuery .= ' ORDER BY '. implode(',',$referenceNameFieldOrderBy);
			}else{
			    $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
			}
		}
		$viewid = ListViewSession::getCurrentView($moduleName);
		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);

		$listResult = $db->pquery($listQuery, array());
		
		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult, $this->get('view_context'));

		$pagingModel->calculatePageRange($listViewEntries);

		if($db->num_rows($listResult) > $pageLimit){
			array_pop($listViewEntries);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}

		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$record['uicolor'] = $rawData['uicolor'];
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
		}
		return $listViewRecordModels;
	}
	
	function getQuery() {
		$queryGenerator = $this->get('query_generator');
		$listQuery = $queryGenerator->getQuery();
		/* ED141010 get uicolor field also */
		$matches = array();
		if(strpos($listQuery,'vtiger_attachmentsfolderfolderid'))
			$listQuery = preg_replace('/^SELECT\s/', 'SELECT vtiger_attachmentsfolderfolderid.uicolor, ', $listQuery, 1);
		elseif(strpos($listQuery,'vtiger_attachmentsfolder'))
			$listQuery = preg_replace('/^SELECT\s/', 'SELECT vtiger_attachmentsfolder.uicolor, ', $listQuery, 1);
		return $listQuery;
	}

}
