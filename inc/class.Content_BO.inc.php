<?php

require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.module.inc.php');

	class Content_BO
	{
		var $so;

		function Content_BO()
		{
			$this->so = CreateObject('sitemgr.Content_SO', true);
			$prefs_so = CreateObject('sitemgr.sitePreference_SO', True);
			$sitemgr_dir = $prefs_so->getPreference('sitemgr-site-dir');
			$themesel = $prefs_so->getPreference('themesel');
			$this->templatefile = $sitemgr_dir . SEP . 'templates' . SEP . $themesel . SEP . 'main.tpl';
		}

		function getContentAreas()
		{
			$str = implode('', @file($this->templatefile));
			preg_match_all("/\{contentarea:([^{ ]+)\}/",$str,$matches);
			return $matches[1];
		}


		function addblock($block)
		{
			$permittedmoduleids = array_keys($GLOBALS['Common_BO']->modules->getcascadingmodulepermissions($block->area,$block->cat_id));
			$module = $GLOBALS['Common_BO']->modules->getmodule($block->module_id);

			if ($GLOBALS['Common_BO']->acl->can_write_category($block->cat_id) &&
				in_array($block->module_id,$permittedmoduleids) &&
				$GLOBALS['Common_BO']->modules->createmodule($module['module_name']))
			{
				return $this->so->addblock($block);
			}
			else
			{
				return false;
			}
		}

		function removeBlocksInPageOrCat($cat_id,$page_id)
		{
			$blocks = $this->so->getblocksforscope($cat_id,$page_id);
			while(list($blockid,) = each($blocks))
			{
				$this->removeblock($blockid);
			}
		}

		function removeblock($blockid)
		{
			$block = $this->so->getblockdef($blockid);
			if ($GLOBALS['Common_BO']->acl->can_write_category($block->cat_id))
			{
				return $this->so->removeblock($blockid);
			}
			else
			{
				return false;
			}
		}

		//the next two functions retrieves all blocks for a certain area, if cat_id and page_id are 0, only site-wide blocks are retrieved.
		//if cat_id is non zero and page_id is 0, site-wide blocks and all blocks for the category and all its ancestor categories are retrieved.
		//if page_id is non zero, cat_id should be the page's category. Page blocks + category blocks + site blocks are retrieved.
		function getvisibleblockdefsforarea($area,$cat_id,$page_id)
		{
			$cat_ancestorlist = $cat_id ? $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id,True) : False;
			if ($page_id && !$GLOBALS['Common_BO']->acl->can_read_category($cat_id))
			{
			   $page_id = False;
			}
			return $this->so->getvisibleblockdefsforarea($area,$cat_ancestorlist,$page_id,$visibleonly);
		}

		function getallblocksforarea($area,$cat_id,$page_id,$lang)
		{
			$cat_ancestorlist = $cat_id ? $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id,True) : False;
			if ($page_id && !$GLOBALS['Common_BO']->acl->can_read_category($cat_id))
			{
			   $page_id = False;
			}
			return $this->so->getallblocksforarea($area,$cat_ancestorlist,$page_id,$lang);
		}

		function getblock($block_id,$lang)
		{
			//do we need ACL here, since we have ACL when getting the block lists, we could do without it here?
			return $this->so->getblock($block_id,$lang);
		}

		function getlangarrayforblock($block_id)
		{
			return $this->so->getlangarrayforblock($block_id);
		}

		//this function retrieves blocks only for a certain scope (site-wide, specific to one category or specific to one page), 
		//but for all areas.
		function getblocksforscope($cat_id,$page_id)
		{
			if ($cat_id && !$GLOBALS['Common_BO']->acl->can_read_category($cat_id))
			{
				return array();
			}
			else
			{
 				return $this->so->getblocksforscope($cat_id,$page_id);
			}
		}

		function getlangblockdata($blockid,$lang)
		{
			//TODO: add ACL
			return $this->so->getlangblockdata($blockid,$lang);
		}
		function saveblockdata($block,$data,$lang)
		{
			$oldblock = $this->so->getblockdef($block->id);
			if ($GLOBALS['Common_BO']->acl->can_write_category($oldblock->cat_id))
			{
				$this->so->saveblockdatalang($block,$data['i18n'],$lang);
				unset($data['i18n']);
				return $this->so->saveblockdata($block,$data);
			}
			else
			{
				return false;
			}
		}

		function saveblockdatalang($block,$data,$lang)
		{
			$oldblock = $this->so->getblockdef($block->id);
			if ($GLOBALS['Common_BO']->acl->can_write_category($block->cat_id))
			{
				return $this->so->saveblockdatalang($block,$data['i18n'],$lang);
			}
			else
			{
				return false;
			}
		}

		function getblockmodule($blockid)
		{
			$block = $this->so->getblockdef($blockid);
			return $GLOBALS['Common_BO']->modules->createmodule($block->module_name);
		}
	}
?>
