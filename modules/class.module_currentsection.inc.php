<?php

class module_currentsection extends Module
{
	function module_currentsection()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Current Section');
		$this->description = lang('This block displays the current section\'s table of contents');
	}

	function get_content(&$arguments,$properties)
	{
		global $page;
		if ($page->cat_id == CURRENT_SITE_ID || !$page->cat_id)
		{
			return '';
		}

		$catlinks = $GLOBALS['objbo']->getCatLinks((int)$page->cat_id,False,True);
		$pagelinks = $GLOBALS['objbo']->getPageLinks($page->cat_id,False,True);
		$category = $GLOBALS['objbo']->getcatwrapper($page->cat_id);
		$this->block->title = $category->name;
		$parent = $category->parent;
		unset($category);

		$content = '';
		if ($parent && $parent != CURRENT_SITE_ID)	// do we have a parent?
		{
			$parentcat = $GLOBALS['objbo']->getcatwrapper($parent);
			$content .= "\n<b>".lang('Parent Section:').'</b><br>&nbsp;&middot;&nbsp;<a href="'.
				sitemgr_link2('/index.php','category_id='.$parent).'" title="'.$parentcat->description.'">'.$parentcat->name.
				'</a><br><br>';
			unset($parentcat);
		}
		if (count($catlinks))
		{
			$content .= "\n<b>".lang('Subsections:').'</b><br>';
			foreach ($catlinks as $catlink)
			{
				$content .= "\n".'&nbsp;&middot;&nbsp;'.$catlink['link'].'<br>';
			}
			$content .= '<br>';
		}
		if (count($pagelinks)>1 || (count($pagelinks)>0 && $content))
		{
			$content .= "\n<b>".lang('Pages:').'</b>';
			$content .= ' (<a href="'.sitemgr_link2('/index.php','category_id='.$page->cat_id).
				'"><i>'.lang('show all').'</i></a>)<br>';
			foreach($pagelinks as $pagelink_id => $pagelink)
			{
				if ($page->page_id && $page->page_id == $pagelink_id)
				{
					$content .= '&nbsp;&gt;'.$pagelink['link'].'&lt;<br>';
				}
				else
				{
					$content .= '&nbsp;&middot;&nbsp;'.$pagelink['link'].'<br>';
				}
			}
		}
		return $content;
	}
}
