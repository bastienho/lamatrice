<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class Vtiger_Field_Model extends Vtiger_Field {

	var $webserviceField = false;

	const REFERENCE_TYPE = 'reference';
	const OWNER_TYPE = 'owner';
	
	const QUICKCREATE_MANDATORY = 0;
	const QUICKCREATE_NOT_ENABLED = 1;
	const QUICKCREATE_ENABLED = 2;
	const QUICKCREATE_NOT_PERMITTED = 3;

	/**
	 * Function to get the value of a given property
	 * @param <String> $propertyName
	 * @return <Object>
	 * @throws Exception
	 */
	public function get($propertyName) {
		if(property_exists($this,$propertyName)) {
			return $this->$propertyName;
		}
		return null;
	}

	/**
	 * Function which sets value for given name
	 * @param <String> $name - name for which value need to be assinged
	 * @param <type> $value - values that need to be assigned
	 * @return Vtiger_Field_Model
	 */
	public function set($name, $value) {
		$this->$name = $value;
		return $this;
	}

	/**
	 * Function to get the Field Id
	 * @return <Number>
	 */
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getFieldName() {
		return $this->name;
	}

	/* ED150829
	 * Nom du picklist correspond au champ
	*/
	public function getPickListName() {
		//ED160108
		return Vtiger_Util_Helper::getRelatedFieldName($this->getName());
		//switch($this->getName()){
		// case 'mailingstate': //module Contacts
		// case 'otherstate': //module Contacts
		// case 'bill_state': //modules
		// case 'ship_state': //modules
		// case 'address_state': //module Users 
		// case 'state': //module Contacts
		//	return 'rsnregion';
		// case 'bill_country':
		// case 'ship_country':
		// case 'country':
		// case 'mailingcountry':
		// case 'othercountry':
		// case 'address_country':
		//	return 'rsncountry';
		// case 'bill_city':
		// case 'ship_city':
		// case 'city':
		// case 'mailingcity':
		// case 'othercity':
		// case 'address_city':
		// 	return 'rsncity';
		// case 'mailingzip':
		// 	return 'rsnzipcode';
		// case 'rsnmoderegl':
		// 	return 'receivedmoderegl';
		//
		// default:
		//	return $this->getName();
		//}
	}

	/**
	 * Function to retrieve full data
	 * @return <array>
	 */
	public function getData(){
		return get_object_vars($this);
	}

	public function getModule() {
		if(!$this->module) {
			$moduleObj = $this->block->module;
			$this->module = Vtiger_Module_Model::getInstanceFromModuleObject($moduleObj);
		}
		return $this->module;
	}

	public function setModule($moduleInstance) {
	    $this->module = $moduleInstance;
	}

	/**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false) {
		if(!$this->uitype_instance) {
			$this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
		}
		$uiTypeInstance = $this->uitype_instance;
		return $uiTypeInstance->getDisplayValue($value, $record, $recordInstance);
	}

	/**
	 * Function to retrieve display type of a field
	 * @return <String> - display type of the field
	 */
	public function getDisplayType() {
		return $this->get('displaytype');
	}

	/**
	 * Function to get the Webservice Field Object for the current Field Object
	 * @return WebserviceField instance
	 */
	public function getWebserviceFieldObject() {
		if($this->webserviceField == false) {
			$db = PearDatabase::getInstance();

			$row = array();
			$row['uitype'] = $this->get('uitype');
			$row['block'] = $this->get('block');
			$row['tablename'] = $this->get('table');
			$row['columnname'] = $this->get('column');
			$row['fieldname'] = $this->get('name');
			$row['fieldlabel'] = $this->get('label');
			$row['displaytype'] = $this->get('displaytype');
			$row['masseditable'] = $this->get('masseditable');
			$row['typeofdata'] = $this->get('typeofdata');
			$row['presence'] = $this->get('presence');
			$row['tabid'] = $this->getModuleId();
			$row['fieldid'] = $this->get('id');
			$row['readonly'] = !$this->getProfileReadWritePermission();
			$row['defaultvalue'] = $this->get('defaultvalue');

			$this->webserviceField = WebserviceField::fromArray($db, $row);
		}
		return $this->webserviceField;
	}

	/**
	 * Function to get the Webservice Field data type
	 * @return <String> Data type of the field
	 */
	public function getFieldDataType() {
		if(!$this->fieldDataType) {
			$uiType = $this->get('uitype');
			switch($uiType){
			case '69' :
				$fieldDataType = 'image';
				break;
			case '26' :
				$fieldDataType = 'documentsFolder';
				break;
			case '27' :
				$fieldDataType = 'fileLocationType';
				break;
			case '9' :
				$fieldDataType = 'percentage';
				break;
			
			//ED150812
			case '7' :
				$fieldDataType = 'numeric';
				break;
			
			case '28' :
				$fieldDataType = 'documentsFileUpload';
				break;
			case '83' :
				$fieldDataType = 'productTax';
				break;
			case '117' :
				$fieldDataType = 'currencyList';
				break;
			case '55' :
				switch( $this->getName() ){
				case 'salutationtype':
					$fieldDataType = 'picklist';
					break;
				case 'firstname' :
					$fieldDataType = 'salutation';
					break;
				default:
					$webserviceField = $this->getWebserviceFieldObject();
					$fieldDataType = $webserviceField->getFieldDataType();
					break;
				}
			default:
				$webserviceField = $this->getWebserviceFieldObject();
				$fieldDataType = $webserviceField->getFieldDataType();
				break;
			}
			$this->fieldDataType = $fieldDataType;
		}
		return $this->fieldDataType;
	}

	/**
	 * Function to get list of modules the field refernced to
	 * @return <Array> -  list of modules for which field is refered to
	 */
	public function getReferenceList() {
		$webserviceField = $this->getWebserviceFieldObject();
		return $webserviceField->getReferenceList();
	}

	/**
	 * Function to check if the field is named field of the module
	 * @return <Boolean> - True/False
	 */
	public function isNameField() {
		$nameFieldObject = Vtiger_Cache::get('EntityField',$this->getModuleName());
		if($nameFieldObject) {
			$moduleEntityNameFields = explode(',', $nameFieldObject->fieldname);
			if(in_array($this->get('name'), $moduleEntityNameFields)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Function to check whether the current field is read-only
	 * @return <Boolean> - true/false
	 */
	public function isReadOnly() {
		$webserviceField = $this->getWebserviceFieldObject();
		return $webserviceField->isReadOnly();
	}

	/**
	 * Function to get the UI Type model for the uitype of the current field
	 * @return Vtiger_Base_UIType or UI Type specific model instance
	 */
	public function getUITypeModel() {
		return Vtiger_Base_UIType::getInstanceFromField($this);
	}

    public function isRoleBased() {
		//ED150713 TODO REACTIVER !
		//Plantage pour non-admin sur Invoice->typedossier
		return false;
		
        if($this->get('uitype') == '15' || $this->get('uitype') == '33' || ($this->get('uitype') == '55' && $this->getFieldName() == 'salutationtype')) {
            return true;
        }
        return false;
    }

	/**
	 * Function to get all the available picklist values for the current field
	 * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
	 * ED141128 : $picklistvaluesdata  returns uicolor, uiicon, ...
	 */
	public function getPicklistValues(&$picklistvaluesdata = FALSE) {
		// AV150415: Do not return data if field is asynchronous.
		if(strpos($this->uiclass, 'ui-async') === false) {
			$fieldDataType = $this->getFieldDataType();
			if($this->getName() == 'hdnTaxType') return null;
		
			switch($fieldDataType){
			case 'picklist':
			case 'multipicklist':
				$fieldPickListValues = array();
				$currentUser = Users_Record_Model::getCurrentUserModel();
				if($this->isRoleBased() && !$currentUser->isAdminUser()) {
				    $userModel = Users_Record_Model::getCurrentUserModel();
					$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($this->getName(), $userModel->get('roleid'), $picklistvaluesdata);
				}else{
				    $picklistValues = Vtiger_Util_Helper::getPickListValues($this->getName(), $picklistvaluesdata);
				}
				foreach($picklistValues as $value) {
					$fieldPickListValues[$value] = vtranslate($value,$this->getModuleName());
				}
				return $fieldPickListValues;
			case 'buttonSet':
				$fieldPickListValues = array();
				$module = $this->getModule();
				$picklistValues = $module->getPicklistValuesDetails($this->getName());
				foreach($picklistValues as $valueKey=>$value) {
					$fieldPickListValues[$valueKey] = vtranslate($value['label'],$this->getModuleName());
				}
				return $fieldPickListValues;
			default:
				break;
			}
		}

		return null;
    }
	/**
	 * Function to get all the available picklist values for the current field in a CustomView context
	 * @return <Array> List of picklist values if the field is of type picklist or multipicklist, null otherwise.
	 */
	public function getPicklistValuesForCustomView(&$picklistvaluesdata = FALSE){
		return $this->getPicklistValues($picklistvaluesdata);
	}

	/**
	 * Function to check if the current field is mandatory or not
	 * @return <Boolean> - true/false
	 */
	public function isMandatory() {
		list($type,$mandatory)= explode('~',$this->get('typeofdata'));
		return $mandatory=='M' ? true:false;
	}

	/**
	 * Function to get the field type
	 * @return <String> type of the field
	 */
	public function getFieldType(){
		$webserviceField = $this->getWebserviceFieldObject();
		return $webserviceField->getFieldType();
	}

	/**
	 * Function to check if the field is shown in detail view
	 * @return <Boolean> - true/false
	 */
	public function isViewEnabled() {
        $permision = $this->getPermissions();
        if ($this->getDisplayType() == '4' || in_array($this->get('presence'), array(1, 3))) {
            return false;
        }
        return $permision;
    }


	/**
	 * Function to check if the field is shown in detail view
	 * @return <Boolean> - true/false
	 */
	public function isViewable() {
		if(!$this->isViewEnabled()) {
			return false;
		}
		return true;
	}

	/**
	 * Function to check if the field is shown in detail view
	 * @return <Boolean> - true/false
	 */
	public function isViewableInDetailView() {
		if(!$this->isViewable() || $this->getDisplayType() == '3' || $this->getDisplayType() == '5') {
			return false;
		}
		return true;
	}

	public function isEditEnabled() {
		$displayType = (int)$this->get('displaytype');
		$editEnabledDisplayTypes = array(1,3);
		if(!$this->isViewEnabled() ||
				!in_array($displayType, $editEnabledDisplayTypes) ||
				strcasecmp($this->getFieldDataType(),"autogenerated") ===0 ||
				strcasecmp($this->getFieldDataType(),"id") ===0) {

			return false;
		}
		return true;
	}

	public function isQuickCreateEnabled() {
		$quickCreate = $this->get('quickcreate');
		if(($quickCreate == self::QUICKCREATE_MANDATORY || $quickCreate == self::QUICKCREATE_ENABLED
		|| $this->isMandatory()) && $this->get('uitype') != 69) {
			$moduleModel = $this->getModule();
		    //isQuickCreateSupported will not be there for settings
			if(method_exists($moduleModel,'isQuickCreateSupported') && $moduleModel->isQuickCreateSupported()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Function to check whether summary field or not
	 * @return <Boolean> true/false
	 */
	 public function isSummaryField() {
		 return ($this->get('summaryfield')) ? true : false;
	}

	/**
	 * Function to check whether the current field is editable
	 * @return <Boolean> - true/false
	 */
	public function isEditable() {
		if(!$this->isEditEnabled()
		|| !$this->isViewable()
		|| ((int)$this->get('displaytype')) != 1
		|| $this->isReadOnly() == true
		|| $this->get('uitype') ==  4) {
			/*if(!$this->isEditEnabled())
				echo $this->getName() . ' ! isEditEnabled';
			elseif(!$this->isViewable())
				echo $this->getName() . ' ! isViewable';
			elseif(((int)$this->get('displaytype')) != 1)
				echo $this->getName() . ' displaytype != 1';
			elseif($this->isReadOnly() == true)
				echo $this->getName() . ' isReadOnly';
			elseif($this->get('uitype') ==  4)
				echo $this->getName() . ' uitype 4';
			*/
			return false;
		}
		return true;
	}

	/**
	 * Function to check whether field is ajax editable'
	 * @return <Boolean>
	 */
	public function isAjaxEditable() {
		$ajaxRestrictedFields = array('4', '72');
		if(!$this->isEditable() || in_array($this->get('uitype'), $ajaxRestrictedFields)) {
			return false;
		}
		return true;
	}

	/**
	 * Static Function to get the instance fo Vtiger Field Model from a given Vtiger_Field object
	 * @param Vtiger_Field $fieldObj - vtlib field object
	 * @return Vtiger_Field_Model instance
	 */
	public static function getInstanceFromFieldObject(Vtiger_Field $fieldObj) {
		$objectProperties = get_object_vars($fieldObj);
		$className = Vtiger_Loader::getComponentClassName('Model', 'Field', $fieldObj->getModuleName());
		$fieldModel = new $className();
		foreach($objectProperties as $properName=>$propertyValue) {
			$fieldModel->$properName = $propertyValue;
		}
		return $fieldModel;
	}

	/**
	 * Function to get the custom view column name transformation of the field for a date field used in date filters
	 * @return <String> - tablename:columnname:fieldname:module_fieldlabel
	 */
	public function getCVDateFilterColumnName() {
		$moduleName = $this->getModuleName();
		$tableName = $this->get('table');
		$columnName = $this->get('column');
		$fieldName = $this->get('name');
		$fieldLabel = $this->get('label');

		$escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
		$moduleFieldLabel = $moduleName.'_'.$escapedFieldLabel;

		return $tableName.':'.$columnName.':'.$fieldName.':'.$moduleFieldLabel;
	}

	/**
	 * Function to get the custom view column name transformation of the field
	 * @return <String> - tablename:columnname:fieldname:module_fieldlabel:fieldtype
	 */
	public function getCustomViewColumnName() {
		$moduleName = $this->getModuleName();
		$tableName = $this->get('table');
		$columnName = $this->get('column');
		$fieldName = $this->get('name');
		$fieldLabel = $this->get('label');
		$typeOfData = $this->get('typeofdata');

		$fieldTypeOfData = explode('~', $typeOfData);
		$fieldType = $fieldTypeOfData[0];

		//Special condition need for reference field as they should be treated as string field
		if($this->getFieldDataType() == 'reference') {
			$fieldType = 'V';
		} else {
			$fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
		}

		$escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
		$moduleFieldLabel = $moduleName.'_'.$escapedFieldLabel;

		return $tableName.':'.$columnName.':'.$fieldName.':'.$moduleFieldLabel.':'.$fieldType;
	}

	/**
	 * Function to get the Report column name transformation of the field
	 * @return <String> - tablename:columnname:module_fieldlabel:fieldname:fieldtype
	 */
	public function getReportFilterColumnName() {
		$moduleName = $this->getModuleName();
		$tableName = $this->get('table');
		$columnName = $this->get('column');
		$fieldName = $this->get('name');
		$fieldLabel = $this->get('label');
		$typeOfData = $this->get('typeofdata');

		$fieldTypeOfData = explode('~', $typeOfData);
		$fieldType = $fieldTypeOfData[0];
		if($this->getFieldDataType() == 'reference') {
			$fieldType = 'V';
		} else {
			$fieldType = ChangeTypeOfData_Filter($tableName, $columnName, $fieldType);
		}
		$escapedFieldLabel = str_replace(' ', '_', $fieldLabel);
		$moduleFieldLabel = $moduleName.'_'.$escapedFieldLabel;

		if($tableName == 'vtiger_crmentity' && $columnName !='smownerid'){
			$tableName = 'vtiger_crmentity'.$moduleName;
		} elseif($columnName == 'smownerid') {
			$tableName = 'vtiger_users'.$moduleName;
			$columnName ='user_name';
		}

		return $tableName.':'.$columnName.':'.$moduleFieldLabel.':'.$fieldName.':'.$fieldType;
	}

	/**
	 * This is set from Workflow Record Structure, since workflow expects the field name
	 * in a different format in its filter. Eg: for module field its fieldname and for reference
	 * fields its reference_field_name : (reference_module_name) field - salesorder_id: (SalesOrder) subject
	 * @return <String>
	 */
	function getWorkFlowFilterColumnName() {
		return $this->get('workflow_columnname');
	}

	
	/**
	 * Function to get the field details
	 * @return <Array> - array of field values
	 */
	public function getFieldInfo() {
		return $this->getFieldInfoForContext();
	}
	
	/** ED151025
	 * Function to get the field details for custom view context
	 * @return <Array> - array of field values
	 *
	 * First use with RSNStatisticsFields, editing CustomView
	 * Used to change PickListValues
	 */
	public function getFieldInfoForCustomView() {
		return $this->getFieldInfoForContext('CustomView');
	}
	/** ED151025
	 * Function to get the field details
	 * @return <Array> - array of field values
	 */
	public function getFieldInfoForContext($context = false) {
		
		//ED151107 : utilisation du cache, car chargements multiples pour l'éditeur de CustomView
		$fieldInfo = Vtiger_Cache::get('FieldInfoForContext::'.$context, $this->id);
		if($fieldInfo){
			if(is_array($this->fieldInfo))
				return array_merge($this->fieldInfo, $fieldInfo);
			return $fieldInfo;
		}
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$fieldDataType = $this->getFieldDataType();
		$this->fieldInfo['mandatory'] = $this->isMandatory();
		$this->fieldInfo['presence'] = $this->isActiveField();
		$this->fieldInfo['quickcreate'] = $this->isQuickCreateEnabled();
		$this->fieldInfo['masseditable'] = $this->isMassEditable();
		$this->fieldInfo['defaultvalue'] = $this->hasDefaultValue();
		$this->fieldInfo['type'] = $fieldDataType;
		$this->fieldInfo['name'] = $this->get('name');
		$this->fieldInfo['label'] = vtranslate($this->get('label'), $this->getModuleName());
		if($context)
			$this->fieldInfo['context'] = $context;
			
		switch($fieldDataType){
		case 'picklist':
		case 'multipicklist':
		case 'buttonSet'://ED140000
			if($context === 'CustomView')
				$pickListValues = $this->getPicklistValuesForCustomView();
			else
				$pickListValues = $this->getPicklistValues();
				
		    if(!empty($pickListValues)) {
				$this->fieldInfo['picklistvalues'] = $pickListValues;
		    }
		    break;
		case 'date':
		case 'datetime':
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['date-format'] = $currentUser->get('date_format');
			break;

		case 'time':
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['time-format'] = $currentUser->get('hour_format');
			break;

		case 'currency':
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$this->fieldInfo['currency_symbol'] = $currentUser->get('currency_symbol');
			break;

		case 'owner':
			$userList = $currentUser->getAccessibleUsers();
			$groupList = $currentUser->getAccessibleGroups();
			$pickListValues = array();
			$pickListValues[vtranslate('LBL_USERS', $this->getModuleName())] = $userList;
			$pickListValues[vtranslate('LBL_GROUPS', $this->getModuleName())] = $groupList;
			$this->fieldInfo['picklistvalues'] = $pickListValues;
			break;
		
		//ED150625
		case 'reference':
			$searchInfos = $this->getPopupSearchInfo();
			if(is_array($searchInfos))
				$this->fieldInfo = array_merge($this->fieldInfo, $searchInfos);
			break;
		
		default:
			break;
		}
		
		//ED151107
		Vtiger_Cache::set('FieldInfoForContext::'.$context, $this->id, $this->fieldInfo);
		
		return $this->fieldInfo;
	}

	function setFieldInfo($fieldInfo) {
		$this->fieldInfo = $fieldInfo;
	}
	/**
	 * Function to get the date values for the given type of Standard filter
	 * @param <String> $type
	 * @return <Array> - 2 date values representing the range for the given type of Standard filter
	 */
	protected static function getDateForStdFilterBytype($type) {
		$today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		switch ($type){
		case "yesterday":
			$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			break;
		case "tomorrow":
			$tomorrow = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
			break;
		case "thisweek":
			$thisweek0 = date("Y-m-d", strtotime("-1 week Sunday"));
			$thisweek1 = date("Y-m-d", strtotime("this Saturday"));
			break;
		case "thismonth":
			$currentmonth0 = date("Y-m-d", mktime(0, 0, 0, date("m"), "01", date("Y")));
			$currentmonth1 = date("Y-m-t");
			break;
		case "nextweek":
			$nextweek0 = date("Y-m-d", strtotime("this Sunday"));
			$nextweek1 = date("Y-m-d", strtotime("+1 week Saturday"));
			break;
		case "nextmonth":
			$nextmonth0 = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, "01", date("Y")));
			$nextmonth1 = date("Y-m-t", strtotime("+1 Month"));
			break;
		case "next7days":
			$next7days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 6, date("Y")));
			break;
		case "next30days":
			$next30days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 29, date("Y")));
			break;
		case "next60days":
			$next60days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 59, date("Y")));
			break;
		case "next90days":
			$next90days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 89, date("Y")));
			break;
		case "next120days":
			$next120days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 119, date("Y")));
			break;
		case "lastweek":
			$lastweek0 = date("Y-m-d", strtotime("-2 week Sunday"));
			$lastweek1 = date("Y-m-d", strtotime("-1 week Saturday"));
			break;
		case "lastmonth":
			$lastmonth0 = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, "01", date("Y")));
			$lastmonth1 = date("Y-m-t", strtotime("-1 Month"));
			break;
		case "last7days":
			$last7days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
			break;
		case "last30days":
			$last30days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 29, date("Y")));
			break;
		case "last60days":
			$last60days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 59, date("Y")));
			break;
		case "last90days":
			$last90days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 89, date("Y")));
			break;
		case "last120days":
			$last120days = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 119, date("Y")));
			break;
		case "thisfy":
			$currentFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
			$currentFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")));
			break;
		case "prevfy":
			$lastFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") - 1));
			$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y") - 1));
			break;
		case "nextfy";
			$nextFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") + 1));
			$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y") + 1));
			break;
		case "nextfq";
			if (date("m") <= 4) {
				$nFq = date("Y-m-d", mktime(0, 0, 0, "05", "01", date("Y")));
				$nFq1 = date("Y-m-d", mktime(0, 0, 0, "08", "31", date("Y")));
			} else if (date("m") > 4 and date("m") <= 8) {
				$nFq = date("Y-m-d", mktime(0, 0, 0, "09", "01", date("Y")));
				$nFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
			} else {
				$nFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y") + 1));
				$nFq1 = date("Y-m-d", mktime(0, 0, 0, "04", "30", date("Y") + 1));
			}
			break;
		case "prevfq":
			if (date("m") <= 4) {
				$pFq = date("Y-m-d", mktime(0, 0, 0, "09", "01", date("Y") - 1));
				$pFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y") - 1));
			} else if (date("m") > 4 and date("m") <= 8) {
				$pFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
				$pFq1 = date("Y-m-d", mktime(0, 0, 0, "04", "30", date("Y")));
			} else {
				$pFq = date("Y-m-d", mktime(0, 0, 0, "05", "01", date("Y")));
				$pFq1 = date("Y-m-d", mktime(0, 0, 0, "08", "31", date("Y")));
			}
			break;
		case "thisfq":
			if (date("m") <= 4) {
				$cFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", date("Y")));
				$cFq1 = date("Y-m-d", mktime(0, 0, 0, "04", "30", date("Y")));
			} else if (date("m") > 4 and date("m") <= 8) {
				$cFq = date("Y-m-d", mktime(0, 0, 0, "05", "01", date("Y")));
				$cFq1 = date("Y-m-d", mktime(0, 0, 0, "08", "31", date("Y")));
			} else {
				$cFq = date("Y-m-d", mktime(0, 0, 0, "09", "01", date("Y")));
				$cFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
			}
			break;
		}
		
		switch ($type){
		case "today":
			$dateValues[0] = $today;
			$dateValues[1] = $today;
			break;
		case "yesterday":
			$dateValues[0] = $yesterday;
			$dateValues[1] = $yesterday;
			break;
		case "tomorrow":
			$dateValues[0] = $tomorrow;
			$dateValues[1] = $tomorrow;
			break;
		case "thisweek":
			$dateValues[0] = $thisweek0;
			$dateValues[1] = $thisweek1;
			break;
		case "lastweek":
			$dateValues[0] = $lastweek0;
			$dateValues[1] = $lastweek1;
			break;
		case "nextweek":
			$dateValues[0] = $nextweek0;
			$dateValues[1] = $nextweek1;
			break;
		case "thismonth":
			$dateValues[0] = $currentmonth0;
			$dateValues[1] = $currentmonth1;
			break;
		case "lastmonth":
			$dateValues[0] = $lastmonth0;
			$dateValues[1] = $lastmonth1;
			break;
		case "nextmonth":
			$dateValues[0] = $nextmonth0;
			$dateValues[1] = $nextmonth1;
			break;
		case "next7days":
			$dateValues[0] = $today;
			$dateValues[1] = $next7days;
			break;
		case "next30days":
			$dateValues[0] = $today;
			$dateValues[1] = $next30days;
			break;
		case "next60days":
			$dateValues[0] = $today;
			$dateValues[1] = $next60days;
			break;
		case "next90days":
			$dateValues[0] = $today;
			$dateValues[1] = $next90days;
			break;
		case "next120days":
			$dateValues[0] = $today;
			$dateValues[1] = $next120days;
			break;
		case "last7days":
			$dateValues[0] = $last7days;
			$dateValues[1] = $today;
			break;
		case "last30days":
			$dateValues[0] = $last30days;
			$dateValues[1] = $today;
			break;
		case "last60days":
			$dateValues[0] = $last60days;
			$dateValues[1] = $today;
			break;
		case "last90days":
			$dateValues[0] = $last90days;
			$dateValues[1] = $today;
			break;
		case "last120days":
			$dateValues[0] = $last120days;
			$dateValues[1] = $today;
			break;
		case "thisfy":
			$dateValues[0] = $currentFY0;
			$dateValues[1] = $currentFY1;
			break;
		case "prevfy":
			$dateValues[0] = $lastFY0;
			$dateValues[1] = $lastFY1;
			break;
		case "nextfy";
			$dateValues[0] = $nextFY0;
			$dateValues[1] = $nextFY1;
			break;
		case "nextfq";
			$dateValues[0] = $nFq;
			$dateValues[1] = $nFq1;
			break;
		case "prevfq":
			$dateValues[0] = $pFq;
			$dateValues[1] = $pFq1;
			break;
		case "thisfq":
			$dateValues[0] = $cFq;
			$dateValues[1] = $cFq1;
			break;
		default:
			$dateValues[0] = "";
			$dateValues[1] = "";
			break;
		}

		return $dateValues;
	}

	/**
	 * Function to get all the date filter type informations
	 * @return <Array>
	 */
	public static function getDateFilterTypes() {
		$dateFilters = Array('custom' => array('label' => 'LBL_CUSTOM'),
								'prevfy' => array('label' => 'LBL_PREVIOUS_FY'),
								'thisfy' => array('label' => 'LBL_CURRENT_FY'),
								'nextfy' => array('label' => 'LBL_NEXT_FY'),
								'prevfq' => array('label' => 'LBL_PREVIOUS_FQ'),
								'thisfq' => array('label' => 'LBL_CURRENT_FQ'),
								'nextfq' => array('label' => 'LBL_NEXT_FQ'),
								'yesterday' => array('label' => 'LBL_YESTERDAY'),
								'today' => array('label' => 'LBL_TODAY'),
								'tomorrow' => array('label' => 'LBL_TOMORROW'),
								'lastweek' => array('label' => 'LBL_LAST_WEEK'),
								'thisweek' => array('label' => 'LBL_CURRENT_WEEK'),
								'nextweek' => array('label' => 'LBL_NEXT_WEEK'),
								'lastmonth' => array('label' => 'LBL_LAST_MONTH'),
								'thismonth' => array('label' => 'LBL_CURRENT_MONTH'),
								'nextmonth' => array('label' => 'LBL_NEXT_MONTH'),
								'last7days' => array('label' => 'LBL_LAST_7_DAYS'),
								'last30days' => array('label' => 'LBL_LAST_30_DAYS'),
								'last60days' => array('label' => 'LBL_LAST_60_DAYS'),
								'last90days' => array('label' => 'LBL_LAST_90_DAYS'),
								'last120days' => array('label' => 'LBL_LAST_120_DAYS'),
								'next30days' => array('label' => 'LBL_NEXT_30_DAYS'),
								'next60days' => array('label' => 'LBL_NEXT_60_DAYS'),
								'next90days' => array('label' => 'LBL_NEXT_90_DAYS'),
								'next120days' => array('label' => 'LBL_NEXT_120_DAYS')
							);

		foreach($dateFilters as $filterType => $filterDetails) {
			$dateValues = self::getDateForStdFilterBytype($filterType);
			$dateFilters[$filterType]['startdate'] = $dateValues[0];
			$dateFilters[$filterType]['enddate'] = $dateValues[1];
		}
		return $dateFilters;
	}

	/**
	 * Function to get all the supported advanced filter operations
	 * @return <Array>
	 */
	public static function getAdvancedFilterOptions() {
		return array(
			'e' => 'LBL_EQUALS',
			'n' => 'LBL_NOT_EQUAL_TO',
			's' => 'LBL_STARTS_WITH',
			'ew' => 'LBL_ENDS_WITH',
			'c' => 'LBL_CONTAINS',
			'k' => 'LBL_DOES_NOT_CONTAIN',
			'l' => 'LBL_LESS_THAN',
			'g' => 'LBL_GREATER_THAN',
			'm' => 'LBL_LESS_THAN_OR_EQUAL',
			'h' => 'LBL_GREATER_OR_EQUAL',
			'b' => 'LBL_BEFORE',
			'a' => 'LBL_AFTER',
			'bw' => 'LBL_BETWEEN',
			'y' => 'LBL_IS_EMPTY',
			'ny' => 'LBL_IS_NOT_EMPTY',
			/* ED150225 */
			'vwi' => 'LBL_EXISTS',
			'vwx' => 'LBL_EXCLUDED',
			/* ED150619 */
			'ct' => 'LBL_CONTAINS_TEXT',
			'kt' => 'LBL_DOES_NOT_CONTAIN_TEXT',
			'ca' => 'LBL_CONTAINS_ALL',
			'ka' => 'LBL_DOES_NOT_CONTAIN_ALL',
		);
	}

	/**
	 * Function to get the advanced filter option names by Field type
	 * @return <Array>
	 */
	public static function getAdvancedFilterOpsByFieldType() {
		return array(
			'V' => array('e','n','s','ew','c','k','y','ny'),//ED150619 : 'ct','kt' added in js for picklist only
			'N' => array('e','n','l','g','m','h', 'y','ny'),
			'T' => array('e','n','l','g','m','h','bw','b','a','y','ny'),
			'I' => array('e','n','l','g','m','h','y','ny'),
			'C' => array('e','n','y','ny'),
			'D' => array('e','n','bw','b','a','y','ny'),
			'DT' => array('e','n','bw','b','a','y','ny'),
			'NN' => array('e','n','l','g','m','h','y','ny'),
			'E' => array('e','n','s','ew','c','k','y','ny'),
			/* ED150225 */
			'VW' => array('vwi','vwx'),
			/* ED150507 */
			'PANEL' => array('vwi','vwx'),
			/* ED150619 Picklist, MultiPicklist, ButtonSet */
			'PL' => array('e','n','s','ew','c','k','ct','kt','ca','ka','y','ny'),
		);
	}


     /**
     * Function to retrieve field model for specific block and module
     * @param <Vtiger_Module_Model> $blockModel - block instance
     * @return <array> List of field model
     */
	public static function getAllForModule($moduleModel){
        $fieldModelList = Vtiger_Cache::get('ModuleFields',$moduleModel->id);
        if(!$fieldModelList){
            $fieldObjects = parent::getAllForModule($moduleModel);

            $fieldModelList = array();
            //if module dont have any fields
            if(!is_array($fieldObjects)){
                $fieldObjects = array();
            }

            foreach($fieldObjects as $fieldObject){
                $fieldModelObject= self::getInstanceFromFieldObject($fieldObject);
                $fieldModelList[$fieldModelObject->get('block')->id][] = $fieldModelObject;
                Vtiger_Cache::set('field-'.$moduleModel->getId(),$fieldModelObject->getId(),$fieldModelObject);
                Vtiger_Cache::set('field-'.$moduleModel->getId(),$fieldModelObject->getName(),$fieldModelObject);
            }

            Vtiger_Cache::set('ModuleFields',$moduleModel->id,$fieldModelList);
        }
        return $fieldModelList;
	}

	/**
	 * Function to get instance
	 * @param <String> $value - fieldname or fieldid
	 * @param <type> $module - optional - module instance
	 * @return <Vtiger_Field_Model>
	 */
	public static function getInstance($value, $module = false) {
        $fieldObject = null;
        if($module){
            $fieldObject = Vtiger_Cache::get('field-'.$module->getId(), $value);
        }
        if(!$fieldObject){
            $fieldObject = parent::getInstance($value, $module);
            if($module){
                Vtiger_Cache::set('field-'.$module->getId(),$value,$fieldObject);
            }
        }

		if($fieldObject) {
			return self::getInstanceFromFieldObject($fieldObject);
		}
		return false;
	}

    /**
	 * Added function that returns the folders in a Document
	 * @return <Array>
	 */
	function getDocumentFolders() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_attachmentsfolder', array());
		$rows = $db->num_rows($result);
		$folders = array();
		for($i=0; $i<$rows; $i++){
			$folderId = $db->query_result($result, $i, 'folderid');
			$folderName = $db->query_result($result, $i, 'foldername');
			$uicolor = $db->query_result($result, $i, 'uicolor');/* ED141010 TODO */
			$folders[$folderId] = array(
				'name' => $folderName,
				'uicolor' => $uicolor,
			);
		}
		return $folders;
	}

	/**
	 * Function checks if the current Field is Read/Write
	 * @return <Boolean>
	 */
	function getProfileReadWritePermission() {
		return $this->getPermissions('readwrite');
	}

	/**
	 * Function returns Client Side Validators name
	 * @return <Array> [name=>Name of the Validator, params=>Extra Parameters]
	*/
	/**TODO: field validator need to be handled in specific module getValidator api  **/
	function getValidator() {
		$validator = array();
		$fieldName = $this->getName();
		switch($fieldName) {
			case 'birthday' : $funcName = array('name'=>'lessThanToday');
							  array_push($validator, $funcName);
								break;
			case 'support_end_date' : $funcName = array('name' => 'greaterThanDependentField',
														'params' => array('support_start_date'));
									array_push($validator, $funcName);
									break;
            case 'support_start_date' : $funcName = array('name' => 'lessThanDependentField',
														'params' => array('support_end_date'));
									array_push($validator, $funcName);
									break;
			case 'targetenddate' :
			case 'actualenddate':
			case 'enddate':
							$funcName = array('name' => 'greaterThanDependentField',
								'params' => array('startdate'));
							array_push($validator, $funcName);
							break;
            case 'startdate':
                            if($this->getModule()->get('name') == 'Project') {
                                $params = array('targetenddate');
                            }else{
                                //for project task
                                $params = array('enddate');
                            }
                            $funcName = array('name' => 'lessThanDependentField',
								'params' => $params);
							array_push($validator, $funcName);
							break;
			case 'expiry_date':
			case 'due_date':
								$funcName = array('name' => 'greaterThanDependentField',
									'params' => array('start_date'));
								array_push($validator, $funcName);
								break;
			case 'sales_end_date':
								$funcName = array('name' => 'greaterThanDependentField',
									'params' => array('sales_start_date'));
								array_push($validator, $funcName);
								break;
            case 'sales_start_date':
								$funcName = array('name' => 'lessThanDependentField',
									'params' => array('sales_end_date'));
								array_push($validator, $funcName);
								break;
			case 'qty_per_unit' :
			case 'qtyindemand' :
			case 'hours':
			case 'days':
								$funcName = array('name'=>'PositiveNumber');
							  array_push($validator, $funcName);
								break;
			case 'employees':
								$funcName = array('name'=>'WholeNumber');
							  array_push($validator, $funcName);
								break;
			case 'related_to':
								$funcName = array('name'=>'ReferenceField');
							  array_push($validator, $funcName);
								break;
            //SalesOrder field sepecial validators
            case 'end_period' : $funcName = array('name' => 'greaterThanDependentField',
									'params' => array('start_period'));
								array_push($validator, $funcName);
								break;
             case 'start_period' : $funcName = array('name' => 'lessThanDependentField',
									'params' => array('end_period'));
								array_push($validator, $funcName);
								break;
		}
		return $validator;
	}

	/**
	 * Function to retrieve display value in edit view
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getEditViewDisplayValue($value) {
		if(!$this->uitype_instance) {
			$this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
		}
		$uiTypeInstance = $this->uitype_instance;
		return $uiTypeInstance->getEditViewDisplayValue($value);
	}

	/**
	 * Function to retieve types of file locations in Documents Edit
	 * @return <array> - List of file location types
	 */
	public function getFileLocationType() {
		return array('I'=>'LBL_INTERNAL', 'E'=>'LBL_EXTERNAL');
	}

	/**
	 * Function returns list of Currencies available in the system
	 * @return <Array>
	 */
	public function getCurrencyList() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_currency_info WHERE currency_status = ? AND deleted=0', array('Active'));
		for($i=0; $i<$db->num_rows($result); $i++) {
			$currencyId = $db->query_result($result, $i, 'id');
			$currencyName = $db->query_result($result, $i, 'currency_name');
			$currencies[$currencyId] = $currencyName;
		}
		return $currencies;
	}

	/**
	 * Function to get Display value for RelatedList
	 * @param <String> $value
	 * @return <String>
	 */
	public function getRelatedListDisplayValue($value) {
		if(!$this->uitype_instance) {
			$this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
		}
		$uiTypeInstance = $this->uitype_instance;
		return $uiTypeInstance->getRelatedListDisplayValue($value);
	}

	/**
	 * Function to get Default Field Value
	 * @return <String> defaultvalue
	 */
	public function getDefaultFieldValue(){
		return $this->defaultvalue;
	}


    /**
     * Function whcih will get the databse insert value format from user format
     * @param type $value in user format
     * @return type
     */
    public function getDBInsertValue($value) {
        if(!$this->uitype_instance) {
			$this->uitype_instance = Vtiger_Base_UIType::getInstanceFromField($this);
		}
		$uiTypeInstance = $this->uitype_instance;
        return $uiTypeInstance->getDBInsertValue($value);
    }

    /**
     * Function to get visibilty permissions of a Field
     * @param <String> $accessmode
     * @return <Boolean>
     */
    public function getPermissions($accessmode = 'readonly') {
        $user = Users_Record_Model::getCurrentUserModel();
        $privileges = $user->getPrivileges();
        if ($privileges->hasGlobalReadPermission()) {
            return true;
        } else {
            $modulePermission = Vtiger_Cache::get('modulePermission-'.$accessmode, $this->getModuleId());
            if (!$modulePermission) {
                $modulePermission = self::preFetchModuleFieldPermission($this->getModuleId(), $accessmode);
            }
            if (array_key_exists($this->getId(), $modulePermission)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Function to Preinitialize the module Field Permissions
     * @param <Integer> $tabid
     * @param <String> $accessmode
     * @return <Array>
     */
    public static function preFetchModuleFieldPermission($tabid,$accessmode = 'readonly'){
        $adb = PearDatabase::getInstance();
        $user = Users_Record_Model::getCurrentUserModel();
        $privileges = $user->getPrivileges();
        $profilelist = $privileges->get('profiles');

                if (count($profilelist) > 0) {
                    if ($accessmode == 'readonly') {
                        $query = "SELECT vtiger_profile2field.visible,vtiger_field.fieldid
			FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
			WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
			AND vtiger_profile2field.profileid in (" . generateQuestionMarks($profilelist) . ") AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                    } else {
                        $query = "SELECT vtiger_profile2field.visible,vtiger_field.fieldid
			FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
			WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0
			AND vtiger_def_org_field.visible=0  AND vtiger_profile2field.profileid in (" . generateQuestionMarks($profilelist) . ")
			AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                    }
                    $params = array($tabid, $profilelist);
                } else {
                    if ($accessmode == 'readonly') {
                        $query = "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                    } else {
                        $query = "SELECT vtiger_profile2field.visible,vtiger_field.fieldid FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0  AND vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid";
                    }
                    $params = array($tabid);
                }
                $result = $adb->pquery($query, $params);
                $modulePermission = array();
                $noOfFields = $adb->num_rows($result);
                for ($i = 0; $i < $noOfFields; ++$i) {
                    $row = $adb->query_result_rowdata($result, $i);
                    $modulePermission[$row['fieldid']] = $row['visible'];
                }
                Vtiger_Cache::set('modulePermission-'.$accessmode,$tabid,$modulePermission);

                return $modulePermission;
    }

    public function __update() {
        $db = PearDatabase::getInstance();
        $query = 'UPDATE vtiger_field SET typeofdata=?,presence=?,quickcreate=?,masseditable=?,defaultvalue=?,summaryfield=? WHERE fieldid=?';
        $params = array($this->get('typeofdata'), $this->get('presence'), $this->get('quickcreate'), $this->get('masseditable'),
                        $this->get('defaultvalue'), $this->get('summaryfield'), $this->get('id'));
		$db->pquery($query,$params);
    }

    public function updateTypeofDataFromMandatory($mandatoryValue='O') {
        $mandatoryValue = strtoupper($mandatoryValue);
        $supportedMandatoryLiterals = array('O','M');
        if(!in_array($mandatoryValue, $supportedMandatoryLiterals)) {
            return;
        }
        $typeOfData = $this->get('typeofdata');
        $components = explode('~', $typeOfData);
        $components[1] = $mandatoryValue;
        $this->set('typeofdata',  implode('~', $components));
        return $this;
    }

    /* ED141219 : ça ne veut plus dire grand chose puisque je renomme les champs */
    public function isCustomField() {
        return (substr($this->getName(),0,3) == 'cf_') ? true : false;
    }

	public function hasDefaultValue() {
		return $this->defaultvalue == '' ? false : true;
	}

    public function isActiveField() {
        $presence = $this->get('presence');
        return in_array($presence, array(0,2));
    }

	public function isMassEditable() {
		return $this->masseditable == 1 ? true : false;
	}

    /**
     * Function which will check if empty piclist option should be given
     */
    public function isEmptyPicklistOptionAllowed() {
        return true;
    }

    public function isReferenceField() {
        return ($this->getFieldDataType() == self::REFERENCE_TYPE) ? true : false;
    }

	public function isOwnerField() {
        return ($this->getFieldDataType() == self::OWNER_TYPE) ? true : false;
    }

    public static function getInstanceFromFieldId($fieldId, $moduleTabId) {
        $db = PearDatabase::getInstance();

        if(is_string($fieldId)) {
            $fieldId = array($fieldId);
        }

        $query = 'SELECT * FROM vtiger_field WHERE fieldid IN ('.generateQuestionMarks($fieldId).') AND tabid=?';
        $result = $db->pquery($query, array($fieldId,$moduleTabId));
        $fieldModelList = array();
        $num_rows = $db->num_rows($result);
        for($i=0; $i<$num_rows; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $fieldModel = new self();
            $fieldModel->initialize($row);
            $fieldModelList[] = $fieldModel;
        }
        return $fieldModelList;
    }

	/* ED150414
	 * for Header Filter
	*/
	var $filterOperator = null;
	/* ED150414
	 * Returns UI operator
	 */
	public function getFilterOperatorDisplayValue(){
		//see include\QueryGenerator\QueryGenerator.php, line 1054
		if(!$this->filterOperator)
			return '';
		$filterOperator = self::getOperatorFromOperatorCode($this->filterOperator);
		$filterOperatorHtml = to_html($filterOperator);
		//var_dump($this->get('fieldvalue'), $this->filterOperator, $filterOperator, strlen($filterOperator));
		//si fieldvalue commence déjà par l'opérateur, on annule 
		if($this->get('fieldvalue')
		&& (strcasecmp(substr($this->get('fieldvalue'), 0, strlen($filterOperator)), $filterOperator) === 0
		 || ($filterOperator != $filterOperatorHtml
			&& strcasecmp(substr($this->get('fieldvalue'), 0, strlen($filterOperatorHtml)), $filterOperatorHtml) === 0))){
			return '';
		}
		return $filterOperator;
	}
	
	public static function getOperatorFromOperatorCode($filterOperator){
		switch($filterOperator){
			case 'e': return '=';
			case 'n': return '<>';
			case 's': return '^';
			case 'ew': return '%-';
			case 'c': return '%';
			case 'k': return '<>%'; //"NOT LIKE %$value%"
			case 'l': return '<';
			case 'g': return '>';
			case 'm': return '<=';
			case 'h': return '>=';
			case 'a': return '>';
			case 'b': return '<';
			/*ED150307*/
			case 'vwi': return "IN ";
			case 'vwx': return " NOT IN ";
		}
	}
	
	/** ED150625
	 * Returns parameters to filter popup selection list of a reference field.
	 * Data added to FieldInfo
	 */
	public function getPopupSearchInfo(){
		return '';
	}
	
	/** ED151215
	 * Returns true if ui reference field in Edit view enables "create related record".
	 * see invoice Field_Model
	 */
	public function canCreateReferenceRecord(){
		return true;
	}
	
	
	

	/** ED160110
	 * Function to get the entity name field models
	 * @return <Vtiger_Field_Model[]> 
	 */
	public static function getEntityNameFieldModels($moduleName) {
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$nameFieldObject = Vtiger_Cache::get('EntityField',$moduleName);
		if(!$nameFieldObject) {
			$nameFields = $moduleModel->getNameFields();
			$nameFieldObject = Vtiger_Cache::get('EntityField',$moduleName);
			if(!$nameFieldObject) {
				$field = self::getModuleEntityLabelField($moduleModel);
				return array($field->getName() => self::getModuleEntityLabelField($moduleModel));
			}
		}
		$fieldModels = array();
		foreach(explode(',', $nameFieldObject->fieldname) as $fieldname){
			$field = $moduleModel->getField($fieldname);
			if(!$field)
				continue;
			$field->set('label', vtranslate($field->get('label'), $moduleName) . ' ' . vtranslate('SINGLE_'.$moduleName, $moduleName));
			$field->set('isrelatedfield', true); //différencie les champs d'une table du module lié des champs de la table de relation
			$fieldModels[$field->getName()] = $field;
		}
		return $fieldModels;
	}
	/** ED160110
	 * Function to get the entity label field
	 * @return <Vtiger_Field_Model> 
	 */
	public static function getModuleEntityLabelField($moduleName) {
		if(is_object($moduleName)){
			$moduleModel = $moduleName;
			$moduleName = $moduleModel->getName();
		}
		else
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	    $fieldName = 'crmentitylabel';
	    $field = new Vtiger_Field_Model();
	    $field->setModule($moduledModel);
	    $field->set('name', $fieldName);
	    $field->set('column', 'label');
	    $field->set('table', 'vtiger_crmentity');
	    $field->set('label', vtranslate('LBL_ITEM_NAME') . ' ' . vtranslate('SINGLE_'.$moduleName, $moduleName));
	    $field->set('typeofdata', 'V~0');
	    $field->set('uitype', 1);
		$field->set('isrelatedfield', true); //différencie les champs d'une table du module lié des champs de la table de relation
	    
		return $field;
	}
}