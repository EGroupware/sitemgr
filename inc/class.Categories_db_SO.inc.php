<?php
	class Categories_db_SO
	{
		var $db;

		function Categories_db_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function getFullCategoryIDList()
		{
			$sql = 'SELECT cat_id FROM phpgw_sitemgr_categories ORDER BY sort_order';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$cat_id_list[] = $this->db->f('cat_id');
			}
			return $cat_id_list;
		}

		function addCategory($name, $description)
		{
			//Create a section for categoriy and return the newly added category id.
			$sql = 'INSERT INTO phpgw_sitemgr_categories (name, description) VALUES ("'
				. $name . '","' . $description . '")';
			$this->db->query($sql, __LINE__, __FILE__);
			return $this->db->get_last_insert_id('phpgw_sitemgr_categories','cat_id');
		}

		function removeCategory($cat_id)
		{
			$sql = 'DELETE FROM phpgw_sitemgr_categories WHERE cat_id="' . $cat_id . '"';
			$this->db->query($sql, __LINE__, __FILE__);
			return true;
		}

		function saveCategory($cat_info)
		{
			$sql = 'UPDATE phpgw_sitemgr_categories SET name="' .
				$cat_info->name . '", description="' . $cat_info->description .
				'", sort_order="'. (int) $cat_info->sort_order .
				'" WHERE cat_id="' . $cat_info->id . '"';
			$this->db->query($sql, __LINE__, __FILE__);
		}

		function getCategory($cat_id)
		{
			$sql = 'SELECT * FROM phpgw_sitemgr_categories WHERE cat_id="' . $cat_id . '"';
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$cat_info = CreateObject('sitemgr.Category_SO', True);
				$cat_info->id = $cat_id;
				$cat_info->name = $this->db->f('name');
				$cat_info->sort_order = $this->db->f('sort_order');
				$cat_info->description = $this->db->f('description');
				return $cat_info;
			}
			else
			{
				return false;
			}
		}
	
		function convert_to_phpgwapi()
		{
			/******************************************************\
			* Purpose of this func is to switch to phpgroupware    *
			* categories from the db categories.  So the           *
			* sql data will be moved to the cat stuff.             *
			*                                                      *
			* It would be nice if we could just run an UPDATE sql  *
			* query, but then you run the risk of this scenario:   *
			* old_cat_id = 5, new_cat_id = 2 --> update all pages  *
			* old_cat_id = 2, new_cat_id = 3 --> update all pages  *
			*  now all old_cat_id 5 pages are cat_id 3....         *
			\******************************************************/

			$cat_so = CreateObject('sitemgr.Categories_SO');

			// Make sure the categories table exists
			$this->db->query('SELECT cat_id FROM phpgw_sitemgr_categories WHERE 1',__LINE__,__FILE__);
			if ($this->db->num_rows==0)
			{
				// They have no categories entered... nothing to convert.
				return '';
			}

			$old_cats = $this->getFullCategoryIDList();
			$cat_conv = array();

			// Add each old category to the new category system
			// Remember the ID translation for the next step.
			foreach($old_cats as $old_cat_id)
			{
				$new_cat_id = $cat_so->addCategory('','',0);
				$rv='';
				if (!$new_cat_id)
				{
					return("ERROR!  I need to update your tables, but I can't upgrade tables until you get the latest phpgwapi/inc/class.categories.inc.php from the 0.9.14 branch of CVS!  Please get this now.");
				}
				$old_cat = $this->getCategory($old_cat_id);
				$old_cat->id = $new_cat_id;
				$cat_so->saveCategory($old_cat);
				$cat_conv[$old_cat_id] = $new_cat_id;
				$rv .= "\n<br>&nbsp;&nbsp;Old category id $old_cat_id is becoming $new_cat_id";
			}

			$update = array();

			// Make a list of page_id's and corresponding new_id's
			$sql = 'SELECT page_id, cat_id FROM phpgw_sitemgr_pages WHERE 1';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->num_rows())
			{
				while ($this->db->next_record())
				{
					$update[$this->db->f('page_id')] = $cat_conv[$this->db->f('cat_id')];
				}
			}

			// Update those page categories
			while (list($page_id,$new_cat_id) = each($update))
			{
				$sql = 'UPDATE phpgw_sitemgr_pages SET cat_id="'.$new_cat_id.
					'" WHERE page_id="'.$page_id.'"';
				$this->db->query($sql,__LINE__,__FILE__);
				$rv .= "\n<br>&nbsp;&nbsp;&nbsp;&nbsp;Updating page ".$page_id;
			}
			return $rv;
		}
	}
?>
