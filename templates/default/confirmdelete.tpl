<!-- BEGIN ConfirmDelete -->
<font size="+1" color="red"><b>{deleteheader}</b></font>
<form action="{actionurl}" method="post">
<input type="hidden" name="deleteconfirmed" value="{category_id}">
<input type="hidden" name="category_id" value="{category_id}">
<input type="submit" name="btnDelete" value="{lang_yes}">
<input type="submit" name="btnCancel" value="{lang_no}">
</form>
<!-- END ConfirmDelete -->

