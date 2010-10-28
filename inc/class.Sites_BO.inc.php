<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class Sites_BO
	{

		var $xml_functions  = array();
		var $soap_functions = array();

		var $debug = False;

		var $so    = '';
		var $start = 0;
		var $query = '';
		var $sort  = '';
		var $order = '';
		var $total = 0;

		var $current_site;
		var $number_of_sites;

		var $use_session = False;

		function Sites_BO($session=False)
		{
			//Web site definitions are stored as top level categories
			$this->so =& CreateObject('sitemgr.Sites_SO');

			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			foreach(array('start','query','sort','order') as $var)
			{
				if (isset($_POST[$var]))
				{
					$this->$var = $_POST[$var];
				}
				elseif (isset($_GET[$var]))
				{
					$this->$var = $_GET[$var];
				}
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				if($this->debug) { echo '<br>Save:'; _debug_array($data); }
				$GLOBALS['egw']->session->appsession('session_data','sitemgr_sites',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['egw']->session->appsession('session_data','sitemgr_sites');
			if($this->debug) { echo '<br>Read:'; _debug_array($data); }

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
		}

		function list_sites($limit=True)
		{
			return $this->so->getWebsites($limit,$this->start,$this->sort,$this->order,$this->query,$this->total);
		}

		function getnumberofsites()
		{
			return $this->so->getnumberofsites();
		}

		function read($id)
		{
			$result = $this->so->read($id);
			if ($result)
			{
				$result['sitelanguages'] = $result['site_languages'] ? explode(',',$result['site_languages']) : array('en');;
				foreach($result['sitelanguages'] as $lang)
				{
					if (!is_object($GLOBALS['Common_BO']))
					{
						$GLOBALS['Common_BO'] = CreateObject("sitemgr.Common_BO");
					}
					$langinfo = $GLOBALS['Common_BO']->cats->getCategory($id,$lang,True);
					$result['site_name_' . $lang] = $langinfo->name;
					$result['site_desc_' . $lang] = $langinfo->description;
				}
				$result['default_theme'] = $result['themesel'];	// set the new name
				$result['app'] = 'sitemgr'; // we need this for ImageUpload in htmlarea
				$result['admin_method'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Common_UI.DisplayPrefs');
				$GLOBALS['phpgw']->session->appsession('UploadImage','phpgwapi',$result);
				return $result;
			}
			else
			{
				return False;
			}
		}

		function get_adminlist($site_id)
		{
			return $GLOBALS['Common_BO']->acl->get_permission_list($site_id);
		}

		function add($site)
		{
			$site_id = $this->so->add($site);
			//$GLOBALS['Common_BO']->cats->saveCategoryLang($site_id, $site['name'],$site['description'],$site['savelang']);
			$GLOBALS['Common_BO']->acl->set_adminlist($site_id,$site['adminlist']);
			return $site_id;
		}

		function update($site_id,$site)
		{
			$this->so->update($site_id,$site);

			$GLOBALS['Common_BO']->acl->set_adminlist($site_id,$site['adminlist']);
		}

		function saveprefs($prefs,$site_id=CURRENT_SITE_ID)
		{
			if (isset($prefs['default_theme']))
			{
				$prefs['themesel'] = $prefs['default_theme'];	// use the new name
			}
			$site_languages = $prefs['site_languages'] ? $prefs['site_languages'] : $this->current_site['site_languages'];
			// remove extra space in the language list
			$site_languages = $site_languages ? preg_split('/ ?, ?/',trim($site_languages)) : array('en');
			$prefs['site_languages'] = implode(',',$site_languages);
			$this->so->saveprefs($prefs,$site_id);
			foreach ($site_languages as $lang)
			{
				$GLOBALS['Common_BO']->cats->saveCategoryLang(
					$site_id,
					$prefs['site_name_' . $lang],
					$prefs['site_desc_' . $lang],
					$lang
				);
			}
			$this->current_site = $this->read($site_id);
		}

		function delete($id)
		{
			if (!$GLOBALS['egw']->acl->check('run',1,'admin'))
			{
				return False;
			}
 			$GLOBALS['Common_BO']->cats->removeCategory($id,True,True);
 			$this->so->delete($id);
			return True;
		}

		function urltoid($url)
		{
			$site_id = $this->so->urltoid($url);

			if ($site_id === False)	// nothing found, try only the path
			{
				$parts = parse_url($url);

				$site_id = $this->so->urltoid($parts['path']);
			}
			return $site_id;
		}


		/**
		* Set the site that will be worked with during this request.
		* It cannot be changed during this request after this.
		*
		* @param site_url Site ID or url
		* @param mode One of Administration, ...?
		*/
		function set_currentsite($site_url,$mode)
		{
			if ($site_url)
			{
				if(!is_numeric($site_url)) {
					$site_url = $this->urltoid($site_url);
				}
				$this->current_site = $this->read($site_url);
			}
			else
			{
				$GLOBALS['egw']->preferences->read_repository();
				$siteswitch = get_var('siteswitch');
				if ($siteswitch)
				{
					$this->current_site = $this->read($siteswitch);
					$GLOBALS['egw']->preferences->change('sitemgr','currentsite',$siteswitch);
					$GLOBALS['egw']->preferences->save_repository(True);
				}
				else
				{
					$currentsite = $GLOBALS['egw_info']['user']['preferences']['sitemgr']['currentsite'];
					if($currentsite)
					{
						$this->current_site = $this->read($currentsite);
					}
				}
			}
			if (!$this->current_site)
			{
				$allsites = $this->so->list_siteids();
				if ($allsites)
				{
					$this->current_site = $this->read($allsites[0]);
					$GLOBALS['egw']->preferences->change('sitemgr','currentsite',$allsites[0]);
					$GLOBALS['egw']->preferences->save_repository(True);
				}
				else
				{
					return False;
				}
			}
			// overwrite selected theme by user
			if (isset($_GET['themesel']) && ($theme_info = $GLOBALS['Common_BO']->theme->getThemeInfos($_GET['themesel'])))
			{
				$GLOBALS['egw']->session->appsession('themesel','sitemgr-site',$theme_info['value']);
				$this->current_site['themesel'] = $theme_info['value'];
			}
			elseif ($theme = $GLOBALS['egw']->session->appsession('themesel','sitemgr-site'))
			{
				$this->current_site['themesel'] = $theme;
			}
			define('CURRENT_SITE_ID',$this->current_site['site_id']);
			$this->setmode($mode);
			return True;
		}

		function setmode($mode)
		{
			$this->current_site['mode'] = $mode;
			$GLOBALS['Common_BO']->setvisiblestates($mode);
			$GLOBALS['Common_BO']->cats->setcurrentcats();
		}

		//this function is here so that we can retrieve basic info from sitemgr-link without creating COMMON_BO
		function get_currentsiteinfo()
		{
			$GLOBALS['egw']->preferences->read_repository();
			$currentsite = $GLOBALS['egw_info']['user']['preferences']['sitemgr']['currentsite'];
			if($currentsite)
			{
				$info = $this->so->read2($currentsite);
			}
			if (!$info)
			{
				$allsites = $this->so->list_siteids();
				$info = $this->so->read2($allsites[0]);
				$GLOBALS['egw']->preferences->change('sitemgr','currentsite',$allsites[0]);
				$GLOBALS['egw']->preferences->save_repository(True);
			}
			return $info;
		}
	}
