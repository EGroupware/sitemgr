<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{translate}</div>
<div style="text-align:center; color:#FF0000; font-weight:bold;"><b>{error_msg}</b></div>
<form name="translatecategory" method="POST">
<input type="hidden" name="category_id" value="{catid}">

<table style="border-width:2px;border-style:solid;" align="center" border="1" rules="all" width="80%" cellpadding="5">
	<tr>
		<td width="20%">{lang_refresh}</td><td width="40%">{showlang}</td><td width="40%">{savelang}</td>
	</tr>
	<tr>
		<td>{lang_catname}:</td>
		<td>{showcatname}</td>
		<td><input type="text" name="savecatname" value="{savecatname}"></td>
	</tr>
	<tr>
		<td>{lang_catdesc}:</td>
		<td style="vertical-align:top">{showcatdesc}</td>
		<td><textarea rows="3" cols="50" name="savecatdesc">{savecatdesc}</textarea></td>
	</tr>
	<tr>
		<td colspan="3" align="center"><input type="reset" name="reset" value="{lang_reset}"><input type="submit" name="btnSaveCategory" value="{lang_save}"></td>
	</tr>
</table>
</form>
<h4 style="text-align:center">Content blocks for category</h4>
<!-- BEGIN Blocktranslator -->
<h5>{moduleinfo}</h5>
<div align="center" style="color:red">{validationerror}</div>
<form method="POST">
<table style="border-width:2px;border-style:solid;" align="center" border ="1" rules="all" width="80%" cellpadding="5">
	<tr>
		<td width="20%">{lang_refresh}</td><td width="40%">{showlang}</td><td width="40%">{savelang}</td>
	</tr>
<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{value}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->
	<tr>
		<td colspan="3" align="center"><input type="reset" name="reset" value="{lang_reset}">{savebutton}</td>
	</tr>
</table>
<input type="hidden" name="category_id" value="{catid}">
<input type="hidden" value="{blockid}" name="blockid" />
</form>
<!-- END Blocktranslator -->