<!-- BEGIN ConfirmDelete -->
<font size="+1" color="red"><b>Are you sure you want to delete the category "{category}" and all of its associated pages?  You cannot retrieve the deleted pages if you continue.</b></font>
<form action="{actionurl}" method="post">
<input type="hidden" name="deleteconfirmed" value="{category_id}">
<input type="hidden" name="category_id" value="{category_id}">
<input type="submit" name="btnDelete" value="Yes, please delete it">
<input type="submit" name="btnCancel" value="Cancel the delete">
</form>
<!-- END ConfirmDelete -->

