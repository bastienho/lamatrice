{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	<span class="span2">
		{foreach key=ITER item=IMAGE_INFO from=$RECORD->getImageDetails()}
			{if !empty($IMAGE_INFO.rsnClass)}{* ED140927 *}
				<span class="summaryImg rsn-summaryImg"><span class="icon-rsn-large-{$IMAGE_INFO.rsnClass}"></span></span>
			{else if !empty($IMAGE_INFO.path)}
				<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="65" height="80" align="left"><br>
			{else}
				<img src="{vimage_path('summary_Contact.png')}" class="summaryImg"/>
			{/if}
		{/foreach}
	</span>
	<span class="span8 margin0px">
		<span class="row-fluid">
			<h4 class="recordLabel pushDown" title="{$RECORD->getDisplayValue('salutationtype')}&nbsp;{$RECORD->getName()}"> &nbsp;
			{if $RECORD->getDisplayValue('salutationtype')}
				<span class="salutation">{vtranslate($RECORD->getDisplayValue('salutationtype'), $MODULE_NAME)}&nbsp;</span> 
			{/if}
			{assign var=COUNTER value=0}
			{foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
				{assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
				{if $FIELD_MODEL->getPermissions()}
					<span class="{$NAME_FIELD}">{$RECORD->get($NAME_FIELD)}</span>
					{if $COUNTER eq 0 && ($RECORD->get($NAME_FIELD))}&nbsp;{assign var=COUNTER value=$COUNTER+1}{/if}
				{/if}
			{/foreach}
			{assign var=NAME_FIELD value='contact_no'}
			<span class="{$NAME_FIELD}"><small>-&nbsp;{$RECORD->get($NAME_FIELD)}</small></span>
			{if $RECORD->get('isgroup') neq '0'}
			       <span class="mailingstreet2-synchronized" style="margin-left: 1em;">{htmlentities($RECORD->get('mailingstreet2'))}</span>
		       {/if}
			</h4>
		</span>
		<span class="row-fluid">
			<span class="title_label">&nbsp;{$RECORD->getDisplayValue('title')}</span>
			{if $RECORD->getDisplayValue('account_id') && $RECORD->getDisplayValue('title') }
				&nbsp;{vtranslate('LBL_AT')}&nbsp;
			{/if}
			{$RECORD->getDisplayValue('account_id')}
		</span>
	</span>
{/strip}