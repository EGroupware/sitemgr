<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{module_manager} {cat_name}</div>
<div><a href={findmodules}>{lang_findmodules}</a></div>
<br>
<div>{help}</div>
<br>
<!-- BEGIN Contentarea -->
<h4>{title}</h4>
<center style="color:red">{error}</center>
<table align="center">
	<tr>
		<td>
			<form method="POST">
				<select style="vertical-align:middle" size="10" name="inputmodules[]" multiple="multiple">
					{selectmodules}
				</select>
				<input type="hidden" name="inputarea" value={contentarea} />
				<input style="vertical-align:middle" type="submit" name="btnselect" value="Select allowed modules"/>
			</form>
		</td>
		<td>
			<form action="{configureurl}" method="POST">
				<select style="vertical-align:middle" size="10" name="inputmodule_id">
					{configuremodules}
				</select>
				<input type="hidden" name="inputarea" value={contentarea} />
				<input style="vertical-align:middle" type="submit" value="Configure module properties"/>
			</form>
		</td>
	</tr>
</table>
<!-- END Contentarea -->
<div align="center">{managelink}</div>