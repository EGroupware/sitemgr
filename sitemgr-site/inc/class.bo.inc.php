<?php
	/*************************************************************************\
	* phpGroupWare - Web Content Manager                                      *
	* http://www.phpgroupware.org                                             *
	* -------------------------------------------------                       *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id$ */

	class bo
	{
		var $pages_bo;
		var $headerfooter_bo;
		var $page_id;
		var $page;
		var $catbo;
		var $acl;

		function bo()
		{
			$this->catbo = CreateObject('sitemgr.Categories_BO');
			$this->pages_bo = CreateObject('sitemgr.Pages_BO');
			$this->pages_so = CreateObject('sitemgr.Pages_SO');
			$this->headerfooter_bo = CreateObject('sitemgr.headerFooter_BO');
			$this->page = CreateObject('sitemgr.Page_SO');
			$this->acl = CreateObject('sitemgr.ACL_BO');
		}

		function loadPage($page_id)
		{
			$this->page = $this->pages_bo->GetPage($page_id);
		}

		function loadIndex()
		{
			$this->page->title = 'Site Index';
			$this->page->subtitle = '';
			$indexarray = $this->getIndex();
			$content = "\n".'<table border="0" width="100%" align="left" cellspacing="1" cellpadding="0"><tr>';
			$catname = '';
			foreach($indexarray as $page)
			{
				$buffer = str_pad('', $page['catdepth']*24,'&nbsp;');
				if ($catname!=$page['catname']) //category name change
				{
					if ($catname!='') //not the first name change
					{
						$content .= '<br><br></td></tr></table></td></tr><tr>';
					}
					$content .= '<td>
					<table border="0" width="100%" cellspacing="0" align="left" cellpadding="0">
						<tr><td>'.$buffer.'</td>
						<td width="100%">';
					$catname = $page['catname'];
					if ($page['catdepth'])
					{
						$content .= '&middot;&nbsp;';
					}
					$content .= '<b>'.$catname.'</b> &ndash; <i>'.
						$page['catdescrip'].'</i>'."\n";
				}
				$content .= "\n".'<br>&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.$page['pagelink'];
			}
			$content .= "\n".'</td></tr></table></td></tr></table>';
			if (count($indexarray)==0)
			{
				$content='You do not have access to any content on this site.';
			}
			$this->page->content = $content;
		}

		function getIndex($showhidden=true, $rootonly=false)
		{
			$cats = $this->getCatLinks(0,!$rootonly);
			$index = array();

			if (count($cats)>0)
			{
				reset($cats);
				$content = "\n".'<ul>';
				while(list($cat_id,$cat) = each($cats))
				{
					$pages = $this->getPageLinks($cat_id,$showhidden);
					if (count($pages)>0)
					{
						foreach($pages as $link)
						{
							$index[] = array(
								'catname'=>$cat['name'],
								'catdepth'=>$cat['depth'],
								'catlink'=>$cat['link'],
								'catdescrip'=>$cat['description'],
								'pagename'=>$link['name'],
								'pagelink'=>$link['link'],
								'pagetitle'=>$link['title'],
								'pagesubtitle'=>$link['subtitle']
							);
						}
					}
					else
					{
						$index[] = array(
							'catname'=>$cat['name'],
							'catdepth'=>$cat['depth'],
							'catdescrip'=>$cat['description'],
							'catlink'=>$cat['link'],
							'pagelink'=>'No pages available'
						);
					}
				}
			}
			return $index;
		}

		function loadTOC($category_id=false)
		{
			/*
				If category_id is passed in, just show that category.  Otherwise,
				show all categories.
			*/
			if ($category_id)
			{
				$acl = CreateObject('sitemgr.ACL_BO');
				if($acl->can_read_category($category_id))
				{
					$links = $this->getPageLinks($category_id,true);
					$cat = $this->catbo->getCategory($category_id);
					$content = '';
					if ($cat)
					{
						$this->page->title = 'Category '.$cat->name;
						$this->page->subtitle = '<i>'.$cat->description.'</i>';
						$content .= '<b><a href="'.sitemgr_link2('/index.php','toc=1').'">Up to table of contents</a></b>';
						if ($cat->depth)
						{
							$content .= ' | <b><a href="'.sitemgr_link2('/index.php','category_id='.$cat->parent).'">Up to parent</a></b>';
						}
						$children = $this->getCatLinks((int) $category_id,false);
						if (count($children))
						{
							$content .= '<br><br><b>Subcategories:</b><br>';
							foreach ($children as $child)
							{
								$content .= '<br>&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.
									$child['link'].' &ndash; '.$child['description'];
							}
						}
						$content .= '<br><br><b>Pages:</b><br>';
						$links = $this->getPageLinks($category_id,true);
						if (count($links)>0)
						{
							foreach($links as $pg)
							{
								$content .= "\n<br>".
									'&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.$pg['link'];
								if (!empty($pg['subtitle']))
								{
									$content .= ' &ndash; <i>'.$pg['subtitle'].'</i>';
								}
								$content .= '';
							}
						}
						else
						{
							$content .= '<li>There are no pages in this section</li>';
						}
						$this->page->content=$content;
					}
					else
					{
						$ui = new ui;
						$ui->displayPage(-1);
						exit;
					}
				}
				else
				{
					// Following line will spit out an ambiguous not exist/ no permission error.
					$ui = new ui;
					$ui->displayPage(-1);
					exit;
				}
			}
			else
			{
				$this->page->title = 'Table of Contents';
				$this->page->subtitle = '';
				$content = '<b>Choose a category:</b><br>';
				$links = $this->getCatLinks();
				if (count($links)>0)
				{
					foreach($links as $cat)
					{
						$buffer = str_pad('', $cat['depth']*24,'&nbsp;').'&middot;&nbsp;';
						if (!$cat['depth'])
						{
							$buffer = '<br>'.$buffer;
						}
						$content .= "\n".$buffer.$cat['link'].' &mdash; <i>'.$cat['description'].
							'</i><br>';
					}
				}
				else
				{
					$content .= 'There are no sections available to you.';
				}
				$this->page->content=$content;
			}
			return true;
		}
		
		function getPageLinks($category_id, $showhidden=true)
		{
			$pages=$this->pages_bo->getPageIDList($category_id);
			foreach($pages as $page_id)
			{
				$page=$this->pages_bo->getPage($page_id);
				if ($showhidden || !$page->hidden)
				{
					if (strtolower($page->subtitle) == 'link')
					{
						$pglinks[$page_id] = array(
							'name'=>$page->name,
							'link'=>'<a href="'.$page->content.'">'.$page->title.'</a>',
							'title'=>$page->title,
							'subtitle'=>''
						);
					}
					else
					{
						$pglinks[$page_id] = array(
							'name'=>$page->name,
							'link'=>'<a href="'.sitemgr_link2('/index.php','page_name='.
								$page->name).'">'.$page->title.'</a>',
							'title'=>$page->title,
							'subtitle'=>$page->subtitle
						);
					}
				}
			}
			return $pglinks;
		}

		function getCatLinks($cat_id=0,$recurse=true)
		{
			$catlinks = array();
			if ($recurse)
			{
				$cat_list=$this->catbo->getPermittedCatReadNested($cat_id);
			}
			else
			{
				$cat_list=$this->catbo->getPermittedCategoryIDReadList($cat_id);
			}
			foreach($cat_list as $cat_id)
			{
				$category = $this->catbo->getCategory($cat_id);
				$catlinks[$cat_id] = array(
					'name'=>$category->name,
					'link'=>'<a href="'.sitemgr_link2('/index.php',
						'category_id='.$cat_id).'">'.$category->name.'</a>',
					'description'=>$category->description,
					'depth'=>$category->depth
				);
			}
			return $catlinks;
		}

		function get_header()
		{
			return $this->headerfooter_bo->getsiteheader();
		}

		function get_siteName()
		{
			$prefs = CreateObject('sitemgr.sitePreference_SO');
			return $prefs->getPreference('sitemgr-site-name');
		}

		function get_title()
		{
			return $this->page->title;
		}

		function get_subtitle()
		{
			return $this->page->subtitle;
		}

		function get_content()
		{
			return $this->page->content;
		}

		function get_footer()
		{
			return $this->headerfooter_bo->getsitefooter();
		}

		function is_user()
		{
			global $sitemgr_info,$phpgw_info;
			if ($phpgw_info['user']['account_lid'] != $sitemgr_info['login'])
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_admin()
		{
			return $this->acl->is_admin();
		}

	}
?>
