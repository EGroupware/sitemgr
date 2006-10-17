<?php
	/**
	* sitemgr - search storage object
	*
	* @link http://www.egroupware.org
	* @author Jose Luis Gordo Romero <jgordor@gmail.com>
	* @package sitemgr
	* @copyright Jose Luis Gordo Romero <jgordor@gmail.com>
	* @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
	* @version $Id$
	*/
	
	class search_so
	{
		var $db;
		var $content_table,$content_lang_table,$blocks_table;
		var $categories_lang_table,$categories_state_table,$pages_table,$pages_lang_table;
		var $like;
		
		function search_so()
		{
			$this->db = clone($GLOBALS['egw']->db);
			$this->db->set_app('sitemgr');
			foreach(array('content','content_lang','blocks','categories_lang','categories_state','pages','pages_lang') as $name)
			{
				$var = $name.'_table';
				$this->$var = 'egw_sitemgr_'.$name;	// only reference to the db-prefix
			}
			
            // postgresql is case sensite by default, so make it case insensitive
            if ($this->db->Type == 'pgsql')
            {
                $this->like = 'ILIKE';
            }
            else
            {
                $this->like = 'LIKE';
            }
		}
		
		function search($query)
		{
				
			$likes_content_lang = $this->likes($query,'tcontent_lang.arguments_lang');
			$likes_pages_lang_title = $this->likes($query,'tpages_lang.title');
			$likes_pages_lang_subtitle = $this->likes($query,'tpages_lang.subtitle');
			$likes_categories_lang_name = $this->likes($query,'tcat_lang.name');
			$likes_categories_lang_description = $this->likes($query,'tcat_lang.description');

		    $sql = "SELECT distinct(tblocks.page_id), tblocks.cat_id ".
	   				"FROM ". 
						"$this->content_table AS tcontent, ". 
						"$this->content_lang_table AS tcontent_lang, ".
						"$this->blocks_table AS tblocks, ".
						"$this->pages_table AS tpages, ".
						"$this->pages_lang_table AS tpages_lang ".
//						"$this->categories_lang_table AS tcat_lang, ".
//						"$this->categories_state_table AS tcat_state ".
	   				"WHERE ".				
						"(".
							"(".$likes_content_lang.") AND ".
							"tcontent.state = 2 AND ".
							"tblocks.viewable = 0 AND ".
							"tblocks.area = 'center' AND ".
							"tcontent.version_id = tcontent_lang.version_id AND ".
							"tblocks.block_id = tcontent.block_id ".
						") OR (".		
							"((".$likes_pages_lang_title.") OR (".$likes_pages_lang_subtitle.")) AND ".									
							"tpages.state = 2 AND ".
							"tpages.page_id = tpages_lang.page_id AND ".
							"tblocks.page_id = tpages.page_id AND ".
							"tblocks.cat_id = tpages.cat_id ".
						")";// OR (".
//							"((".$likes_categories_lang_name.") OR (".$likes_categories_lang_description.")) AND ".
//							"tcat_state.state = 2 AND ".					
//							"tcat_lang.cat_id = tcat_state.cat_id AND ".
//							"tblocks.cat_id = tcat_lang.cat_id AND ".
//							"tblocks.page_id = 0 ".
//						")";
// In order to check cats name/descriptions another query is needed, and merge array results
// If you 
			$this->db->query($sql,__LINE__,__FILE__);
			
			while (($row = $this->db->row(true)))
			{
				$result[] = array('cat_id' => $row['cat_id'], 'page_id' => $row['page_id']);
			}
			if ($result)
			{
				return $result;
			}
			
		}		
		
		function likes($query,$column)
		{
				$words_init = explode(' ', $query);
				$words = array();
				foreach ($words_init as $word_init)
				{
					$words[] = $this->db->db_addslashes($word_init);
				}
				$likes = array();
				foreach ($words as $word)
				{
					if ((int)$word) 
					{
						break;
					}
					$likes[] = "$column {$this->like} '%$word%'";
				}
				$likes = implode(' AND ', $likes);
				
				return $likes;
		}	
	}
?>
