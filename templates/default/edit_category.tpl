<!-- BEGIN edit category -->
<form action="{actionurl}" method="POST">
<input type="hidden" name="btnSaveCategory" value="True">
<input type="hidden" name="catid" value="{catid}">
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
					<td colspan="2"><b><u>Basic Settings:</u></b></td>
				</tr>
				<tr>
					<td>Category Name:</td>
					<td><input type="text" name="catname" value="{catname}"></td>
				</tr>
				<tr>
					<td>Category Description:</td>
					<td><textarea ROWS="3" COLS="50" name="catdesc">{catdesc}</textarea></td>
				</tr>
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
					<td colspan="3"><b><u>Group Access Permissions:</u></b></td>
				</tr>
				<tr>
					<td align="center" width="33%"><u>Group Name</u></td>
					<td align="center" width="33%"><u>Read Permission</u></td>
					<td align="center" width="33%"><u>Write Permission</u></td>
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
					<td colspan="2"><b><u>Individual Access Permission:</u></b></td>
				</tr>
                                <tr>
                                        <td align="center" width="33%"><u>User Name</u></td>
                                        <td align="center" width="33%"><u>Read Permission</u></td>
                                        <td align="center" width="33%"><u>Write Permission</u></td>
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
		<td align="right"><input type="reset" name="reset" value="Reset"></td>
 		<td align="left"><input type="submit" name="save" value="Save"></td>
	</tr>
</table>
</form>
<!-- END edit category -->

