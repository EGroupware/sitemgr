<?php

	class Content_SO
	{
		var $db;

		function Content_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function addblock($block)
		{
			if (!$block->cat_id)
			{
				$block->cat_id = 0;
			}
			if (!$block->page_id)
			{
				$block->page__id = 0;
			}
			$sql = "INSERT INTO phpgw_sitemgr_content (area,module_id,page_id,cat_id,sort_order,viewable,actif) VALUES ('" .
				$block->area . "'," . $block->module_id . "," . $block->page_id . "," . $block->cat_id . ",0,0,0)";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function removeblock($blockid)
		{
			$sql = "DELETE FROM phpgw_sitemgr_content WHERE block_id = $blockid";
			if ($this->db->query($sql,__LINE__,__FILE__))
			{
				$sql = "DELETE FROM phpgw_sitemgr_content_lang WHERE block_id = $blockid";
				return $this->db->query($sql,__LINE__,__FILE__);
			}
			else
			{
				return false;
			}
		}

		function getblocksforscope($cat_id,$page_id)
		{
			$sql = "SELECT t1.block_id,t1.module_id,app_name,module_name,area FROM phpgw_sitemgr_content AS t1,phpgw_sitemgr_modules AS t2 WHERE t1.module_id = t2.module_id AND cat_id = $cat_id AND page_id = $page_id ORDER by sort_order";
			$block = CreateObject('sitemgr.Block_SO',True);
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->module_id = $this->db->f('module_id');
				$block->app_name = $this->db->f('app_name');
				$block->module_name = $this->db->f('module_name');
				$block->area = $this->db->f('area');
				$result[$id] = $block;
			}
			return $result;
		}

		function getallblocksforarea($area,$cat_list,$page_id,$lang)
		{
			$sql = "SELECT t1.block_id,area,cat_id,page_id,t1.module_id,app_name,module_name,arguments,arguments_lang,sort_order,title,viewable,actif FROM phpgw_sitemgr_content AS t1,phpgw_sitemgr_modules AS t2 LEFT JOIN phpgw_sitemgr_content_lang as t3 ON (t1.block_id=t3.block_id AND lang='$lang') WHERE t1.module_id = t2.module_id AND area = '$area' AND ((page_id = 0 and cat_id = 0)";
			if ($cat_list)
			{
				$sql .= " OR (page_id = 0 AND cat_id IN (" . implode(',',$cat_list) . "))";
			}
			if ($page_id)
			{
				$sql .= " OR (page_id = $page_id) ";
			}
			$sql .= ") ORDER by sort_order";

			$block = CreateObject('sitemgr.Block_SO',True);
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->area = $this->db->f('area');
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->module_id = $this->db->f('module_id');
				$block->app_name = $this->db->f('app_name');
				$block->module_name = $this->db->f('module_name');
				$block->arguments = array_merge(
					unserialize(stripslashes($this->db->f('arguments'))),
					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
				$block->sort_order = $this->db->f('sort_order');
				$block->title = stripslashes($this->db->f('title'));
				$block->view = $this->db->f('viewable');
				$block->actif = $this->db->f('actif');
				$result[$id] = $block;
			}
			return $result;
		}

		function getvisibleblockdefsforarea($area,$cat_list,$page_id)
		{
			$sql = "SELECT t1.block_id,area,cat_id,page_id,t1.module_id,app_name,module_name,viewable FROM phpgw_sitemgr_content AS t1,phpgw_sitemgr_modules AS t2 WHERE t1.module_id = t2.module_id AND area = '$area' AND  ((page_id = 0 and cat_id = 0)";
			if ($cat_list)
			{
				$sql .= " OR (page_id = 0 AND cat_id IN (" . implode(',',$cat_list) . "))";
			}
			if ($page_id)
			{
				$sql .= " OR (page_id = $page_id) ";
			}
			$sql .= ") AND actif = 1 ORDER by sort_order";
	
			$block = CreateObject('sitemgr.Block_SO',True);
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->area = $this->db->f('area');
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->module_id = $this->db->f('module_id');
				$block->app_name = $this->db->f('app_name');
				$block->module_name = $this->db->f('module_name');
				$block->view = $this->db->f('viewable');
				$result[$id] = $block;
			}
			return $result;
		}

		function getlangarrayforblock($block_id)
		{
			$retval = array();
			$this->db->query("SELECT lang FROM phpgw_sitemgr_content_lang WHERE block_id = $block_id",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function getlangblockdata($blockid,$lang)
		{
			$sql = "SELECT title, arguments, arguments_lang FROM phpgw_sitemgr_content AS t1 LEFT JOIN phpgw_sitemgr_content_lang AS t2 ON (t1.block_id=t2.block_id AND lang='$lang') WHERE t1.block_id = $blockid";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->arguments = array_merge(
					unserialize(stripslashes($this->db->f('arguments'))),
					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
 				$block->title = stripslashes($this->db->f('title'));
				return $block;
			}
			else
			{
				return false;
			}
		}

		function getblock($block_id,$lang)
		{
			$sql = "SELECT t1.block_id,cat_id,page_id,area,t1.module_id,app_name,module_name,arguments,arguments_lang,sort_order,title,viewable,actif FROM phpgw_sitemgr_content AS t1,phpgw_sitemgr_modules AS t2 LEFT JOIN phpgw_sitemgr_content_lang as t3 ON (t1.block_id=t3.block_id AND lang='$lang') WHERE t1.module_id = t2.module_id AND t1.block_id = $block_id";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
				$block->app_name = $this->db->f('app_name');
 				$block->module_name = $this->db->f('module_name');
 				$block->arguments = array_merge(
					unserialize(stripslashes($this->db->f('arguments'))),
					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
 				$block->sort_order = $this->db->f('sort_order');
 				$block->title = stripslashes($this->db->f('title'));
 				$block->view = $this->db->f('viewable');
 				$block->actif = $this->db->f('actif');
				return $block;
			}
			else
			{
				return false;
			}
		}

		//this function only retrieves basic info for the block
		function getblockdef($block_id)
		{
			$sql = "SELECT cat_id,page_id,area,t1.module_id,app_name,module_name FROM phpgw_sitemgr_content AS t1,phpgw_sitemgr_modules AS t2 WHERE t1.module_id = t2.module_id AND t1.block_id = $block_id";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
				$block->app_name = $this->db->f('app_name');
 				$block->module_name = $this->db->f('module_name');
				return $block;
			}
			else
			{
				return false;
			}
		}

		function saveblockdata($block,$data)
		{
			//this is necessary because double slashed data breaks while serialized
			$this->remove_magic_quotes($data);
			$s = $this->db->db_addslashes(serialize($data));
			$sql = "UPDATE phpgw_sitemgr_content SET arguments = '$s', sort_order = " . (int)$block->sort_order . 
				", viewable = " . $block->view . ", actif = " . $block->actif . " WHERE block_id = " . $block->id;
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function saveblockdatalang($block,$data,$lang)
		{
			//this is necessary because double slashed data breaks while serialized
			$this->remove_magic_quotes($data);
			$s = $this->db->db_addslashes(serialize($data));
			$title = $this->db->db_addslashes($block->title);
			$blockid = $block->id;
			$sql = "DELETE FROM phpgw_sitemgr_content_lang WHERE block_id = $blockid AND lang = '$lang'";
			$this->db->query($sql,__LINE__,__FILE__);
			$sql = "INSERT INTO phpgw_sitemgr_content_lang (block_id,lang,arguments_lang,title) VALUES ($blockid,'$lang','$s','$title')";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function remove_magic_quotes(&$data)
		{
			if (is_array($data))
			{
				reset($data);
				while (list($key,$val) = each($data))
				{
					$this->remove_magic_quotes($data[$key]);
				}
			}
			elseif (get_magic_quotes_gpc()) 
			{
				$data = stripslashes($data);
			}
		}
	}