{*<!--
/*********************************************************************************
  ** ED151106
  *
 ********************************************************************************/
-->*}
<div style="margin:0 auto;width: 50em;">
	<table border='0' cellpadding='5' cellspacing='0' height='600px' width="700px">
	<tr><td align='center'>
		<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 100000020;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tr>
			<td rowspan='2' width='11%'><img src="{vimage_path('info.gif')}" ></td>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
				<span class='genHeaderSmall'>{vtranslate($MESSAGE)}</span></td>
		</tr>
		<tr>
			<td class='small' align='right' nowrap='nowrap'>
				{if $BUTTON_CLOSE}
					<a onclick="$.unblockUI(); return false;">{vtranslate('LBL_CLOSE')}</button><br>
				{else}
					<a href='javascript:window.history.back();'>{vtranslate('LBL_GO_BACK')}</a>
				{/if}
				<br>
			</td>
		</tr>
		</table>
		</div>
	</td></tr>
	</table>
</div>