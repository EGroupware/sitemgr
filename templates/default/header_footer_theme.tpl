<!-- BEGIN Form -->
<!-- BEGIN Header -->
<table border="0" width="30%" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td align="center"><font face="{font}"><b>
		<a href="{link_categories}">{lang_categories}</a>
			&nbsp;
			&nbsp;
		<a href="{link_notes}">{lang_notes}</a></b></font></td>
	</tr>
</table>
<!-- End Header -->
<!-- Begin Footer -->
<table border="0" width="30%" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td align="center" bgcolor="{bg_color}"><font face="{font}"><b>
		<a href="{link_categories}">{lang_categories}</a>
			&nbsp;
			&nbsp;
		<a href="{link_notes}">{lang_notes}</a></b></fon></td>
	</tr>
</table>
<!-- End Footer -->
<!-- Begin Theme List -->
<table border="0" width="30%" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td><b>Please Select A Theme For The Site.</b></td>
	</tr>
	<tr>
		<td align="center">
			<form action="{theme_selections}" name="theme" method="POST">
			<select name="filter" onChange="this.form.submit();">{theme_list}</select>
			</form>
		</td>
	</tr>
</table>
<!-- End Theme List -->
<!-- END Form -->
