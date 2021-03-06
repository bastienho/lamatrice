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
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="alphabetSearchKey" value= "{$MODULE_MODEL->getAlphabetSearchField()}" />
<input type="hidden" id="requestSearchKey" value= "{$PAGING_MODEL->getRequestSearchField()}" />{* ED150412 *}
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTIRES_COUNT}" id="noOfEntries">
{if $MODULE neq 'RsnDons'}
{*assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL*}
{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}

{foreach item=ALPHABET_FIELD from=$ALPHABET_FIELDS}
	{include file=vtemplate_path($ALPHABET_FIELD->getUITypeModel()->getAlphabetTemplateName(),$MODULE)}
{/foreach}

<div id="selectAllMsgDiv" class="alert-block msgDiv noprint">
	<strong><a id="selectAllMsg">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount"></span>)</a></strong>
</div>
<div id="deSelectAllMsgDiv" class="alert-block msgDiv noprint">
	<strong><a id="deSelectAllMsg">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></strong>
</div>
<div class="contents-topscroll noprint">
	<div class="topscroll-div">
		&nbsp;
	 </div>
</div>
{/if}
<div class="listViewEntriesDiv contents-bottomscroll">
	<div class="bottomscroll-div">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
		<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
		<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<th width="5%" class="{$WIDTHTYPE}">
					<input type="checkbox" id="listViewEntriesMainCheckBox" />
				</th>
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{* ED150412 *}
					{if $LISTVIEW_HEADER->getName() == 'createdtime'}
						{assign var=SKIP_MODIFIEDTIME value=true}
					{else if $LISTVIEW_HEADER->getName() == 'modifiedtime' && $SKIP_MODIFIEDTIME && !$LISTVIEW_HEADER@last}
						{continue}
					{/if}
					<th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if} class="{$WIDTHTYPE} {if $LISTVIEW_HEADER->get('hide')}hide{/if}">
						<a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('column')}">{vtranslate($LISTVIEW_HEADER->get('label'), $MODULE)}
							&nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
					</th>
					{assign var=IS_BUTTONSET value=$LISTVIEW_HEADER->get('uitype') eq '402'}
					{if $IS_BUTTONSET}
					    {assign var=tmp value=$LISTVIEW_HEADER->set('picklist_values',$MODULE_MODEL->getListViewPicklistValues($LISTVIEW_HEADER->getName()))}
					{/if}
				{/foreach}
			</tr>
			<tr class="listViewHeaders filters">
				<th width="5%"></th>
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{* ED150412 *}
					{if $LISTVIEW_HEADER->getName() == 'modifiedtime' && $SKIP_MODIFIEDTIME && !$LISTVIEW_HEADER@last}
						{continue}
					{/if}
					<th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if} {if $LISTVIEW_HEADER->get('hide')}class="hide"{/if}>
						{include file=vtemplate_path($LISTVIEW_HEADER->getUITypeModel()->getHeaderFilterTemplateName(),$MODULE)}
					</th>
				{/foreach}
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
			{assign var=UICOLOR value=$LISTVIEW_ENTRY->get('uicolor')}
			<tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}'
				data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}'
				id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}"
			>
				<td  width="5%" class="{$WIDTHTYPE}"
					{*if $UICOLOR neq null} style="background-color: {$UICOLOR} !important;"{/if*}>
					<input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"/>
				</td>
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
					{if $LISTVIEW_HEADERNAME == 'modifiedtime' && $SKIP_MODIFIEDTIME && !$LISTVIEW_HEADER@last}
						{continue}
					{/if}
					{assign var=UITYPE value=$LISTVIEW_HEADER->get('uitype')}
					{assign var=IS_BUTTONSET value=$UITYPE eq '402'}
					<td class="listViewEntryValue {$WIDTHTYPE} {if $LISTVIEW_HEADER->get('hide')}hide{/if}"
					data-field-type="{$LISTVIEW_HEADER->getFieldDataType()}" data-field-name="{$LISTVIEW_HEADER->getFieldName()}"
					{if $UICOLOR neq null && $UITYPE eq '401'} style="background-color: {$UICOLOR} !important; min-width:3em;"{/if} nowrap>
						{if $LISTVIEW_HEADER->isNameField() eq true or $UITYPE eq '4'}
							<a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
						{else if $UITYPE eq '72'}
							{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
								{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
							{else}
								{$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
							{/if}
						{elseif	$UITYPE eq '401'}
						
						{* ED150000 *}
						{elseif $IS_BUTTONSET}
						    {assign var=PICKLIST_VALUES value=$LISTVIEW_HEADER->get('picklist_values')}
						    {assign var=FIELD_VALUE value=$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
						    {if is_array($FIELD_VALUE)}{assign var=FIELD_VALUE value=$FIELD_VALUE[0]}{/if}
						    {if $FIELD_VALUE eq null}{assign var=FIELD_VALUE value=$LISTVIEW_HEADER->getDefaultFieldValue()}{/if}
						    {if $PICKLIST_VALUES && array_key_exists($FIELD_VALUE, $PICKLIST_VALUES)}
							    {assign var=PICKLIST_ITEM value=$PICKLIST_VALUES[$FIELD_VALUE]}
						    {else}
							    {assign var=PICKLIST_ITEM value=$FIELD_VALUE}
						    {/if}
						    {if is_array($PICKLIST_ITEM)}
							    {assign var=PICKLIST_LABEL value=$PICKLIST_ITEM['label']}
							    {if isset($PICKLIST_ITEM['class'])}
								{assign var=PICKLIST_CLASS value=$PICKLIST_ITEM['class']}
							    {else}
								{assign var=PICKLIST_CLASS value=''}
							    {/if}
							    {assign var=PICKLIST_ICON value=$PICKLIST_ITEM['icon']}
						    {else}
							    {assign var=PICKLIST_LABEL value=$PICKLIST_ITEM}
							    {assign var=PICKLIST_ICON value=false}
							    {assign var=PICKLIST_CLASS value=false}
						    {/if}
						    <label for="{$UID}{$PICKLIST_KEY}" class="{$PICKLIST_CLASS}">
						    {if $PICKLIST_ICON}<span class="{$PICKLIST_ICON}"></span>
						    {else}
							&nbsp;{$PICKLIST_LABEL}
						    {/if}</label>
						    
						{* ED150412 *}
						{else if $LISTVIEW_HEADERNAME == 'createdtime'}
							{substr($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME),0,10)}
							{if $LISTVIEW_ENTRY->get('modifiedtime')
							&& substr($LISTVIEW_ENTRY->get('createdtime'),0,10) neq substr($LISTVIEW_ENTRY->get('modifiedtime'),0,10)}
								<br>{substr($LISTVIEW_ENTRY->get('modifiedtime'),0,10)}
							{/if}
						{else if $LISTVIEW_HEADERNAME == 'modifiedtime' && $SKIP_MODIFIEDTIME}
							
						{else} 
							{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
						{/if}
						{if $LISTVIEW_HEADER@last}
							</td><td nowrap class="{$WIDTHTYPE}">
							<div class="actions pull-right">
								<span class="actionImages">
									{if $IS_MODULE_DUPLICATABLE}{*ED150207*}
									    <a href='{$LISTVIEW_ENTRY->getDuplicateRecordUrl()}'><i title="{vtranslate('LBL_DUPLICATE', $MODULE)}" class="icon-plus alignMiddle"></i></a>
									{/if}
									<a href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
									{if $IS_MODULE_EDITABLE}
										<a href='{$LISTVIEW_ENTRY->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
									{/if}
									{if $IS_MODULE_DELETABLE}
										<a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
									{/if}
								</span>
							</div></td>
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</table>

<!--added this div for Temporarily -->
{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a href="{$MODULE_MODEL->getCreateRecordUrl()}">{vtranslate($SINGLE_MODULE, $MODULE)}</a>{/if}
				</td>
			</tr>
		</tbody>
	</table>
{/if}
</div>
</div>
{/strip}
