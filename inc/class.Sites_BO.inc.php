<?php
  /**************************************************************************\
  * phpGroupWare - phpgroupware SiteMgr                                      *
  * http://www.phpgroupware.org                                              *
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
		var $acl;
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
			$this->so = CreateObject('sitemgr.Sites_SO');

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
				$GLOBALS['phpgw']->session->appsession('session_data','sitemgr_sites',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','sitemgr_sites');
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
				$sitelanguages = explode(',',$result['site_languages']);
				foreach($sitelanguages as $lang)
				{
					$langinfo = $GLOBALS['Common_BO']->cats->getCategory($id,$lang);
					$result['site_name_' . $lang] = $langinfo->name;
					$result['site_desc_' . $lang] = $langinfo->description;
				}
				return $result;
			}
			else
			{
				return False;
			}
		}

		function get_adminlist($site_id)
		{
			return $GLOBALS['Common_BO']->acl->get_adminlist($site_id);
		}

		function add($site)
		{
			$site_id = $GLOBALS['Common_BO']->cats->so->addCategory($site['name'],'',0);
			$this->so->add($site_id,$site);
			//$GLOBALS['Common_BO']->cats->saveCategoryLang($site_id, $site['name'],$site['description'],$site['savelang']);
			$GLOBALS['Common_BO']->acl->set_adminlist($site_id,$site['adminlist']);
			return $site_id;
		}

		function update($site_id,$site)
		{
			$this->so->update($site_id,$site);

			$GLOBALS['Common_BO']->acl->set_adminlist($site_id,$site['adminlist']);
		}

		function saveprefs($prefs)
		{
			$this->so->saveprefs($prefs);
			$sitelanguages = explode(',',$this->current_site['site_languages']);
			foreach ($sitelanguages as $lang)
			{
				$GLOBALS['Common_BO']->cats->saveCategoryLang(
					CURRENT_SITE_ID,
					$prefs['site_name_' . $lang],
					$prefs['site_desc_' . $lang],
					$lang
				);
			}
			$this->current_site = $this->read(CURRENT_SITE_ID);
		}

		function delete($id)
		{
			//TODO: add ACL!!!!!
 			$this->so->delete($id);
 			$GLOBALS['Common_BO']->cats->removeCategory($id,True,True);
			return True;
		}

		function urltoid($url)
		{
			return $this->so->urltoid($url);
		}


		function set_currentsite($site_url)
		{
			if ($site_url)
			{
				$this->current_site = $this->read($this->urltoid($site_url));
			}
			else
			{
				$GLOBALS['phpgw']->preferences->read_repository();
				if ($_POST['siteswitch'])
				{
					$this->current_site = $this->read($_POST['siteswitch']);
					$GLOBALS['phpgw']->preferences->change('sitemgr','currentsite',$_POST['siteswitch']);
					$GLOBALS['phpgw']->preferences->save_repository(True);
				}
				else
				{
					$currentsite = $GLOBALS['phpgw_info']['user']['preferences']['sitemgr']['currentsite'];
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
					$GLOBALS['phpgw']->preferences->change('sitemgr','currentsite',$allsites[0]);
					$GLOBALS['phpgw']->preferences->save_repository(True);
				}
				else
				{
					return False;
				}
			}
			define('CURRENT_SITE_ID',$this->current_site['site_id']);
			return True;
		}

		//this function is here so that we can retrieve basic info from sitemgr-link without creating COMMON_BO
		function get_currentsiteinfo()
		{
			$GLOBALS['phpgw']->preferences->read_repository();
			$currentsite = $GLOBALS['phpgw_info']['user']['preferences']['sitemgr']['currentsite'];
			if($currentsite)
			{
				$info = $this->so->read2($currentsite);
			}
			if (!$info)
			{
				$allsites = $this->so->list_siteids();
				$info = $this->so->read2($allsites[0]);
				$GLOBALS['phpgw']->preferences->change('sitemgr','currentsite',$allsites[0]);
				$GLOBALS['phpgw']->preferences->save_repository(True);
			}
			return $info;
		}
	}