<!-- BEGIN sitemgr_header -->
	<!-- <center> -->
	<b><u>{sitemgr_administration}</u></b>
		<br>
		<table border="0">
		<tr><td>{lang_sitename}</td><td>{sitename}</td></tr>
		</table>
		<!-- &nbsp;&nbsp;&nbsp; -->
		[
<!-- BEGIN admin -->
<i>
		<a href='{sitesadmin}'>{view_admin}</a>
		</i>&nbsp;|&nbsp
<!-- END admin -->
		<i>
		<a href='{mainmenu}'>{view_menu}</a>
		</i>&nbsp;|&nbsp;<i>
		<a href='{sitemgr-site}' target='_blank'>{view_site}</a>
		</i>
<!-- BEGIN switch -->
&nbsp;|&nbsp;<form name="siteselect" method="POST" style="display:inline" action="{mainmenu}"><select name="siteswitch" onChange="this.form.submit()">{sitelist}</select></form>&nbsp;
<!-- END switch -->
]
	<hr>
	<br>
	<!-- </center> -->
<!-- END sitemgr_header -->
