<!-- BEGIN form -->
<form action="{actionurl}" method ="POST">
<input type="hidden" name="pageid" value="{pageid}">
<input type="hidden" name="category_id" value="{category_id}">
<table align='center'>
	<tr>
		<td colspan='2' align='center'><font size='4'><b>{add_edit}</b></font></td>
	</tr>
	<tr>
		<td colspan='2' align='center'><font size='2' color='#ff0000'><b>{message}</b></font></td>
	</tr>
	<tr>
		<td><b>Name: <font size='2' color='#ff0000'>*</font></b></td>
		<td><input size="40" type="text" name="name" id="name" value="{name}"></td>
	</tr>
	<tr>
		<td><br></td>
		<td><i><b><font size='2' color='#ff0000'>(Do not put spaces or punctuation in the Name field.)</font></b></i></td>
	<tr>	
		<td><b>Title: <font size='2' color='#ff0000'>*</font></b></td>
		<td><input size="40" type="text" name="title" value="{title}"></td>
	</tr>
	<tr>
		<td><b>Subtitle: </b></td>
		<td><input size="40" type="text" name="subtitle" value="{subtitle}"></td>
	</tr>
	<tr>
		<td colspan='2'><b>Main Content: <font size='2' color='#ff0000'>*</font></b></td>
	</tr>
	<tr>
		<td colspan='2'><textarea ROWS="13" COLS="50" type="text" name="main">{main}</textarea>	</td>
	</tr>
	<tr>
		<td align='right'><input type="reset" name="btnReset" value="Reset"></td>
		<td align='left'><input type="submit" name="btnSave" value="Save"></td>
	<tr>
	<tr>
		<td align='center' colspan='2'><font size='2' color='#ff0000'><b>* Required Fields</b></font></td>
	</tr>
	<tr>
		<td align='right' colspan='2'><a href="{goback}">Go back to Page Manager</a></td>
	</tr>
</table>
</form>
<!-- END form -->
