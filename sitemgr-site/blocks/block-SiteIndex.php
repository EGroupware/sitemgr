<?php
if (eregi("block-SiteIndex.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	$title = 'Site Index';
	$bo = new bo;
	$indexarray = $bo->getIndex();
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
				$catname.'</b></td></tr>'."\n";
		}
		$content .= "\n".'<tr><td align="right" valign="top" width="15%">'.
			'&middot;&nbsp;</td><td>'.$page['pagelink'].'</td></tr>';
	}
	$content .= "\n</table>";
	if (count($indexarray)==0)
	{
		$content='You do not have access to any content on this site.';
	}
?>
