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

	// Note: all data to this class is run through addslashes or intval -- RalfBecker 2004/03/09
	class Sites_SO
	{
		var $db;
		
		function Sites_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
			if (!is_array($GLOBALS['Common_BO']->table_definitions))
			{
				$GLOBALS['Common_BO']->table_definitons = $this->db->get_table_definitions('sitemgr');
			}
			$this->table = 'phpgw_sitemgr_sites';
			$this->db->set_column_definitions($GLOBALS['Common_BO']->table_definitions[$this->table]['fd']);
		}

		function list_siteids()
		{
			$result = array();
			$sql = "SELECT site_id FROM $this->table";
			$this->db->query($sql,__LINE__,__FILE__);
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
				if (!$sort)
				{
					$sort = 'DESC';
				}
				if ($query)
				{
					$query = $this->db->db_addslashes($query);
					$whereclause = "WHERE site_name LIKE '%$query%'"
						. "OR site_url LIKE '%$query%'"
						. "OR site_dir LIKE '%$query%'";
				}
				if ($order)
				{
					$orderclause = 'ORDER BY ' . $order . ' ' . $sort;
				}
				else
				{
					$orderclause = 'ORDER BY site_name ASC';
				}
				$sql = "SELECT site_id,site_name,site_url from $this->table $whereclause $orderclause";
				$this->db->query($sql,__LINE__,__FILE__);
				$total = $this->db->num_rows();
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$sql = "SELECT site_id,site_name,site_url from $this->table";
				$this->db->query($sql,__LINE__,__FILE__);
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
			$sql = "SELECT COUNT(*) FROM $this->table";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f(0);
		}

		function urltoid($url)
		{
			$sql  = "SELECT site_id FROM $this->table ";
			$sql .= "WHERE site_url ='" . $this->db->db_addslashes($url) . "'";
			$this->db->query($sql,__LINE__,__FILE__);
			return $this->db->next_record() ? $this->db->f('site_id') : False;
		}

		function read($id)
		{
			$sql =  "SELECT * FROM $this->table ";
			$sql .= 'WHERE site_id = ' . (int)$id;
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_id', 'site_name', 'site_url', 'site_dir', 'themesel', 
						'site_languages', 'home_page_id', 'anonymous_user','anonymous_passwd'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			else
			{
				return false;
			}
		}

		function read2($id)
		{
			$sql  = "SELECT site_url,site_dir FROM $this->table ";
			$sql .= 'WHERE site_id = ' . (int)$id;
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_url', 'site_dir'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			else
			{
				return false;
			}
		}

		function add($site)
		{
			$cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
			$site_id =  $cats->add(array(
				'name'		=> $site['name'],
				'descr'		=> '',
				'access'	=> 'public',
				'parent'	=> 0,
				'old_parent' => 0
			));
			$data = array(
				'site_id'   => $site_id,
				'site_name' => $site['name'],
				'site_url'  => $site['url'],
				'site_dir'  => $site['dir'],
				'anonymous_user' => $site['anonuser'],
				'anonymous_passwd' => $site['anonpasswd'],
			);
			$this->db->query($sql="INSERT INTO $this->table (".implode(',',array_keys($data)).") VALUES (".
				$this->db->column_data_implode(',',$data,False).')',__LINE__,__FILE__);

			return $site_id;
		}

		function update($id,$site)
		{
			$this->db->query($sql="UPDATE $this->table SET ".
				$this->db->column_data_implode(',',array(
					'site_name' => $site['name'],
					'site_url'  => $site['url'],
					'site_dir'  => $site['dir'],
					'anonymous_user' => $site['anonuser'],
					'anonymous_passwd' => $site['anonpasswd'],
				))." WHERE site_id=".(int)$id,__LINE__,__FILE__);
		}

		function delete($id)
		{
			$sql = "DELETE FROM $this->table WHERE site_id=".(int)$id;
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function saveprefs($prefs,$site_id=CURRENT_SITE_ID)
		{
			$this->db->query($sql="UPDATE $this->table SET ".
				$this->db->column_data_implode(',',array(
					'themesel' => $prefs['themesel'],
					'site_languages' => $prefs['site_languages'],
					'home_page_id' => $prefs['home_page_id'],
				))." WHERE site_id=".(int)$site_id,__LINE__,__FILE__);
		}
	}
