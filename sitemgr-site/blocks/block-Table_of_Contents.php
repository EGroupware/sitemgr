<?php
if (eregi("block-Table_of_Contents.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	$title = 'Table of Contents';
	$bo = new bo;
	$indexarray = $bo->getCatLinks();
	unset($bo);
	$content = "\n".'<table border="0" cellspacing="0" cellpadding="0" width="100%">';
	foreach($indexarray as $cat)
	{
		$content .= "\n".'<tr><td align="right" valign="top" width="5%">'.
			'&middot;&nbsp;</td><td><b>'.$cat['link'].'</b></td></tr>';
	}
	$content .= "\n</table>";
	if (count($indexarray)==0)
	{
		$content='You do not have access to any content on this site.';
	}
?>
