<body bgcolor="#505050" text="#000000" link="#363636" vlink="#363636" alink="#d5ae83">
<br>
<table cellpadding="0" cellspacing="0" width="100%" border="0" align="center" bgcolor="#ffffff">
<tr>
<td bgcolor="#cfcfbb">
<img height="16" alt="" hspace="0" src="themes/NukeNews/images/corner-top-left.gif" width="17" align="left">
<a href="index.php"><img src="themes/NukeNews/images/logo.gif" align="left" alt="" border="0" hspace="10"></a></td>
<td bgcolor="#999999"><IMG src="themes/NukeNews/images/pixel.gif" width="1" height="1" alt="" border="0" hspace="0"></td>
<td bgcolor="#cfcfbb" align="center">
<center>
	{site_header}
</center></td>
<td bgcolor="#cfcfbb" align="center">
<center>
	&nbsp;
</center></td>
<td bgcolor="#cfcfbb" valign="top"><img height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-top-right.gif" width="17" align="right"></td>
</tr></table>
<table cellpadding="0" cellspacing="0" width="100%" border="0" align="center" bgcolor="#fefefe">
<tr>
<td bgcolor="#000000" colspan="4"><IMG src="themes/NukeNews/images/pixel.gif" width="1" height=1 alt="" border="0" hspace="0"></td>
</tr>
<tr valign="middle" bgcolor="#dedebb">
<td width="15%" nowrap><font class="content" color="#363636">
		<b>Logged in as: {user}</b></font></td>
<td align="center" height="20" width="70%"><font class="content"><B>
			<a href="{?sitemgr:}">Home</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="{?toc=1}">Site Contents</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="{?index=1}">Site Index</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="{?phpgw:/index.php,}">phpGroupWare</a>
	</B></font>
</td>
<td align="right" width="15%"><font class="content"><b>
<script type="text/javascript">
<!--   // Array ofmonth Names
var monthNames = new Array( "January","February","March","April","May","June","July","August","September","October","November","December");
var now = new Date();
thisYear = now.getYear();
if(thisYear < 1900) {thisYear += 1900}; // corrections if Y2K display problem
document.write(monthNames[now.getMonth()] + " " + now.getDate() + ", " + thisYear);
// -->
</script></b></font></td>
<td>&nbsp;</td>
</tr>
<tr>
<td bgcolor="#000000" colspan="4"><IMG src="themes/NukeNews/images/pixel.gif" width="1" height="1" alt="" border="0" hspace="0"></td>
</tr>
</table>
<!-- FIN DEL TITULO -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top">
<td bgcolor="#ffffff"><img src="themes/NukeNews/images/pixel.gif" width="1" height="20" border="0" alt=""></td></tr></table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top">
<td bgcolor="#ffffff"><img src="themes/NukeNews/images/pixel.gif" width="10" height="1" border="0" alt=""></td>
<td bgcolor="#ffffff" width="150" valign="top">
{left_blocks}
</td><td><img src="themes/NukeNews/images/pixel.gif" width="15" height="1" border="0" alt=""></td><td valign="top" width="100%">
<h1>{page_title}</h1>
<h3>{page_subtitle}</h3>
{page_content}
</td><td><img src="themes/NukeNews/images/pixel.gif" width="15" height="1" border="0" alt=""></td><td valign="top" width="150">
{right_blocks}
</td><td bgcolor="#ffffff"><img src="themes/NukeNews/images/pixel.gif" width=10 height=1 border=0 alt="">
</td></tr></table>
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top">
<td align="center" height="17">
<IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-bottom-left.gif" width="17" align="left">
<IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-bottom-right.gif" width="17" align="right">
</td></tr></table>
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top">
<td><IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-top-left.gif" width="17" align="left"></td>
<td width="100%">&nbsp;</td>
<td><IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-top-right.gif" width="17" align="right"></td>
</tr><tr align="center">
<td width="100%" colspan="3">
{site_footer}
</td>
</tr><tr>
<td><IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-bottom-left.gif" width="17" align="left"></td>
<td width="100%">&nbsp;</td>
<td><IMG height="17" alt="" hspace="0" src="themes/NukeNews/images/corner-bottom-right.gif" width="17" align="right"></td>
</tr></table>
</body>
