Porting phpNuke Themes to phpGW's Web Content Manager


The Web Content Manager was designed to use phpNuke themes.  Unfortunately, phpNuke's haphazard design makes it very difficult to plug stuff in without some effort.  The combination of functions, echo's, callbacks, etc. is a nightmare.  But I've tried to improve it slightly.

Here are the key points if you want to port a phpNuke theme to sitemgr:

1) Links

	Delete the existing links in the themes.  They probably won't work.

	If you want to link from your theme to phpGroupWare or a sitemgr page, you obviously (I hope) can't use an absolute URL.  In fact, you can't even use a relative URL since you might cause a logged in user to lose their session.  

	The alternative is to figure out the page you want to link to and the parameters, if any, you want to pass.  For a sitemgr link, for example, to the table of contents page, you would do this:

	<a href="{?sitemgr:/index.php,toc=1}">Table of Contents</a>

	this will spit out a url like: http://machine/sitemgr-site/index.php?toc=1&etc...

	To link to the calendar in phpGroupWare, do this:

	<a href="{?phpgw:/calendar,}">Calendar</a>

	and it will create the appropriate link.  

	There are some shortcuts.  If you don't put a comma, then '/index.php' is assumed.  That 'index.php' will be in either the sitemgr or phpgw directory, depending.  Also, if you don't specify phpgw or sitemgr, it will assume sitemgr.  So in the Table of Contents example above, you could have just done this:

	<a href="{?toc=1}">Table of Contents</a>

	This only works if you're linking to index.php though.

2) Variables

	Two types of variables, regular php vars that can evaluated normally and template variables.  phpNuke uses php variables.  Ditch these.  Replace them with {var} style variables.  

	There are a couple of vars that you will automatically be able to use.  These are {header}, which obviously is replaced by the site-wide header, {user}, which is replaced by the login name of the logged in user, and {sitename} which probably is only used in the <title> and since this is done outside of your theme, you probably won't use it.

	Otherwise, you can use one of your functions in theme.php to populate variables.  Populating variables (the {var} type) is pretty simple.  Just call the add_theme_var() function.  For example, if you have a theme html file like this:

	Current app name is: {appname}

	You would populate the variable like this:

	add_theme_var('appname',$GLOBALS['phpgw_info']['current_app']);

3) Reserved variables

	First thing you need to do is edit header.html and add the {header} variable somewhere.  Probably right next to the logo.  

	Next, if you want a block that results from your OpenTable() and CloseTable() theme calls (if you don't know what I'm talking about, don't sweat it -- this is only for people who are familiar with phpNuke themes and how they work -- skip to the next section), then you can use a var like this: {opentable} and {closetable} to surround whatever you want blocked out.  {opentable2} and {closetable2} also work.  

4) Changes

	A) Var Parsing

	Now for all of this to work, you need to go through your themes searching for where an eval call is made.  Right above the eval line, which looks something like this:
	
	eval($thefile);

	put this:

	$thefile = parse_theme_vars($thefile);

	B) OpenTable/CloseTable changes

	OK, in classic phpNuke fashion, some functions echo output directly, others return a content string.  LAME.  For everything to work properly in sitemgr (blocks as well as the reserved vars above), you need to edit the OpenTable, OpenTable2, CloseTable, and CloseTable2 functions.  Just run through these functions and replace all of the echo's with $content .= '...'; instead.  Make sure $content=''; at the top of the function.  Then make the last line return $content;.  So, for example:

function OpenTable() {
    global $bgcolor1, $bgcolor2;
	echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
}

	would become:

function OpenTable() {
    global $bgcolor1, $bgcolor2;
	$content = "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
	return $content;
}


	That's all folks.

