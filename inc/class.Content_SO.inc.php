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
			$sql = "INSERT INTO phpgw_sitemgr_blocks (area,module_id,page_id,cat_id,sort_order,viewable) VALUES ('" .
				$block->area . "'," . $block->module_id . "," . $block->page_id . "," . $block->cat_id . ",0,0)";
			$this->db->query($sql,__LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_blocks','block_id');
		}

		function createversion($blockid)
		{
			$sql = "INSERT INTO phpgw_sitemgr_content (block_id,state) VALUES ($blockid," . SITEMGR_STATE_DRAFT  . ")";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function deleteversion($id)
		{
			$sql = "DELETE FROM phpgw_sitemgr_content WHERE version_id = $id";
			if ($this->db->query($sql,__LINE__,__FILE__))
 			{
				$sql = "DELETE FROM phpgw_sitemgr_content_lang WHERE version_id = $id";
				return $this->db->query($sql,__LINE__,__FILE__);
 			}
			else
			{
				return false;
			}
		}

		function getblockidforversion($versionid)
		{
			$sql = "SELECT block_id FROM phpgw_sitemgr_content WHERE version_id = $versionid";
			$this->db->query($sql,__LINE__,__FILE__);
			return $this->db->next_record() ? $this->db->f('block_id') : false;
		}

		function removeblock($id)
		{
			$sql = "DELETE FROM phpgw_sitemgr_blocks WHERE block_id = $id";
 			if ($this->db->query($sql,__LINE__,__FILE__))
 			{
				$sql = "DELETE FROM phpgw_sitemgr_blocks_lang WHERE block_id = $id";
				return $this->db->query($sql,__LINE__,__FILE__);
 			}
			else
			{
				return false;
			}
		}

		function getblocksforscope($cat_id,$page_id)
		{
			$sql = "SELECT t1.block_id,t1.module_id,module_name,area FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_modules AS t2 WHERE t1.module_id = t2.module_id AND cat_id = $cat_id AND page_id = $page_id ORDER by sort_order";
			$block = CreateObject('sitemgr.Block_SO',True);

			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('block_id');
				$block->id = $id;
				$block->module_id = $this->db->f('module_id');
				$block->module_name = $this->db->f('module_name');
				$block->area = $this->db->f('area');
				$result[$id] = $block;
			}
			return $result;
		}

		function getallblocksforarea($area,$cat_list,$page_id,$lang)
		{
			$sql = "SELECT t1.block_id, area, cat_id, page_id, t1.module_id, module_name, sort_order, title, viewable"
				. " FROM phpgw_sitemgr_blocks AS t1 LEFT JOIN "
				. " phpgw_sitemgr_modules AS t2 ON t1.module_id=t2.module_id LEFT JOIN "
				. " phpgw_sitemgr_blocks_lang AS t3 ON (t1.block_id=t3.block_id AND lang='$lang') "
				. " WHERE area = '$area' AND ((page_id = 0 and cat_id = ". CURRENT_SITE_ID  . ")";
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
				$block->module_name = $this->db->f('module_name');
				$block->sort_order = $this->db->f('sort_order');
				$block->title = stripslashes($this->db->f('title'));
				$block->view = $this->db->f('viewable');
				$result[$id] = $block;
			}
			return $result;
		}

		function getversionidsforblock($blockid)
		{
			$sql = "SELECT version_id FROM phpgw_sitemgr_content WHERE block_id = $blockid";
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$result[] = $this->db->f('version_id');
			}
			return $result;
		}


		function getallversionsforblock($blockid,$lang)
		{
			$sql = "SELECT t1.version_id, arguments,arguments_lang,state FROM phpgw_sitemgr_content AS t1 LEFT JOIN "
				. "phpgw_sitemgr_content_lang AS t2 ON (t1.version_id=t2.version_id AND lang = '$lang') WHERE block_id = $blockid ";
			$result = array();
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$id = $this->db->f('version_id');
 				$version['arguments'] = array_merge(
 					unserialize(stripslashes($this->db->f('arguments'))),
 					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
				$version['state'] = $this->db->f('state');
				$version['id'] = $id;
				$result[$id] = $version;
			}
			return $result;
		}

		//selects all blocks from a given cat_list + site-wide blocks that are in given states
		function getallblocks($cat_list,$states)
		{
			$sql = "SELECT COUNT(*) AS cnt,t1.block_id,area,cat_id,page_id,viewable,state FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_content as t2 WHERE t1.block_id=t2.block_id AND ((cat_id = " . CURRENT_SITE_ID  . ")";
			if ($cat_list)
			{
				$sql .= " OR (cat_id IN (" . implode(',',$cat_list) . "))";
			}
			$sql .= ") AND state IN (" . implode(',',$states) .") GROUP BY block_id";
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
//				$block->module_id = $this->db->f('module_id');
//				$block->module_name = $this->db->f('module_name');
				$block->view = $this->db->f('viewable');
				$block->state = $this->db->f('state');
				//in cnt we retrieve the numbers of versions that are commitable for a block,
				//i.e. if there are more than one, it should normally be a prepublished version 
				//that will replace a preunpublished version
				$block->cnt =  $this->db->f('cnt');
				$result[$id] = $block;
			}
			return $result;
		}

		function getvisibleblockdefsforarea($area,$cat_list,$page_id,$isadmin,$isuser)
		{
			$viewable = SITEMGR_VIEWABLE_EVERBODY  . ',';
			$viewable .= $isuser ? SITEMGR_VIEWABLE_USER : SITEMGR_VIEWABLE_ANONYMOUS;
			$viewable .= $isadmin ? (',' . SITEMGR_VIEWABLE_ADMIN) : '';

			$sql = "SELECT t1.block_id,area,cat_id,page_id,t1.module_id,module_name,state,version_id " . 
				"FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_modules AS t2,phpgw_sitemgr_content AS t3 " . 
				"WHERE t1.module_id = t2.module_id AND t1.block_id=t3.block_id AND area = '$area' " . 
				"AND  ((page_id = 0 and cat_id = ". CURRENT_SITE_ID  . ")";
			if ($cat_list)
			{
				$sql .= " OR (page_id = 0 AND cat_id IN (" . $cat_list . "))";
			}
			if ($page_id)
			{
				$sql .= " OR (page_id = $page_id) ";
			}
			$sql .= ") AND viewable IN (" . $viewable . ") AND state IN (" . implode(',',$GLOBALS['Common_BO']->visiblestates) . ") ORDER by sort_order";
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
				$block->module_name = $this->db->f('module_name');
				$block->view = $this->db->f('viewable');
				$block->state = $this->db->f('state');
				$block->version = $this->db->f('version_id');
				$result[$id] = $block;
			}
			return $result;
		}

		function getlangarrayforblocktitle($block_id)
		{
			$retval = array();
			$this->db->query("SELECT lang FROM phpgw_sitemgr_blocks_lang WHERE block_id = $block_id",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		//find out in what languages this block has data and return 
		function getlangarrayforversion($version_id)
		{
			$retval = array();
			$this->db->query("SELECT lang FROM phpgw_sitemgr_content_lang WHERE version_id = $version_id",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function getversion($version_id,$lang)
		{
			$sql = "SELECT arguments, arguments_lang FROM phpgw_sitemgr_content AS t1 LEFT JOIN phpgw_sitemgr_content_lang AS t2 ON (t1.version_id = t2.version_id AND lang='$lang') WHERE t1.version_id = $version_id";

			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->arguments = array_merge(
					unserialize(stripslashes($this->db->f('arguments'))),
					unserialize(stripslashes($this->db->f('arguments_lang')))
				);
				return $block;
			}
			else
			{
				return false;
			}
		}

		function getblock($block_id,$lang)
		{
			$sql = "SELECT area,cat_id,page_id,area,t1.module_id,module_name,sort_order,title,viewable"
				. " FROM phpgw_sitemgr_blocks AS t1 LEFT JOIN "
				. " phpgw_sitemgr_modules as t2 ON t1.module_id=t2.module_id LEFT JOIN "
				. " phpgw_sitemgr_blocks_lang AS t3 ON (t1.block_id=t3.block_id AND lang='$lang') WHERE t1.block_id = $block_id";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
 				$block->module_name = $this->db->f('module_name');
 				$block->sort_order = $this->db->f('sort_order');
 				$block->title = stripslashes($this->db->f('title'));
 				$block->view = $this->db->f('viewable');
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
			$sql = "SELECT cat_id,page_id,area,t1.module_id,module_name FROM phpgw_sitemgr_blocks AS t1,phpgw_sitemgr_modules AS t2 WHERE t1.module_id = t2.module_id AND t1.block_id = $block_id";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$block = CreateObject('sitemgr.Block_SO',True);
				$block->id = $block_id;
				$block->cat_id = $this->db->f('cat_id');
				$block->page_id = $this->db->f('page_id');
				$block->area = $this->db->f('area');
				$block->module_id = $this->db->f('module_id');
 				$block->module_name = $this->db->f('module_name');
				return $block;
			}
			else
			{
				return false;
			}
		}

		function getlangblocktitle($id,$lang)
		{
			if ($lang)
			{
				$sql = "SELECT title FROM phpgw_sitemgr_blocks_lang WHERE block_id = $id AND lang = '$lang'";
				$this->db->query($sql,__LINE__,__FILE__);
				return $this->db->next_record() ? $this->db->f('title') : false;
			}
			else
			{
				$sql = "SELECT title FROM phpgw_sitemgr_blocks_lang WHERE block_id = $id";
				$this->db->query($sql,__LINE__,__FILE__);
				return $this->db->next_record() ? $this->db->f('title') : false;
			}
		}

		function saveblockdata($block)
		{
			$sql = "UPDATE phpgw_sitemgr_blocks SET sort_order = " . (int)$block->sort_order . 
				", viewable = " . $block->view . " WHERE block_id = " . $block->id;
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function saveblockdatalang($id,$title,$lang)
		{
			$sql = "DELETE FROM phpgw_sitemgr_blocks_lang WHERE block_id = $id AND lang = '$lang'";
			$this->db->query($sql,__LINE__,__FILE__);
			$sql = "INSERT INTO phpgw_sitemgr_blocks_lang (block_id,title,lang) VALUES ($id,'$title','$lang')";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function saveversiondata($block_id,$version_id,$data)
		{
			//this is necessary because double slashed data breaks while serialized
			if (isset($data))
			{
				$this->remove_magic_quotes($data);
			}
			$s = $this->db->db_addslashes(serialize($data));
			//by requiring block_id, we make sur that we only touch versions that really belong to the block
			$sql = "UPDATE phpgw_sitemgr_content SET arguments = '$s' WHERE version_id = $version_id AND block_id = $block_id";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function saveversionstate($block_id,$version_id,$state)
		{
			$sql = "UPDATE phpgw_sitemgr_content SET state = $state  WHERE version_id = $version_id AND block_id = $block_id";
			return $this->db->query($sql,__LINE__,__FILE__);
		}

		function saveversiondatalang($id,$data,$lang)
		{
			//this is necessary because double slashed data breaks while serialized
			if (isset($data))
			{
				$this->remove_magic_quotes($data);
			}
			$s = $this->db->db_addslashes(serialize($data));
			$blockid = $block->id;
			$sql = "DELETE FROM phpgw_sitemgr_content_lang WHERE version_id = $id AND lang = '$lang'";
			$this->db->query($sql,__LINE__,__FILE__);
			$sql = "INSERT INTO phpgw_sitemgr_content_lang (version_id,lang,arguments_lang) VALUES ($id,'$lang','$s')";
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

		function commit($block_id)
		{
			$sql = "UPDATE phpgw_sitemgr_content SET state = " . SITEMGR_STATE_PUBLISH . " WHERE state = " . SITEMGR_STATE_PREPUBLISH . " AND block_id = $block_id";
			$this->db->query($sql, __LINE__,__FILE__);
			$sql = "UPDATE phpgw_sitemgr_content SET state = " . SITEMGR_STATE_ARCHIVE . " WHERE state = " . SITEMGR_STATE_PREUNPUBLISH . " AND block_id = $block_id";;
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function reactivate($block_id)
		{
				$sql = "UPDATE phpgw_sitemgr_content SET state = " . SITEMGR_STATE_DRAFT . " WHERE state = " . SITEMGR_STATE_ARCHIVE . " AND block_id = $block_id";;
			$this->db->query($sql, __LINE__,__FILE__);
		}
	}