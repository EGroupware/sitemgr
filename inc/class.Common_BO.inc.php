<?php

	class Common_BO
	{
		var $sites,$acl,$theme,$pages,$cats,$content,$modules;
		var $state,$visiblestates;
		function Common_BO()
		{
			$this->sites = CreateObject('sitemgr.Sites_BO',True);
			$this->acl = CreateObject('sitemgr.ACL_BO',True);
			$this->theme = CreateObject('sitemgr.Theme_BO',True);
			$this->pages = CreateObject('sitemgr.Pages_BO',True);
			$this->cats = CreateObject('sitemgr.Categories_BO',True);
			$this->content = CreateObject('sitemgr.Content_BO',True);
			$this->modules = CreateObject('sitemgr.Modules_BO',True);
			$this->state = array(
				SITEMGR_STATE_DRAFT => lang('draft'),
				SITEMGR_STATE_PREPUBLISH => lang('prepublished'),
				SITEMGR_STATE_PUBLISH => lang('published'),
				SITEMGR_STATE_PREUNPUBLISH => lang('preunpublished'),
				SITEMGR_STATE_ARCHIVE => lang('archived'),
			);
		}

		function setvisiblestates($mode)
		{
			$this->visiblestates = $this->getstates($mode);
		}

		function getstates($mode)
		{
			switch ($mode)
			{
				case 'Administration' :
					return array(SITEMGR_STATE_DRAFT,SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Production' :
					return array(SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Draft' :
				case 'Edit' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH);
				case 'Commit' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Archive' :
					return array(SITEMGR_STATE_ARCHIVE);
			}
		}

		function globalize($varname)
		{
			if (is_array($varname))
			{
				foreach($varname as $var)
				{
					$GLOBALS[$var] = $_POST[$var];
				}
			}
			else
			{
				$GLOBALS[$varname] = $_POST[$varname];
			}
		}

		function getlangname($lang)
		{
			$GLOBALS['phpgw']->db->query("select lang_name from phpgw_languages where lang_id = '$lang'",__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			return $GLOBALS['phpgw']->db->f('lang_name');
		}

		function inputstateselect($default)
		{
			$returnValue = '';
			foreach($this->state as $value => $display)
			{
				$selected = ($default == $value) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option '.$selected.'value="'.$value.'">'.
					$display.'</option>'."\n";
			}
			return $returnValue;
		}

	}
?>
