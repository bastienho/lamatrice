<?php
/*+***********************************************************************************
 * ED150817
 *
 * Attention, dans le champ query, l'initialisation remplace < et > par &lt; et &gt;
 *************************************************************************************/

/**
 * Vtiger Entity Record Model Class
 */
class RSNSysControls_Record_Model extends Vtiger_Record_Model {

	/** ED150609
	 * return SELECT COUNT(*) query
	 */
	public function getSysControlCountQuery(){
		return 'SELECT COUNT(*) FROM (' . $this->getQueryFieldValue() . ') _sysctrlqueryforcount_';
	}
	
	//L'initialisation remplace < et > par &lt; et &gt;
	//TODO éviter ça
	public function getQueryFieldValue(){
		$ctrlQuery = $this->get('query');
		if(strpos($ctrlQuery, 't;'))
			$ctrlQuery = str_replace('&gt;', '>', str_replace('&lt;', '<', $ctrlQuery));
		return $ctrlQuery;
		
	}
}