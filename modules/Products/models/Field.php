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
 * Vtiger Field Model Class
 */
class Products_Field_Model extends Vtiger_Field_Model {
	
	
	/**
	 * qtyindemand est toujours interdit de modification, il est calculé dans SalesOrder crmentity
	 * @return <Boolean> - true/false
	 */
	public function isReadOnly() {
		if($this->getName() === 'qtyindemand')
			return true;
		return parent::isReadOnly();
	}
}