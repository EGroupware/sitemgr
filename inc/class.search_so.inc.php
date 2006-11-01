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

		function search($query,$lang="all",$mode="any")
		{
			$result = array_merge(
					$this->search_pages($query,$lang,$mode),
					$this->search_cats($query,$lang,$mode),
					$this->search_content($query,$lang,$mode)
					);
			if ($result)
			{
				return $result;
			}
			
		}
		
		function search_content($query,$lang="all",$mode="any")
		{
			$likes_content_lang = $this->likes($query,'tcontent_lang.arguments_lang',$mode);
		    $sql = "SELECT distinct(tblocks.page_id), tblocks.cat_id, tcontent_lang.arguments_lang ".
	   				"FROM ". 
						"$this->content_table AS tcontent, ". 
						"$this->content_lang_table AS tcontent_lang, ".
						"$this->blocks_table AS tblocks ".
	   				"WHERE ".				
						"(".$likes_content_lang.") AND ";
						if ($lang != "all")
						{
							$sql .= "tcontent_lang.lang = '$lang' AND ";
						}
						$sql .= "tcontent.state = 2 AND ".
						"tblocks.viewable = 0 AND ".
						"tblocks.area = 'center' AND ".
						"tcontent.version_id = tcontent_lang.version_id AND ".
						"tblocks.block_id = tcontent.block_id ";
						
			$this->db->query($sql,__LINE__,__FILE__);
									
			while (($row = $this->db->row(true)))
			{
				$result[] = array(
					'cat_id' => $row['cat_id'], 
					'page_id' => $row['page_id'], 
					'content' => $row['arguments_lang'],
				);
			}
			return $result ? $result : array();
//			if ($result)
//			{
//				return $result;
//			}			
						
		}

		function search_pages($query,$lang="all",$mode="any")
		{
			$likes_pages_lang_title = $this->likes($query,'tpages_lang.title',$mode);
			$likes_pages_lang_subtitle = $this->likes($query,'tpages_lang.subtitle',$mode);

		    $sql = "SELECT distinct(tpages.page_id), tpages.cat_id ".
	   				"FROM ". 
						"$this->pages_table AS tpages, ".
						"$this->pages_lang_table AS tpages_lang ".
	   				"WHERE ".	
						"((".$likes_pages_lang_title.") OR (".$likes_pages_lang_subtitle.")) AND ";
						if ($lang != "all")
						{
							$sql .= "tpages_lang.lang = '$lang' AND ";
						}														
						$sql .= "tpages.state = 2 AND ".
						"tpages.page_id = tpages_lang.page_id";
			
			$this->db->query($sql,__LINE__,__FILE__);
									
			while (($row = $this->db->row(true)))
			{
				$result[] = array(
					'cat_id' => $row['cat_id'], 
					'page_id' => $row['page_id'],
					'content' => null,
					);
			}
			return $result ? $result : array();
//			if ($result)
//			{
//				return $result;
//			}											
		}

		function search_cats($query,$lang="all",$mode="any")
		{
			$likes_cats_lang_name = $this->likes($query,'tcats_lang.name',$mode);
			$likes_cats_lang_description = $this->likes($query,'tcats_lang.description',$mode);
			
			$sql =  "SELECT distinct(tcats_lang.cat_id) ".
	   				"FROM ". 
						"$this->categories_lang_table AS tcats_lang, ". 
						"$this->categories_state_table AS tcats_state ".
	   				"WHERE ".
	   					"((".$likes_cats_lang_name.") OR (".$likes_cats_lang_description.")) AND ";
						if ($lang != "all")
						{
							$sql .= "tcats_lang.lang = '$lang' AND ";
						}														
						$sql .= "tcats_state.state = 2 AND ".
						"tcats_lang.cat_id = tcats_state.cat_id";	
			
			$this->db->query($sql,__LINE__,__FILE__);
									
			while (($row = $this->db->row(true)))
			{
				$result[] = array(
					'cat_id' => $row['cat_id'], 
					'page_id' => 0,
					'content' => null,
					);
			}
			return $result ? $result : array();		
		}
		
		function likes($query,$column,$mode)
		{
			if  ($mode == "exact")
			{
				$likes = "$column {$this->like} '%".$this->db->db_addslashes($query)."%'";	
			}
			else
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
					$likes[] = "$column {$this->like} '%$word%'";
				}
				if ($mode == "all")
				{	
					$likes = implode(' AND ', $likes);
				}
				elseif ($mode == "any")
				{
					$likes = implode(' OR ', $likes);
				}
			}
			return $likes;
		}
	}
?>
