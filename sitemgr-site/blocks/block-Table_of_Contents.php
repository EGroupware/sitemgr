<?php
/* blockconfig: <title>Table of contents</title> */
/* blockconfig: <description>This block displays the site's table of contents</description> */
/* blockconfig: <view>0</view> (everybody) */
if (eregi("block-Table_of_Contents.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	$bo = new bo;
	$indexarray = $bo->getCatLinks();
	unset($bo);
	$content = "\n".'<table border="0" cellspacing="0" cellpadding="0" width="100%">'.
		'<tr><td>';
	foreach($indexarray as $cat)
	{
		$space = str_pad('',$cat['depth']*18,'&nbsp;');
		$content .= "\n".'<table border="0" cellspacing="0" cellpadding="0" '.
			'width="100%"><tr><td align="right" valign="top" width="5">'.
			$space.'&middot;&nbsp;</td><td width="100%"><b>'.
			$cat['link'].'</b></td></tr></table>';
	}
	$content .= "\n</td></tr></table>";
	//$content .= '<br>&nbsp;&nbsp;<i><a href="'.sitemgr_link2('/index.php','toc=1').'"><font size="1">(View full contents)</font></a></i>';
	if (count($indexarray)==0)
	{
		$content=lang('You do not have access to any content on this site.');
	}
?>
