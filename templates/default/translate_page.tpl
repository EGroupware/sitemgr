<form name="translatepage" method="POST">
<input type="hidden" name="page_id" value="{pageid}">

<table align="center" border ="0" width="80%" cellpadding="5" cellspacing="1">
	<tr>	
		<td align="center" colspan="3"><u><b>{translate}</b></u></td>
	</tr>
	<tr>
		<td align="center" colspan="3"><font size="2" color="#FF0000"><b>&nbsp;{error_msg}</b></font></td>
	</tr>
	<tr>
		<td>{lang_pagename}</td>
		<td align="center" colspan="2" style="font-weight:bold">{pagename}</td>
	<tr>
		<td>{lang_refresh}</td><td>{showlang}</td><td>{savelang}</td>
	</tr>
	<tr>
		<td>{lang_pagetitle}:</td>
		<td>{showpagetitle}</td>
		<td><input type="text" size="50" name="savepagetitle" value="{savepagetitle}"></td>
	</tr>
	<tr>
		<td>{lang_pagesubtitle}:</td>
		<td>{showpagesubtitle}</td>
		<td><input type="text" size="50" name="savepagesubtitle" value="{savepagesubtitle}"></td>
	</tr>
	<tr>
		<td>{lang_pagecontent}:</td>
		<td style="vertical-align:top">{showpagecontent}</td>
		<td><textarea rows="13" cols="50" type="text" name="savepagecontent">{savepagecontent}</textarea></td>
	</tr>
	<tr>
		<td align="right"><input type="reset" name="reset" value="{lang_reset}"></td>
 		<td align="left"><input type="submit" name="btnSavePage" value="{lang_save}"></td>
	</tr>
</table>
</form>