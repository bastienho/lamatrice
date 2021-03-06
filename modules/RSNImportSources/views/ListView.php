<?php

class RSNImportSources_ListView_View extends Vtiger_List_View {

	/** ED150904
	 * Du fait de la substitution de List par ListView, on force le chargement du ListView.js, dont la classe hérite de Vtiger_List_Js
	 */
	public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);

		$jsFileNames = array(
			'~layouts/vlayout/modules/RSNImportSources/resources/ListView.js'
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$headerCssInstances = parent::getHeaderCss($request);

		$cssFileNames = array(
			'~/layouts/vlayout/modules/RSNImportSources/resources/css/style.css',
		);
		$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

		return $headerCssInstances;
	}
}