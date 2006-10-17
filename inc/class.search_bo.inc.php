<?php
	/**
	* sitemgr - search business object
	*
	* @link http://www.egroupware.org
	* @author Jose Luis Gordo Romero <jgordor@gmail.com>
	* @package sitemgr
	* @copyright Jose Luis Gordo Romero <jgordor@gmail.com>
	* @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
	* @version $Id$
	*/
	
	class search_bo
	{
		var $so,$pages_bo,$catbo;

		function search_bo()
		{
			//all sitemgr BOs should be instantiated via a globalized Common_BO object,
			$this->so =& CreateObject('sitemgr.search_so', true);
			$this->catbo = &$GLOBALS['Common_BO']->cats;
			$this->pages_bo = &$GLOBALS['Common_BO']->pages;
		}
		
		function search($query)
		{
					
			$matches = 0;
			$searched = $this->so->search($query);
			if ($searched)
			{
				foreach ($searched as $item) 
				{			
					if ($GLOBALS['Common_BO']->acl->can_read_category($item['cat_id']))//$has_perm)
					{
						// Content in a category (not page) has page_id=0, then don't search for info
						
						$cat = $this->catbo->getCategory($item['cat_id'],$GLOBALS['sitemgr_info']['userlang']);
						
						if ($item['page_id'] != 0)
						{
							$page = $this->pages_bo->getPage($item['page_id'],$GLOBALS['sitemgr_info']['userlang']);	
						}
						
						$res .= '<a href="'.sitemgr_link('category_id='.$item['cat_id']).'">' . lang('category') . ': '.$cat->name.'</a>"';
						if ($page)
						{
							$res .= ' -> <a href="'.sitemgr_link('page_name='.$page->name).'">' . lang('Page') . ': '.$page->name.'</a>"';
						}
						$res .= "<hr>";
						$matches = $matches + 1;
					}
				} 
				return array('search' => $query, 'matches' => $matches, 'result' => $res);
			}
			else
			{
				return array('search' => $query, 'matches' => $matches, 'result' => "No records found");
			}
		}			
	}
?>
