<!-- BEGIN editcategory -->
<form action="{actionurl}" method="POST">
<input type="hidden" name="btnSaveCategory" value="True">
<input type="hidden" name="catid" value="{catid}">
<input type="hidden" name="old_parent" value="{old_parent}">
<table align="center" border ="0" width="80%" cellpadding="5" cellspacing="0">
	<tr>
		<td align="center" colspan="2"><u><b>{add_edit}</b></u></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><font size="2" color="#FF0000"><b>&nbsp;{error_msg}</b></font></td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" cellpadding="5" cellspacing="0">
				<tr>
					<td colspan="2"><b><u>{lang_basic}:</u></b></td>
				</tr>
				<tr>
					<td>{lang_catname}:</td>
					<td><input type="text" name="catname" value="{catname}"></td>
				</tr>
				<tr>
					<td>{lang_catsort}:</td>
					<td><input type="text" name="sort_order" value="{sort_order}"></td>
				</tr>
				<tr>
					<td>{lang_catparent}:</td>
					<td>{parent_dropdown}</td>
				</tr>
				<tr>
					<td>{lang_catdesc}:</td>
					<td><textarea ROWS="3" COLS="50" name="catdesc">{catdesc}</textarea></td>
				</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<td colspan="2">&nbsp;<!-- <hr width="80%"> --></td>
	</tr>
	<tr>
		<td align="right"><input type="reset" name="reset" value="{lang_reset}"></td>
 		<td align="left"><input type="submit" name="save" value="{lang_save}"> {savelang}</td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" cellpadding="2" cellspacing="2">
				<tr>
					<td colspan="3"><b><u>{lang_groupaccess}</u></b></td>
				</tr>
				<tr>
					<td align="center" width="33%"><u>{lang_groupname}</u></td>
					<td align="center" width="33%"><u>{lang_readperm}</u></td>
					<td align="center" width="33%"><u>{lang_writeperm}</u></td>
				</tr>
				<!-- BEGIN GroupBlock -->
				<tr>
					<td align="center" bgcolor="dddddd" width="33%">{groupname}</td>
 					<td align="center" bgcolor="dddddd" width="33%"><input type="checkbox" {checkedgroupread} name="groupaccessread[i{group_id}][read]" value="checked"></td>
					<td align="center" bgcolor="dddddd" width="33%"><input type="checkbox" {checkedgroupwrite} name="groupaccesswrite[i{group_id}][write]" value="checked"></td>
				</tr>
				<!-- END GroupBlock -->
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;<!-- <hr width="80%"> --></td>
	</tr>
	<tr>
		<td colspan="2">
			<table align="center" border="0" width="80%" cellpadding="2" cellspacing="2">
				<tr>
					<td colspan="2"><b><u>{lang_useraccess}:</u></b></td>
				</tr>
                                <tr>
                                        <td align="center" width="33%"><u>{lang_username}</u></td>
                                        <td align="center" width="33%"><u>{lang_readperm}</u></td>
                                        <td align="center" width="33%"><u>{lang_writeperm}</u></td>
                                </tr>
                                <!-- BEGIN UserBlock -->
                                <tr>
                                        <td bgcolor="dddddd" align="center">{username}</td>
                                        <td bgcolor="dddddd" align="center"><input type="checkbox" {checkeduserread} name="individualaccessread[i{user_id}][read]" value="checked"></td>
                                        <td bgcolor="dddddd" align="center"><input type="checkbox" {checkeduserwrite} name="individualaccesswrite[i{user_id}][write]" value="checked"></td>
                                </tr>
                                <!-- END UserBlock -->

			</table>
		</td>
	</tr>
	<tr>
                <td colspan="2">&nbsp;<!-- <hr width="80%"> --></td>
	</tr>
	<tr>
		<td align="right"><input type="reset" name="reset" value="{lang_reset}"></td>
 		<td align="left"><input type="submit" name="save" value="{lang_save}"> {savelang}</td>
	</tr>
</table>
</form>
<!-- END editcategory -->
