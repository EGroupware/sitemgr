<!-- BEGIN form -->
<form action="{actionurl}" method ="POST">
<input type="hidden" name="inputpageid" value="{page_id}">
<table align='center'>
	<tr>
		<td colspan='2' align='center'><font size='4'><b>{add_edit}</b></font></td>
	</tr>
	<tr>
		<td colspan='2' align='center'><font size='2' color='#ff0000'><b>{message}</b></font></td>
	</tr>
	<tr>
		<td><b>{lang_name}: <font size='2' color='#ff0000'>*</font></b></td>
		<td><input size="40" type="text" name="inputname" id="name" value="{name}"></td>
	</tr>
	<tr>
		<td><br></td>
		<td><i><b><font size='2' color='#ff0000'>(Do not put spaces or punctuation in the Name field.)</font></b></i></td>
	<tr>	
		<td><b>{lang_title}: <font size='2' color='#ff0000'>*</font></b></td>
		<td><input size="40" type="text" name="inputtitle" value="{title}"></td>
	</tr>
	<tr>
		<td><b>{lang_subtitle}: </b></td>
		<td><input size="40" type="text" name="inputsubtitle" value="{subtitle}"></td>
	</tr>
	<tr>
		<td><b>{lang_sort}: </b></td>
		<td><input size="10" type="text" name="inputsort" value="{sort_order}"></td>
	</tr>
	<tr>
		<td><b>{lang_move}: </b></td>
		<td>{move_to}</td>
	</tr>
	<tr>
		<td><b>{lang_state}: </b></td>
		<td><select name="inputstate">{stateselect}</select></td>
	</tr>
	<tr>
		<td align='right'><input type='checkbox' {hidden} name ="inputhidden"></td>
		<td>{lang_hide}</td>
	</tr>
	<tr>
		<td align='right'><input type="reset" name="btnReset" value="{lang_reset}"></td>
		<td align='left'><input type="submit" name="btnSave" value="{lang_save}"> {savelang}</td>
	</tr>
	<tr>
		<td align='center' colspan='2'><font size='2' color='#ff0000'><b>* {lang_required}</b></font></td>
	</tr>
	<tr>
		<td align='right' colspan='2'><a href="{goback}">{lang_goback}</a></td>
	</tr>
</table>
</form>
<!-- END form -->
