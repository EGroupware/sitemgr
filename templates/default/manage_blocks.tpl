<form method="POST">
<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{block_manager}</div>
<table border='0' align='center' cellpadding='5' cellspacing='1'>
	<tr style='font-weight:bold' bgcolor='dddddd'>
		<td align='left'>{lang_name}<br>{lang_description}</td>
		<td align='left'>{lang_actif}</td>
		<td align='left'>{lang_title}</td>
		<td align='left'>{lang_side}</td>
		<td align='left'>{lang_view}</td>
		<td align='left'>{lang_position}</td>
	</tr>
	<tr>
		<td colspan="6"></td>
	</tr>
	<!-- BEGIN BlockBlock -->
	<tr bgcolor='dddddd'>
		<td align='left'><b>{blockname}</b><br>{blockdescription}</td>
		<td align='center'><input type="checkbox" name="blockactif[{blockid}] {blockactif}"></td>
		<td><input type="text" name="blocktitle[{blockid}]" size="30" value="{blocktitle}"></td>
		<td>{sideselect}</td>
		<td>{viewselect}</td>
		<td><input type="text" name="blockpos[{blockid}]" size="2" value="{blockpos}"></td>
	</tr>
	<!-- END BlockBlock -->
	<tr><td colspan="6"></td></tr>
	<tr>
		<td colspan="2" align="right"><input type="reset" name="reset" value="{lang_reset}"></td>
 		<td colspan="3 "align="left"><input type="submit" name="btnSaveBlock" value="{lang_save}"></td>
	</tr>
</table>
</form>