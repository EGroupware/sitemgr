<?php
	/***************************************************************************\
	* phpGroupWare - Web Content Manager                                        *
	* http://www.phpgroupware.org                                               *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class bo
	{
		var $pages_bo;
		var $headerfooter_bo;
		var $page_id;
		var $page;
		var $catbo;

		function bo()
		{
			$this->catbo = CreateObject('sitemgr.Categories_BO');
			$this->pages_bo = CreateObject('sitemgr.Pages_BO');
			$this->pages_so = CreateObject('sitemgr.Pages_SO');
			$this->headerfooter_bo = CreateObject('sitemgr.headerFooter_BO');
			$this->page = CreateObject('sitemgr.Page_SO');
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
			$content = "\n".'<ul>';
			$catname = '';
			foreach($indexarray as $page)
			{
				if ($catname!=$page['catname']) //category name change
				{
					if ($catname!='') //not the first name change
					{
						$content .= '</ol><br></li>';
					}
					$catname = $page['catname'];
					$content .= "\n".'<li><b>'.$catname.'</b><br><i>'.
						$page['catdescrip'].'</i>'."\n".'<ol>';
				}
				$content .= "\n".'<li>'.$page['pagelink'].'</li>';
			}
			$content .= "\n".'</ol></li></ul>';
			if (count($indexarray)==0)
			{
				$content='You do not have access to any content on this site.';
			}
			$this->page->content = $content;
		}

		function getIndex()
		{
			$cats = $this->getCatLinks();
			$index = array();

			if (count($cats)>0)
			{
				reset($cats);
				$content = "\n".'<ul>';
				while(list($cat_id,$cat) = each($cats))
				{
					$pages = $this->getPageLinks($cat_id);
					if (count($pages)>0)
					{
						foreach($pages as $link)
						{
							$index[] = array(
								'catname'=>$cat['name'],
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
							'catdescrip'=>$cat['description'],
							'pagelink'=>'No pages in this section.'
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
					$links = $this->getPageLinks($category_id);
					$cat = $this->catbo->getCategory($category_id);
					if ($cat)
					{
						$this->page->title = 'Table of Contents: '.$cat->name;
						$this->page->subtitle = '<a href="'.sitemgr_link2('/index.php','toc=1').'">Up to table of contents</a>';
						$links = $this->getPageLinks($category_id);
						$content = '<ul>';
						if (count($links)>0)
						{
							foreach($links as $pg)
							{
								$content .= "\n".'<li>'.$pg['link'].'</li>';
							}
						}
						else
						{
							$content .= '<li>There are no pages in this section</li>';
						}
						$content .= '</ul>';
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
				$content = '<ul>';
				$links = $this->getCatLinks();
				if (count($links)>0)
				{
					foreach($links as $cat)
					{
						$content .= "\n".'<li>'.$cat['link'].'<br><i>'.$cat['description'].
							'</i></li>';
					}
				}
				else
				{
					$content .= '<li>There are no sections available to you.</li>';
				}
				$content .= '</ul>';
				$this->page->content=$content;
			}
			return true;
		}
		
		function getPageLinks($category_id)
		{
			$pages=$this->pages_bo->getPageIDList($category_id);
			foreach($pages as $page_id)
			{
				$page=$this->pages_bo->getPage($page_id);
				$pglinks[$page_id] = array(
					'name'=>$page->name,
					'link'=>'<a href="'.sitemgr_link2('/index.php','page_name='.
						$page->name).'">'.$page->title.'</a>',
					'title'=>$page->title,
					'subtitle'=>$page->subtitle
				);
			}
			return $pglinks;
		}

		function getCatLinks()
		{
			$cat_list=$this->catbo->getPermittedCategoryIDReadList();
			foreach($cat_list as $cat_id)
			{
				$category = $this->catbo->getCategory($cat_id);
				$catlinks[$cat_id] = array(
					'name'=>$category->name,
					'link'=>'<a href="'.sitemgr_link2('/index.php','category_id='.$cat_id).'">'.
						$category->name.'</a>',
					'description'=>$category->description
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

	}
?>
