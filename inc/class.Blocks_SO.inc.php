<?php
	class Blocks_SO
	{
		var $db;
		
		function Blocks_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$this->sides = array('0' => 'l','1' => 'c','2' => 'r');
		}

		function getblockinfo($blockname)
		{
			$sql = "SELECT * FROM phpgw_sitemgr_blocks WHERE filename='$blockname'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$blockinfo = CreateObject('sitemgr.Block_SO', True);
				$blockinfo->id = $this->db->f('block_id');
				$blockinfo->actif = $this->db->f('actif');
				$blockinfo->title = stripslashes($this->db->f('title'));
				$blockinfo->description = stripslashes($this->db->f('description'));
				$blockinfo->filename = $blockname;
				$blockinfo->side = $this->db->f('side');
				$blockinfo->view = $this->db->f('view');
				$blockinfo->pos = $this->db->f('pos');
				return $blockinfo;
			}
			else
			{
				return false;
			}
		}

		function addblock($blockinfo)
		{
			$sql = "INSERT INTO phpgw_sitemgr_blocks (filename,title,description,view) VALUES ('" .
				$blockinfo->filename . "', '" . addslashes($blockinfo->title) . 
				"', '" . addslashes($blockinfo->description) .
				"', " . (int)$blockinfo->view . ")";
			$this->db->query($sql,__LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_blocks','block_id');
		}

		function saveblockinfo($blockinfo)
		{
			$sql = "UPDATE phpgw_sitemgr_blocks SET " .
				"title='" . addslashes($blockinfo->title) .
				"', actif=" . (int)$blockinfo->actif .
				", pos=" . (int)$blockinfo->pos .
				", side=" . (int)$blockinfo->side .
				", view=" . (int)$blockinfo->view .
				" WHERE block_id=" . $blockinfo->id;
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function getactiveblocks()
		{
			$sql = "SELECT title,filename,view,side FROM phpgw_sitemgr_blocks WHERE actif=1 ORDER BY pos";
			$this->db->query($sql,__LINE__,__FILE__);
			$i=0;
			while ($this->db->next_record())
			{
				$blocks[$i]['title'] = stripslashes($this->db->f('title'));
				$blocks[$i]['filename'] = $this->db->f('filename');
				$blocks[$i]['view'] = $this->db->f('view');
				$blocks[$i]['side'] = $this->sides[$this->db->f('side')];
				$i++;
			}
			return $blocks;
		}

		function getblocknames()
		{
			$sql = "SELECT filename FROM phpgw_sitemgr_blocks";
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$blocks[] = $this->db->f('filename');
			}
			return $blocks;
		}

		function deleteblock($blockname)
		{
			$sql = "DELETE FROM phpgw_sitemgr_blocks WHERE filename='$blockname'";
			return $this->db->query($sql,__LINE__,__FILE__);
		}
	}
?>
