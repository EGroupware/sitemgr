<?php
	class Pages_SO
	{
		var $db;

		function Pages_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function getPageIDList($cat_id)
		{
			$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE cat_id="' . $cat_id . '" ORDER BY sort_order';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$page_id_list[] = $this->db->f('page_id');
			}
			if (!is_array($page_id_list))
			{
				$page_id_list = array();
			}
			return $page_id_list;
		}

		function addPage($cat_id)
		{
			$sql = 'INSERT INTO phpgw_sitemgr_pages (cat_id) VALUES ("' . $cat_id . '")';
			$this->db->query($sql, __LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_pages','page_id');
		}

		function removePagesInCat($cat_id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_pages WHERE cat_id="'.$cat_id.'"';
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function removePage($page_id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_pages WHERE page_id="' . $page_id . '"';
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function pageExists($page_name,$exclude_page_id)
		{
			$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE name="' . $page_name . '"';
			if ($exclude_page_id)
			{
				$sql .= ' and page_id!="'. $exclude_page_id . '"';
			}
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('page_id');
			}
			else
			{
				return false;
			}
		}

		function getPageByName($page_name)
		{
			$sql = 'SELECT * FROM phpgw_sitemgr_pages WHERE name="' . $page_name . '"';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$page = CreateObject('sitemgr.Page_SO', True);
				$page->id = $this->db->f('page_id');
				$page->cat_id = $this->db->f('cat_id');
				$page->name = $this->db->f('name');
				$page->title= $this->db->f('title');
				$page->subtitle = $this->db->f('subtitle');
				$page->sort_order = (int) $this->db->f('sort_order');
				$page->content = $this->db->f('content');
				$page->hidden = $this->db->f('hide_page');
				return $page;
			}
			else
			{
				return false;
			}
		}

		function getPage($page_id)
		{
			$sql = 'SELECT * FROM phpgw_sitemgr_pages WHERE page_id="' . $page_id . '"';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$page = CreateObject('sitemgr.Page_SO', True);
				$page->id = $page_id;
				$page->cat_id = $this->db->f('cat_id');
				$page->sort_order = (int) $this->db->f('sort_order');
				$page->name = $this->db->f('name');
				$page->title= $this->db->f('title');
				$page->subtitle = $this->db->f('subtitle');
				$page->content = $this->db->f('content');
				$page->hidden = $this->db->f('hide_page');
				return $page;
			}
			else
			{
				return false;
			}
		}

		function savePageInfo($pageInfo)
		{
			$sql = 'UPDATE phpgw_sitemgr_pages SET ' . 
				'cat_id="' . $pageInfo->cat_id . '",' .
				'name="' . $pageInfo->name . '",' .
				'sort_order="' . (int) $pageInfo->sort_order . '",' .
				'title="' . $pageInfo->title . '",' .
				'subtitle="' . $pageInfo->subtitle . '",' .
				'content="' . $pageInfo->content . '" ' .
				'hide_page="' . $pageInfo->hidden . '" ' .
				'WHERE page_id="' . $pageInfo->id . '"';
			$this->db->query($sql, __LINE__,__FILE__);
			return true;
		}
	}
?>
