<?php
if (eregi("block-SiteIndex.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	if ($GLOBALS['page_name'] || $GLOBALS['page_id'] || $GLOBALS['category_id'])
	{
		if ($GLOBALS['page_id'])
		{
			$page = ExecMethod('sitemgr.Pages_SO.getPage',$GLOBALS['page_id']);
			$cat_id = (int) $page->cat_id;
			unset($page);
		}
		elseif ($GLOBALS['page_name'])
		{
			$page = ExecMethod('sitemgr.Pages_SO.getPageByName',$GLOBALS['page_name']);
			$cat_id = (int) $page->cat_id;
			unset($page);
		}
		elseif ($GLOBALS['category_id'])
		{
			$cat_id = (int) $GLOBALS['category_id'];
		}
		$bo = new bo;
		$catlinks = $bo->getCatLinks($cat_id,false);
		$pagelinks = $bo->getPageLinks($cat_id,false);
		$category = ExecMethod('sitemgr.Categories_BO.getCategory',$cat_id);
		$title = $category->name.' Section';
		unset($bo);

		$content = '';
		if (count($catlinks))
		{
			$content .= "\n".'<b>Subsections:</b><br>';
			foreach ($catlinks as $catlink)
			{
				$content .= "\n".'&nbsp;&middot;&nbsp;'.$catlink['link'].'<br>';
			}
			$content .= '<br>';
		}
		if (count($pagelinks)>1)
		{
			$content .= "\n".'<b>Pages:</b><br>';
			foreach ($pagelinks as $pagelink)
			{
				$content .= '&nbsp;&middot;&nbsp;'.$pagelink['link'].'<br>';
			}
		}
	}
	else
	{
		$content = '';
	}
?>
