<?php
	class Blocks_BO
	{
		var $so;
		var $preferenceso;
		var $blockdir;
		
		
		function Blocks_BO()
		{
			$this->so = CreateObject('sitemgr.Blocks_SO', True);
			$this->preferenceso = CreateObject('sitemgr.sitePreference_SO', true);
			$this->blockdir = $this->preferenceso->getPreference('sitemgr-site-dir') . SEP . 'blocks';
		}

		function getavailableblocks()
		{
			
			$d = dir($this->blockdir);
			if (!$d)
			{
				return 1;
			}

			while ($entry = $d->read()) 
			{
				if (preg_match ("/block-.*.php$/", $entry, $block)) 
				{
					$blocks_filesystem[] = $block[0];
				}
			}
			$d->close();

			if (count($blocks_filesystem) < 1)
			{
				return 2;
			}
			//delete vanished blocks from database
			$blocks_database = $this->so->getblocknames();
			if ($blocks_database)
			{
				foreach ($blocks_database as $blockname)
				{
					if (!in_array($blockname,$blocks_filesystem))
					{
						$this->so->deleteblock($blockname);
					}
				}
			}
			
			return $blocks_filesystem;
		}

		function getblockinfo($blockname)
		{
			$blockinfo = $this->so->getblockinfo($blockname);
			if ($blockinfo)
			{
				return $blockinfo;
			}
			else
			{
				$blockinfo = $this->getblockinfo_fromfile($blockname);
				$blockinfo->id = $this->so->addblock($blockinfo);
				if (!$blockinfo->id)
				{
					$blockinfo->title = lang("There was an error writing to the database");
				}
				return $blockinfo;
			}
		}

		function getblockinfo_fromfile($blockname)
		{
			$blockinfo = CreateObject('sitemgr.Block_SO', True);
			$blockinfo->filename = $blockname;
			$filename = $this->blockdir . SEP . $blockname;
			$blockfile = file($filename);
			$blockconfig = preg_grep('/\\\* blockconfig: .*\\\*/',$blockfile);
			if ($blockconfig)
			{
				foreach (array("title", "description", "view") as $config)
				{
					foreach ($blockconfig as $configline)
					{
						if (preg_match('/<'.$config.'>(.*)<\/'.$config.'>/',$configline,$value))
						{
							$blockinfo->$config = $value[1];
							continue 2;
						}
					}
					$blockinfo->$config = lang('No value for %1 found in blockfile',$config);
				}
			}
			else
			{
				$blockinfo->title = lang('No blockinfo found in blockfile');
			}
			return $blockinfo;
		}

		function saveblockinfo($blockinfo)
		{
			return $this->so->saveblockinfo($blockinfo);
		}
	}
?>
