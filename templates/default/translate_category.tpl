<form name="translatecategory" method="POST">
<input type="hidden" name="category_id" value="{catid}">

<table align="center" border ="0" width="80%" cellpadding="5" cellspacing="1">
	<tr>	
		<td align="center" colspan="3"><u><b>{translate}</b></u></td>
	</tr>
	<tr>
		<td align="center" colspan="3"><font size="2" color="#FF0000"><b>&nbsp;{error_msg}</b></font></td>
	</tr>
	<tr>
		<td>{lang_refresh}</td><td>{showlang}</td><td>{savelang}</td>
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
		<td align="right"><input type="reset" name="reset" value="{lang_reset}"></td>
 		<td align="left"><input type="submit" name="btnSaveCategory" value="{lang_save}"></td>
	</tr>
</table>
</form>