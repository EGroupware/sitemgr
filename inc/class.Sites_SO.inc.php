<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Rewritten with the new db-functions by RalfBecker-AT-outdoor-training.de *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class Sites_SO
	{
		/**
		 * 
		 * @var egw_db
		 */
		var $db;
		var $sites_table = 'egw_sitemgr_sites';	// only reference to the db-prefix
		
		function Sites_SO()
		{
			$this->db = clone($GLOBALS['egw']->db);
			$this->db->set_app('sitemgr');
		}

		function list_siteids()
		{
			$this->db->select($this->sites_table,'site_id',False,__LINE__,__FILE__);

			$result = array();
			while ($this->db->next_record())
			{
				$result[] = $this->db->f('site_id');
			}
			return $result;
		}

		function getWebsites($limit,$start,$sort,$order,$query,&$total)
		{
			if ($limit)
			{
				if ($query)
				{
					$query = $this->db->quote('%'.$query.'%');
					$whereclause = "site_name LIKE $query OR site_url LIKE $query";
				}
				if (preg_match('/^[a-z_0-9]+$/i',$order) && preg_match('/^(asc|desc)*$/i',$sort))
				{
					$orderclause = "ORDER BY $order " . ($sort ? $sort : 'DESC');
				}
				else
				{
					$orderclause = 'ORDER BY site_name ASC';
				}
				$this->db->select($this->sites_table,'COUNT(*)',$whereclause,__LINE__,__FILE__);
				$total = $this->db->next_record() ? $this->db->f(0) : 0;

				$this->db->select($this->sites_table,'site_id,site_name,site_url',$whereclause,__LINE__,__FILE__,$start,$orderclause);
			}
			else
			{
				$this->db->select($this->sites_table,'site_id,site_name,site_url',False,__LINE__,__FILE__);
			}
			while ($this->db->next_record())
			{
				foreach(array('site_id', 'site_name', 'site_url') as $col)
				{
					$site[$col] = $this->db->f($col);
				}
				$result[$site['site_id']] = $site;
			}
			return $result;
		}

		function getnumberofsites()
		{
			$this->db->select($this->sites_table,'COUNT(*)',False,__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function urltoid($url)
		{
			return $this->db->select($this->sites_table,'site_id',array(
					'site_url' => $url,
				),__LINE__,__FILE__)->fetchColumn();
		}

		function read($site_id,$only_url_dir=false)
		{
			if (($ret = $this->db->select($this->sites_table,'*',array(
					'site_id' => $site_id,
				),__LINE__,__FILE__)->fetch()))
			{
				// if we run inside sitemgr, use the script dir as site-dir
				// fixes problems if sitemgr-site directory got moved
				if (isset($GLOBALS['site_id']) && file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/config.inc.php'))
				{
					$ret['site_dir'] = dirname($_SERVER['SCRIPT_FILENAME']);
				}
				elseif($ret['site_dir'] = 'sitemgr'.SEP.'sitemgr-site')
				{
					$ret['site_dir'] = EGW_SERVER_ROOT.SEP.$ret['site_dir'];
				}
			}
			return !$only_url_dir ? $ret : array(
				'site_url' => $ret['site_url'],
				'site_dir' => $ret['site_dir'],
			);
		}

		function read2($site_id)
		{
			return $this->read($site_id,true);
		}

		function add($site)
		{
			$cats = new categories(categories::GLOBAL_ACCOUNT,'sitemgr');
			$site_id =  $cats->add(array(
				'name'		=> $site['name'],
				'descr'		=> '',
				'access'	=> 'public',
				'parent'	=> 0,
				'old_parent' => 0
			));
			$this->db->insert($this->sites_table,array(
					'site_id'   => $site_id,
					'site_name' => $site['name'],
					'site_url'  => $site['url'],
					'site_dir'  => $site['dir'],
					'anonymous_user' => $site['anonuser'],
					'anonymous_passwd' => $site['anonpasswd'],
				),False,__LINE__,__FILE__);

			return $site_id;
		}

		function update($site_id,$site)
		{
			return $this->db->update($this->sites_table,array(
					'site_name' => $site['name'],
					'site_url'  => $site['url'],
					'site_dir'  => $site['dir'],
					'anonymous_user' => $site['anonuser'],
					'anonymous_passwd' => $site['anonpasswd'],
				),array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}

		function delete($site_id)
		{
			return $this->db->delete($this->sites_table,array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}

		function saveprefs($prefs,$site_id=CURRENT_SITE_ID)
		{
			return $this->db->update($this->sites_table,array(
					'themesel' => $prefs['themesel'],
					'site_languages' => $prefs['site_languages'],
					'home_page_id' => $prefs['home_page_id'],
					'upload_dir'  => $prefs['upload_dir'],
					'htaccess_rewrite' => $prefs['htaccess_rewrite'],
				),array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}
	}
