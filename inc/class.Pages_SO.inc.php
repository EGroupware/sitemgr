<?php
	class Pages_SO
	{
		var $db;

		function Pages_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function getPageIDList($cat_id=False)
		{
			$page_id_list = array();
			if (!$cat_id)
			{
				$cat_list = $GLOBALS['Common_BO']->cats->allcatidsforsite;
				if ($cat_list)
				{
					$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE cat_id IN (' . 
						implode(',',$cat_list) .  
						')ORDER BY cat_id, sort_order ASC';
				}
				else
				{
					return $page_id_list;
				}
			}
			else
			{
				$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE cat_id=\'' . $cat_id . '\' ORDER BY sort_order';
			}
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$page_id_list[] = $this->db->f('page_id');
			}
			return $page_id_list;
		}

		function addPage($cat_id)
		{
			$sql = 'INSERT INTO phpgw_sitemgr_pages (cat_id) VALUES (\'' . $cat_id . '\')';
			$this->db->query($sql, __LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_pages','page_id');
		}

		function removePage($page_id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_pages WHERE page_id=\'' . $page_id . '\'';
			$this->db->query($sql, __LINE__,__FILE__);
			$sql = 'DELETE FROM phpgw_sitemgr_pages_lang WHERE page_id=\'' . $page_id . '\'';
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function pageExists($page_name,$exclude_page_id)
		{
			$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE name=\'' . $page_name . '\'';
			if ($exclude_page_id)
			{
				$sql .= ' and page_id!=\''. $exclude_page_id . '\'';
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


		function getlangarrayforpage($page_id)
		{
			$retval = array();
			$this->db->query("SELECT lang FROM phpgw_sitemgr_pages_lang WHERE page_id='$page_id'");
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function PagetoID($page_name)
		{
			$sql = 'SELECT page_id FROM phpgw_sitemgr_pages WHERE name=\'' . $page_name . '\'';
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

		function getPage($page_id,$lang=False)
		{
			$sql = 'SELECT * FROM phpgw_sitemgr_pages WHERE page_id=\'' . $page_id . '\'';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$page = CreateObject('sitemgr.Page_SO', True);
				$page->id = $page_id;
				$page->cat_id = $this->db->f('cat_id');
				$page->sort_order = (int) $this->db->f('sort_order');
				$page->name = stripslashes($this->db->f('name'));
				$page->hidden = $this->db->f('hide_page');
				
				if ($lang)
				{
					$sql = "SELECT * FROM phpgw_sitemgr_pages_lang WHERE page_id='$page_id' and lang='$lang'";
					$this->db->query($sql,__LINE__,__FILE__);
				
					if ($this->db->next_record())
					{
						$page->title= stripslashes($this->db->f('title'));
						$page->subtitle = stripslashes($this->db->f('subtitle'));
						$page->lang = $lang;
					}
					else
					{
						$page->title = lang("not yet translated");
					}
				}
				
				//if there is no lang argument we return the content in whatever languages turns up first 
				else
				{
					$sql = "SELECT * FROM phpgw_sitemgr_pages_lang WHERE page_id='" . $page->id . "'";
					$this->db->query($sql,__LINE__,__FILE__);
				
					if ($this->db->next_record())
					{
						$page->title= stripslashes($this->db->f('title'));
						$page->subtitle = stripslashes($this->db->f('subtitle'));
						$page->lang = $this->db->f('lang');
					}
					else
					{
						$page->title = "This page has no data in any langugage: this should not happen";
					}
				}

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
				'cat_id=\'' . $pageInfo->cat_id . '\',' .
				'name=\'' . $this->db->db_addslashes($pageInfo->name) . '\',' .
				'sort_order=\'' . (int) $pageInfo->sort_order . '\',' .
				'hide_page=\'' . $pageInfo->hidden . '\' ' .
				'WHERE page_id=\'' . $pageInfo->id . '\'';
			$this->db->query($sql, __LINE__,__FILE__);
			return true;
		}
		
		function savePageLang($pageInfo,$lang)
		{
			$page_id = $pageInfo->id;
			$this->db->query("SELECT * FROM phpgw_sitemgr_pages_lang WHERE page_id='$page_id' and lang='$lang'", __LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$sql = "UPDATE phpgw_sitemgr_pages_lang SET " . 
					"title='" . $this->db->db_addslashes($pageInfo->title) . "'," .
					"subtitle='" . $this->db->db_addslashes($pageInfo->subtitle) . "' WHERE page_id='$page_id' and lang='$lang'";
				$this->db->query($sql, __LINE__,__FILE__);
				return true;
			}
			else
			{
				$sql = "INSERT INTO phpgw_sitemgr_pages_lang (page_id,lang,title,subtitle) VALUES ('$page_id','$lang','" .
					$this->db->db_addslashes($pageInfo->title) . "','" .
					$this->db->db_addslashes($pageInfo->subtitle) . "')";
				$this->db->query($sql, __LINE__,__FILE__);
				return true;
			}
		}

		function removealllang($lang)
		{
			$sql = "DELETE FROM phpgw_sitemgr_pages_lang WHERE lang='$lang'";
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function migratealllang($oldlang,$newlang)
		{
			$sql = "UPDATE phpgw_sitemgr_pages_lang SET lang='$newlang' WHERE lang='$oldlang'";
			$this->db->query($sql, __LINE__,__FILE__);
		}
	}
?>
