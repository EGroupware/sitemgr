Porting phpNuke Themes to phpGW's Web Content Manager
-----------------------------------------------------

	The initial version of SiteMgr used a variation on phpNuke's themes.  This was a foolish design mistake.  For backwards compatibility (funny in a project so young), we will continue to support adapted phpNuke themes as per the section below entitled "Making a phpNuke theme work in SiteMgr as a theme." 

	If you're smart, you'll ditch the theme.php file and the other crap associated with phpNuke themes and use templates instead.  They are *much* cleaner.  And more powerful, too.

Making a phpNuke theme work in SiteMgr as a template
----------------------------------------------------

	Make a subdirectory of the templates dir in sitemgr-site and name it whatever you want to call your template set.  You will need a main.tpl file, a sideblock.tpl file, a centerblock.tpl file and, lastly, a newsblock.tpl file.  

	Each of these files look exactly like an html file, except that they take variables where you want the header, footer, content, etc., to go.  Also, when you want to link (ie, <a href="some-url">...</a>) somewhere on this site, you will need to use a special syntax, described below, to keep your templates portable and users of the site logged in.

	main.tpl
	--------

	This file starts with a <body> tag and ends with a </body> tag.  In between you put the following variables (complete with the curly braces) wherever you want them to appear within the html.  You can add them as many times as you want, including none at all.

	{site_header} -- Admin specified site header... might contain html.
	{site_name} -- Admin specified site name... automatically displayed in the Title
	{user} -- Name of the currently logged in user
	{page_title} -- Title of the currently displayed page (ie, <h1>{page_title}</h1>)
	{page_subtitle} -- Subtitle of the currently displayed page
	{page_content} -- main body of the currently displayed page
	{left_blocks} -- the little boxes that go on the left side of the content
	{right_blocks} -- the little boxes that go on the right side of the content
	{site_footer} -- Admin specified site footer... may contain html.

	Don't forget to use the special syntax for all links to sitemgr pages or phpgroupware pages.  It is specified below.

	sideblock.tpl
	-------------
	
	This file starts with:
		<!-- BEGIN SideBlock -->
	and ends with:
		<!-- END SideBlock -->

	exactly like shown above, cAsE is sensitive.

	In between you put the html that will display each block (boxes on the side).  You get two variables:

	{block_title} -- Title of the block.. duh
	{block_content} -- Figure it out.

	centerblock.tpl
	---------------

	Exactly the same as sideblock.tpl except that "SideBlock" should be replaced by "CenterBlock".

	newsblock.tpl
	-------------
	Begins with:
		<!-- BEGIN NewsBlock -->
	and ends with:
		<!-- END NewsBlock -->
	
	You get to use these variables:

	{news_title} -- subject of the article
	{news_category}	-- topic, if any, the article is posted in
	{news_date} -- date submitted
	{news_submitter} -- submitted by...
	{news_content} -- article (or the first paragraph, anyway)
	{news_more} -- This will contain a <a href> link to read the rest of the article if its longer than one paragraph.  

	Making URLs
	-----------

	Number one of the next section describes links in depth.  They are applied the same way in Themes and Templates.  Here's the basics though:

	(1) {?sitemgr:/index.php,page_name=mypage} 

	will create a url like this:

	http://yourmachine.com/path/to/sitemgr-site/index.php?page_name=mypage

	(2) {?phpgw:/addressbook,order=n_given&sort=ASC}

	will create a url to the addressbook, sorted by first name:

	http://yourmachine.com/path/to/phpgw/addressbook/index.php?order=n_given&sort=ASC

	Note that for a shortcut for link (1) you could do this:

	{?page_name=mypage}

	and for link two, if you didn't care about the sorting, you could do this:

	{?phpgw:/addressbook}.
	

Making a phpNuke theme work in SiteMgr as a theme
-------------------------------------------------

The Web Content Manager was originally designed to use phpNuke themes.  Unfortunately, phpNuke's haphazard design makes it very difficult to plug stuff in without some effort.  The combination of functions, echo's, callbacks, etc. is a nightmare.  But I've tried to improve it slightly.

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

