<!-- BEGIN Block -->
<!-- BEGIN Moduleeditor -->
{standalone}
<script>window.focus();</script>
<div style="border-width:2px;border-style:solid; margin:5mm;padding:5mm">
<h4>{moduleinfo}: {description}</h4>
<div style="color:red; text-align: center; font-weight:bold;">{validationerror}</div>
<form method="POST">
<table>
{standardelements}
</table>
<!-- BEGIN Version -->
<div style="border-width:1px; border-style:solid; margin:1mm; padding:2mm">
<b>Version {version_id}</b>
<select name="inputstate[{version_id}]">{state}</select>
<input type="submit" value="{deleteversion}" name="btnDeleteVersion[{version_id}]" />
<table width="100%">
	{versionelements}
</table>
</div>
<!-- END Version -->
<input type="hidden" value="{blockid}" name="inputblockid" />
<input type="submit" value="{savebutton}" name="btnSaveBlock" /> {savelang}
<input type="submit" value="{deletebutton}" name="btnDeleteBlock" />
<input type="submit" value="{createbutton}" name="btnCreateVersion" />
{donebutton}
</form>
</div>
<!-- END Moduleeditor -->

<!-- BEGIN Moduleview -->
<div style="border-width:1px; border-style:solid; margin:1mm; padding:2mm">
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
<!-- END Block -->

<!-- BEGIN EditorElement -->
	<tr>
		<td>{label}</td>
		<td>{form}</td>
	</tr>
<!-- END EditorElement -->
<!-- BEGIN EditorElementLarge -->
	<tr>
		<td colspan="2">
			{label}<br />
			{form}
		</td>
	</tr>
<!-- END EditorElementLarge -->
