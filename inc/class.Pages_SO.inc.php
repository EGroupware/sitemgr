<?php
	class Pages_SO
	{
		var $db;

		function Pages_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		
		//if $cats is an array, pages from this list are retrieved,
		//is $cats is an int, pages from this cat are retrieved,
		//if $cats is 0 or false, pages from currentcats are retrieved
		function getPageIDList($cats=False,$states=false)
		{
			if (!$states)
			{
				$states = $GLOBALS['Common_BO']->visiblestates;
			}

			$page_id_list = array();
			$cat_list = is_array($cats) ? implode(',',$cats) :
				($cats ? $cats : 
					($GLOBALS['Common_BO']->cats->currentcats ? implode(',',$GLOBALS['Common_BO']->cats->currentcats) : false)
				);
			if ($cat_list)
			{
				$sql = "SELECT page_id FROM phpgw_sitemgr_pages WHERE cat_id IN ($cat_list) ";
				if ($states)
				{
					$sql .= 'AND state in ('. implode(',',$states)  . ')';
				}
				$sql .=' ORDER BY cat_id, sort_order ASC'; 
				$this->db->query($sql,__LINE__,__FILE__);
				while ($this->db->next_record())
				{
					$page_id_list[] = $this->db->f('page_id');
				}
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

		//this function should be a deprecated function - IMHO - skwashd
		function pageExists($page_name, $exclude_page_id='')
		{
			$page_id = $this->PagetoID($page_name);
			if($page_id)
			{
				return ($page_id != $exclude_page_id ? $page_id : False);
			}
			else
			{
				return False;
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
			$cats = CreateObject('phpgwapi.categories', -1, 'sitemgr');
			$cat_list = $cats->return_sorted_array(0, False, '', '', '', False, CURRENT_SITE_ID);
			
			if($cat_list)
			{
				foreach($cat_list as $null => $val)
				{
					$site_cats[] = $val['id'];
				}
			}
			
			$sql  = 'SELECT page_id FROM phpgw_sitemgr_pages ';
			$sql .= "WHERE name='" . $this->db->db_addslashes($page_name) . "' ";
			if($site_cats)
			{
				$sql .= 'AND cat_id IN(' . implode(',', $site_cats) . ')';
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

		function getcatidforpage($page_id)
		{
			$sql  = 'SELECT cat_id FROM phpgw_sitemgr_pages ';
			$sql .= 'WHERE page_id = ' . intval($page_id);
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
 			{
				return $this->db->f('cat_id');
			}
			else
			{
				return false;
			}
		}

		function getPage($page_id,$lang=False)
		{
			$sql  = 'SELECT * FROM phpgw_sitemgr_pages ';
			$sql .= 'WHERE page_id=' . intval($page_id);
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$page = CreateObject('sitemgr.Page_SO', True);
				$page->id = $page_id;
				$page->cat_id = $this->db->f('cat_id');
				$page->sort_order = (int) $this->db->f('sort_order');
				$page->name = stripslashes($this->db->f('name'));
				$page->hidden = $this->db->f('hide_page');
				$page->state = $this->db->f('state');
				
				if ($lang)
				{
					$sql = "SELECT * FROM phpgw_sitemgr_pages_lang WHERE page_id=$page_id AND lang='$lang'";
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
				'hide_page=\'' . $pageInfo->hidden . '\',' .
				'state=\'' . $pageInfo->state . '\' ' .
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

		function commit($page_id)
		{
			$sql = "UPDATE phpgw_sitemgr_pages SET state = " . SITEMGR_STATE_PUBLISH . " WHERE state = " . SITEMGR_STATE_PREPUBLISH . " AND page_id = $page_id";
			$this->db->query($sql, __LINE__,__FILE__);
			$sql = "UPDATE phpgw_sitemgr_pages SET state = " . SITEMGR_STATE_ARCHIVE . " WHERE state = " . SITEMGR_STATE_PREUNPUBLISH . " AND page_id = $page_id";;
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function reactivate($page_id)
		{
			$sql = "UPDATE phpgw_sitemgr_pages SET state = " . SITEMGR_STATE_DRAFT . " WHERE state = " . SITEMGR_STATE_ARCHIVE . " AND page_id = $page_id";
			$this->db->query($sql, __LINE__,__FILE__);
		}
	}
?>
