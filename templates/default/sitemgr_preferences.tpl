<!-- BEGIN sitemgr_prefs -->

	<b>{setup_instructions}</b>
	<p>
	There are two subdirectories off of your sitemgr directory that you should move before you do anything else.  You don't <i>have</i> to move either of these directories, although you will probably want to.  
	</p>
	<p>
	The first directory to think about is sitemgr-link.  If you move this to the parent directory of sitemgr (your phpgroupware root directory) then you can use setup to install the app and everyone with access to the app will get an icon on their navbar that links them directly to the public web site.  If you don't want this icon, there's no reason to ever bother with the directory.
	</p>
	<p>
	The second directory is the sitemgr-site directory.  This can be moved <i>anywhere</i>.  It can also be named <i>anything</i>.  Wherever it winds up, when you point a web browser to it, you will get the generated website.  Assuming, of course, that you've accurately completed the setup fields below and also <b><i>edited the config.inc.php</i></b> file.
	</p>
	<p>
	The config.inc.php file needs to be edited to point to the phpGroupWare directory.  Copy the config.inc.php.template file to config.inc.php and then edit it.
	<p>
	<hr>
	<b>{options}</b>
	</p>
	<p>
	<form action="{formaction}" method="post">
<center>
<table border="0" width="90%" cellspacing="8">
<!-- BEGIN PrefBlock -->
	<tr>
		<td>
			<table border="1" cellpadding="5" cellspacing="0" width="100%">
			<tr><td>
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td width="50%" valign="top">
						<b>{pref-title}</b><br>
						{pref-input}
					</td>
					<td width="50%" valign="bottom">
						<i>{pref-note}</i>
					</td>
				</tr>
			</table>
			</td></tr>
			</table>
		</td>
	</tr>
<!-- END PrefBlock -->
</table>
</center>

	<input type="submit" name="btnSave" value="{lang_save}">
	</form>
<!-- END sitemgr_prefs -->
