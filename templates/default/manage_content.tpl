<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{content_manager} {page_or_cat_name}</div>
{help}

<!-- BEGIN Contentarea -->
<h2 style="text-align:center">{contentarea} {area}</h2>
<center style="color:red">{error}</center>

<!-- BEGIN Module -->
<!-- BEGIN Moduleeditor -->
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h4>{moduleinfo}: {description}</h4>
<span style="color:red">{validationerror}</span>
<form method="POST">
<table>
<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->
</table>
<input type="hidden" value="{blockid}" name="blockid" />
<input type="submit" value= {savebutton} name="btnSaveBlock" /> {savelang}
<input type="submit" value= {deletebutton} name="btnDeleteBlock" />
</form>
</div>
<!-- END Moduleeditor -->

<!-- BEGIN Moduleview -->
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h4>{moduleinfo}: {description}</h4>
<table>
<!-- BEGIN ViewElement -->
	<tr>
		<td>{label}</td>
		<td>{value}</td>
	</tr>
<!-- END ViewElement -->
</table>
</div>
<!-- END Moduleview -->
<!-- END Module -->
<div align="center">
{addblockform}
</div>
<!-- END Contentarea -->

<div align="center">{managelink}</div>
