<!-- BEGIN sitemgr_prefs -->

	<p>
	The Administrator (that's you) has a number of things to setup before the Web Content Manager can be used.  Besides filling out the values on this page, there is a config.inc.php file that needs to be edited manually.
	</p>
	<p>
	First, a quick overview on the program.  There are two phpGroupWare apps: sitemgr and sitemgr-link.  The first is used by people with administrator and contributor rights to manage the content and look and feel of the generated web site.  The second app is really a placeholder that redirects the phpGroupWare user to the generated site.  
	</p>
	<p>
	Finally, the generated site is almost like another application.  It resides in its own directory (called sitemgr-site by default), which can be anywhere.  Presumably you'll want this to be outside of the phpgw directory tree, although it really doesn't matter where it is.  This directory is the one with the config.inc.php file in it that needs to be edited.  Some of the data in there is duplicated from what's on this page.  Remember that if you make a change you should check both places.
	</p>
	<hr>
	<form action="{formaction}" method="post">

	URL to sitemgr-site directory: <br><input type="text" name="sitemgr_gen_url" value="{sitemgr-gen-url}" size="100"><br>
	<i>Note: the url can be relative or absolute.  Directory name must end in a slash.</i><br><br>

	Filesystem location of sitemgr-site directory: <br><input type="text" name="sitemgr_gen_dir" value="{sitemgr-gen-dir}" size="100"><br>
	<i>Note: this must be an absolute directory location.  <b>NO trailing slash.</b></i><br><br>

	Default Home Page ID Number: <br><input type="text" name="home_page_id" value="{home-page-id}" size="10"><br>
	<i>Note: This should be a page that is readable by everyone.  If you leave this blank, the Site Index will be shown by default.</i><br><br>

	Site name: <br><input type="text" name="sitemgr_site_name" value="{sitemgr-site-name}" size="100"><br>
	<i>Note: This is used chiefly for meta data and the titlebar title.</i><br><br>

	<input type="submit" name="btnSave" value="Save">
	</form>
	<hr>
	<p>That's all the prefs for now... more to come later.</p>
<!-- END sitemgr_prefs -->
