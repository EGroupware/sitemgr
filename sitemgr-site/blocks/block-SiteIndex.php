<?php
/* blockconfig: <title>Root Site Index</title> */
/* blockconfig: <description>This block displays the root categories</description> */
/* blockconfig: <view>0</view> (everybody) */
if (eregi("block-SiteIndex.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	$bo = new bo;
	$indexarray = $bo->getIndex(false,true);
	unset($bo);
	$content = "\n".'<table border="0" cellspacing="0" cellpadding="0" width="100%">';
	$catname = '';
	foreach($indexarray as $page)
	{
		if ($catname!=$page['catname']) //category name change
		{
			if ($catname=='')
			{
				$break = '';
			}
			else
			{
				$break = '<br>';
			}
			$catname = $page['catname'];
			$content.="\n".'<tr><td width="15%" colspan="2">'.$break.'&nbsp;<b>'.
				$page['catlink'].'</b></td></tr>'."\n";
		}
		if (!$page['hidden'])
		{
			$content .= "\n".'<tr><td align="right" valign="top" width="15%">'.
				'&middot;&nbsp;</td><td>'.$page['pagelink'].'</td></tr>';
		}
	}
	$content .= "\n</table>";
	$content .= '<br>&nbsp;&nbsp;<i><a href="'.sitemgr_link2('/index.php','index=1').'"><font size="1">(' . lang('View full index') . ')</font></a></i>';
	if (count($indexarray)==0)
	{
		$content=lang('You do not have access to any content on this site.');
	}
?>
