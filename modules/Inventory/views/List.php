<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Inventory_List_View extends Vtiger_List_View {
	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$moduleName = $request->getModule();
		$modulePopUpFile = 'modules.'.$moduleName.'.resources.Popup';
		$moduleEditFile = 'modules.'.$moduleName.'.resources.Edit';
		$moduleListFile = 'modules.'.$moduleName.'.resources.List';//ED150625
		unset($headerScriptInstances[$modulePopUpFile]);
		unset($headerScriptInstances[$moduleEditFile]);
		unset($headerScriptInstances[$moduleListFile]);//ED150625


		$jsFileNames = array(
			'modules.Inventory.resources.Edit',
			'modules.Inventory.resources.Popup',
			'modules.Inventory.resources.List',//ED150625
		);
		$jsFileNames[] = $modulePopUpFile;
		$jsFileNames[] = $moduleEditFile;
		$jsFileNames[] = $moduleListFile;//ED150625
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}
}
?>