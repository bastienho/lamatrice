{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
  * ED141005
  * picklist affich� en bouton radio
  * passage du param�tre LABELS contenant un tableau [value=name]
 ********************************************************************************/
-->*}
{strip}
{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
{assign var="SOURCE_FIELD_NAME" value=$FIELD_MODEL->getFieldName()}
{*ED150526*}
{if isset($FORCE_FIELD_NAME)}
		{assign var="FIELD_NAME" value=$FORCE_FIELD_NAME}
{else}
		{assign var="FIELD_NAME" value=$FIELD_MODEL->getFieldName()}
{/if}
{assign var=FIELD_LABEL value=Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode(vtranslate($FIELD_MODEL->get('label'), $MODULE_NAME)))}
{if $RECORD}{assign var=RECORD_MODEL value=$RECORD}{/if}
{if !$RECORD_MODEL}RECORD_MODEL manquant {*echo_callstack()*}{/if}
{assign var=PICKLIST_LABELS value=$RECORD_MODEL->getPicklistValuesDetails($SOURCE_FIELD_NAME)}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var=SELECTED_VALUE value=$FIELD_MODEL->get('fieldvalue')}
{assign var=UID value=uniqid('btnset')}
<div id="{$UID}" class="buttonset {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}">
    {*if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if*}
    {foreach item=PICKLIST_ITEM key=PICKLIST_KEY from=$PICKLIST_LABELS}
	{if is_array($PICKLIST_ITEM)}
	    {assign var=PICKLIST_LABEL value=$PICKLIST_ITEM['label']}
	    {if isset($PICKLIST_ITEM['class'])}
		{assign var=PICKLIST_CLASS value=$PICKLIST_ITEM['class']}
	    {else}
		{assign var=PICKLIST_CLASS value=''}
	    {/if}
	    {assign var=PICKLIST_ICON value=$PICKLIST_ITEM['icon']}
	    {assign var=PICKLIST_TITLE value=$FIELD_LABEL|cat:' '|cat:$PICKLIST_ITEM['title']}
	{else}
	    {assign var=PICKLIST_LABEL value=$PICKLIST_ITEM}
	{/if}
        <input type="radio" 
                name="{$FIELD_NAME}"
                id="{$UID}{$PICKLIST_KEY}" 
                data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
                data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
                data-selected-value='{$SELECTED_VALUE}'
                value="{$PICKLIST_KEY}"
        {if trim(decode_html($SELECTED_VALUE)) eq trim($PICKLIST_KEY)}
                checked="checked"
        {/if}
        /><label for="{$UID}{$PICKLIST_KEY}" class="{$PICKLIST_CLASS}" title="{$PICKLIST_TITLE}">
            {if $PICKLIST_ICON}<span class="{$PICKLIST_ICON}"></span>&nbsp;{/if}
            {$PICKLIST_LABEL}</label>
    {/foreach}
    <!--script>$(document.body).ready(function(){ $('#{$UID}').buttonset(); });</script-->
</div>
{/strip}