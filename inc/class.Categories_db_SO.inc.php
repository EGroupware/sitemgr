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
	}
?>
