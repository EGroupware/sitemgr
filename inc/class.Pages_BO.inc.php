<?php
	class Pages_BO
	{
		var $pageso;

		function Pages_BO()
		{
			$this->pageso = CreateObject('sitemgr.Pages_SO',True);
		}

		function getPageOptionList()
		{
			$pagelist = $this->pageso->getPageIDList();
			$retval[]=array('value'=>0,'display'=>'[' .lang('Show Site Index') . ']');
			foreach($pagelist as $page_id)
			{
				$page = $this->pageso->getPage($page_id);
				$retval[]=array('value'=>$page_id,'display'=>$page->name);
			}
			return $retval;
		}

		function getPageIDList($cat_id=0)
		{
			if ($GLOBALS['Common_BO']->acl->can_read_category($cat_id))
			{
				return $this->pageso->getPageIDList($cat_id);	
			}
			else
			{
				return false;
			}
		}

		function addPage($cat_id)
		{
			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id))
			{
				return $this->pageso->addPage($cat_id);
			}
			else
			{
				return false;
			}
		}

		function removePagesInCat($cat_id,$force=False)
		{
			$pages = $this->pageso->getPageIDList($cat_id);
			while(list(,$page_id) = each($pages))
			{
				$this->removePage($cat_id,$page_id,$force);
			}
		}

		function removePage($cat_id, $page_id,$force=False)
		{
			if ($GLOBALS['Common_BO']->acl->can_write_category($cat_id) || $force)
			{
				$this->pageso->removePage($page_id);
				$GLOBALS['Common_BO']->content->removeBlocksInPageOrCat($cat_id,$page_id);
			}
			else
			{
				return false;
			}
		}

		function getPage($page_id,$lang=False)
		{
			$page = $this->pageso->getPage($page_id,$lang);
			if(
				$page && 
				in_array($page->cat_id,$GLOBALS['Common_BO']->cats->currentcats) &&
				$GLOBALS['Common_BO']->acl->can_read_category($page->cat_id)
			)
			{
				return $page;
			}
			else
			{
				$page = CreateObject('sitemgr.Page_SO');
				$page->name = 'Error';
				$page->title = lang('Error accessing page');
				$page->subtitle = '';
//				$page->content = lang('There was an error accessing the requested page. Either you do not have permission to view this page, or the page does not exist.');
				return $page;
			}
		}

		function getlangarrayforpage($page_id)
		{
			return $this->pageso->getlangarrayforpage($page_id);
		}

		function savePageInfo($page_Info,$lang)
		{
			if (!$GLOBALS['Common_BO']->acl->can_write_category($page_Info->cat_id))
			{
				return lang("You don't have permission to write to that category.");
			}
			$fixed_name = strtr($page_Info->name, '!@#$%^&*()-_=+	/?><,.\\\'":;|`~{}[]','                               ');
			$fixed_name = str_replace(' ', '', $fixed_name);
			if ($fixed_name != $page_Info->name)
			{
				$fixed_name = strtr($page_Info->name, '!@#$%^&*()-_=+	/?><,.\\\'":;|`~{}[]','                       ');
				$fixed_name = str_replace(' ', '', $fixed_name);
				if ($fixed_name != $page_Info->name)
				{
					$page_Info->name = $fixed_name;
					$this->pageso->savePageInfo($page_Info);
					$this->pageso->savePageLang($page_Info,$lang);
					return lang('The Name field cannot contain punctuation or spaces (field modified).');
				}
				if ($this->pageso->savePageInfo($page_Info))
				{
				  	$this->pageso->savePageLang($page_Info,$lang);
					return lang('The page was successfully saved.');
				}
				else
				{
					return lang('There was an error writing to the database.');
				}
				//MT: are the following three lines ever executed?
				$page_Info->name = $fixed_name;
				$this->pageso->savePageInfo($page_Info);
				return lang('The Name field cannot contain punctuation or spaces (field modified).');
			}
			if ($this->pageso->pageExists($page_Info->name,$page_Info->id))
			{
				$page_Info->name .= '--FIX-DUPLICATE-NAME';
				$this->pageso->savePageInfo($page_Info);
				$this->pageso->savePageLang($page_Info,$lang);
				return lang('The page name must be unique.');
			}
			if ($this->pageso->savePageInfo($page_Info))
			{
			  	$this->pageso->savePageLang($page_Info,$lang);
				return True;
			} 
			else
			{
				return lang('There was an error writing to the database.');
			}
		}

		function savePageLang($page_Info,$lang)
		  {
		    $this->pageso->savePageLang($page_Info,$lang);
		  }

		function removealllang($lang)
		{
			$this->pageso->removealllang($lang);
		}

		function migratealllang($oldlang,$newlang)
		{
			$this->pageso->migratealllang($oldlang,$newlang);
		}
	}
?>
