<?php
/* blockconfig: <title>Current Section</title> */
/* blockconfig: <description>This block displays the current section</description> */
/* blockconfig: <view>0</view> (everybody) */
if (eregi("block-SiteIndex.php",$PHP_SELF)) {
	Header("Location: index.php");
	die();
}

	if ($GLOBALS['page_name'] || $GLOBALS['page_id'] || $GLOBALS['category_id'])
	{
		if ($GLOBALS['page_id'])
		{
			$page = ExecMethod('sitemgr.Pages_SO.getPage',$GLOBALS['page_id']);
			$page_id = $GLOBALS['page_id'];
			$cat_id = (int) $page->cat_id;
			unset($page);
		}
		elseif ($GLOBALS['page_name'])
		{
			$page = ExecMethod('sitemgr.Pages_SO.getPageByName',$GLOBALS['page_name']);
			$page_id = $page->id;
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
		$parent = $category->parent;
		unset($bo);
		unset($category);

		$content = '';
		if ($parent)
		{
			$parentcat = ExecMethod('sitemgr.Categories_BO.getCategory',$parent);
			$content .= "\n".'<b>Parent Section:</b><br>&nbsp;&middot;&nbsp;<a href="'.
				sitemgr_link2('/index.php','category_id='.$parent).'">'.$parentcat->name.
				'</a><br><br>';
			unset($parentcat);
		}
		if (count($catlinks))
		{
			$content .= "\n".'<b>Subsections:</b><br>';
			foreach ($catlinks as $catlink)
			{
				$content .= "\n".'&nbsp;&middot;&nbsp;'.$catlink['link'].'<br>';
			}
			$content .= '<br>';
		}
		if (count($pagelinks)>1 || (count($pagelinks)>0 && $content))
		{
			$content .= "\n".'<b>Pages:</b>';
			$content .= ' (<a href="'.sitemgr_link2('/index.php','category_id='.$cat_id).
				'"><i>show all</i></a>)<br>';
			reset($pagelinks);
			while(list($pagelink_id,$pagelink) = each($pagelinks))
			{
				if ($page_id && $page_id == $pagelink_id)
				{
					$content .= '&nbsp;&gt;'.$pagelink['link'].'&lt;<br>';
				}
				else
				{
					$content .= '&nbsp;&middot;&nbsp;'.$pagelink['link'].'<br>';
				}
			}
		}
	}
	else
	{
		$content = '';
	}
?>
