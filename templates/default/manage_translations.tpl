<table border='0' align='center' width='70%' cellpadding='0' cellspacing='1'>
	<tr>
		<td align='center'><B><u>{translation_manager}</u></B></td>
	</tr>
	<tr>
		<td>
		<table align='left' border='1' width='85%' cellspacing='0'>
			<tr>
				<td><u>{lang_catname}</u></td>
				<!-- BEGIN sitelanguages -->
				<td>{sitelanguage}</td>
				<!-- END sitelanguages -->
			</tr>
			<!-- BEGIN CategoryBlock -->
			<tr bgcolor='dddddd'>
				<td align='left' style='font-weight:bold' bgcolor='dddddd'>
				{category}
				</td>
				<!-- BEGIN langexistcat -->
				<td align="center">{catexistsinlang}</td>
				<!-- END langexistcat -->
				<td align='center' bgcolor='dddddd' valign="center" width='5%'>{translatecat}</td>
			</tr>
			<!-- BEGIN PageBlock -->
			<tr>
				<td align='left' style="padding-left:1cm">
				{page}
				</td>
				<!-- BEGIN langexistpage -->
				<td align="center">{pageexistsinlang}</td>
				<!-- END langexistpage -->
				<td align='center' valign="center" width='5%'>{translatepage}</td>
			</tr>
			<!-- END PageBlock -->
			<!-- END CategoryBlock -->
		</table>
		</td>
	</tr>		
</table>
