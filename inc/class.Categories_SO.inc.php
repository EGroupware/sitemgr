<?php
	class Categories_SO
	{
		var $cats;
		var $db;
		var $site_id;
		
		function Categories_SO()
		{
			$this->cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
			$this->db = $GLOBALS['phpgw']->db;			 
		}

		function getChildrenIDList($parent)
		{
			$cats = $this->cats->return_array('all','',False,'','','',False,$parent);

			while (list(,$subs) = @each($cats))
			{
				if ($subs['parent']==$parent)
				{
					$subs_id_list[] = $subs['id'];
				}
			}
			return $subs_id_list;
		}

//this does not seem to be called anywhere
// 		function getFullCategoryIDList()
// 		{
// 			$cats = $this->cats->return_array('all','',False,'','','',False);

// 			while (list(,$cat) = @each($cats))
// 			{
// 				$cat_id_list[] = $cat['id'];
// 			}
// 			return $cat_id_list;
// 		}

		function addCategory($name, $description, $parent = False)
		{
			$data = array
			(
				'name'		=> $name,
				'descr'		=> $description,
				'access'	=> 'public',
				'parent'	=> $parent,
				'old_parent' => $parent
			);

			return $this->cats->add($data);
		}

		function removeCategory($cat_id)
		{
			$this->cats->delete($cat_id,False,True);
			$sql = "DELETE FROM phpgw_sitemgr_categories_lang WHERE cat_id = $cat_id";
			$this->db->query($sql, __LINE__,__FILE__);
			return True;
		}

		function saveCategory($cat_info)
		{
			$data = array
			(
				'name'		=> $cat_info->name,
				'descr'		=> $cat_info->description,
				'data'		=> (int) $cat_info->sort_order,
				'access'	=> 'public',
				'id'		=> $cat_info->id,
				'parent'	=> $cat_info->parent,
				'old_parent' => $cat_info->old_parent
			);

			$this->cats->edit($data);
		}

		function saveCategoryLang($cat_id, $cat_name, $cat_description, $lang)
		{
			$this->db->query("SELECT * FROM phpgw_sitemgr_categories_lang WHERE cat_id='$cat_id' and lang='$lang'", __LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$this->db->query("UPDATE phpgw_sitemgr_categories_lang SET name='$cat_name', description='$cat_description' WHERE cat_id='$cat_id' and lang='$lang'", __LINE__,__FILE__);
			}
			else
			{
				$this->db->query("INSERT INTO phpgw_sitemgr_categories_lang (cat_id,lang,name,description) VALUES ('$cat_id','$lang','$cat_name','$cat_description')", __LINE__,__FILE__);
			}
		}

		function getlangarrayforcategory($cat_id)
		{
			$retval = array();
			$this->db->query("SELECT lang FROM phpgw_sitemgr_categories_lang WHERE cat_id='$cat_id'");
			while ($this->db->next_record())
			{
				$retval[] = $this->db->f('lang');
			}
			return $retval;
		}

		function getCategory($cat_id,$lang=False)
		{
			$cat = $this->cats->return_single($cat_id);

			if (is_array($cat))
			{
				$cat_info				= CreateObject('sitemgr.Category_SO', True);
				$cat_info->id			= $cat[0]['id'];
				//$cat_info->name			= stripslashes($cat[0]['name']);
				$cat_info->sort_order	= $cat[0]['data'];
				//$cat_info->description	= stripslashes($cat[0]['description']);
				$cat_info->parent		= $cat[0]['parent'];
				$cat_info->depth		= $cat[0]['level'];
				$cat_info->root			= $cat[0]['main'];
				
				if ($lang)
				{
					$this->db->query("SELECT * FROM phpgw_sitemgr_categories_lang WHERE cat_id='$cat_id' and lang='$lang'");
					if ($this->db->next_record())
					{
						$cat_info->name = $this->db->f('name');
						$cat_info->description = $this->db->f('description');
					}
					else
					{
						//return False;
						$cat_info->name	= lang("not yet translated");
					}
				}

				//if there is no lang argument we return the content in whatever languages turns up first
				else
				{
					$this->db->query("SELECT * FROM phpgw_sitemgr_categories_lang WHERE cat_id='$cat_id'");
					if ($this->db->next_record())
					{
						$cat_info->name	= $this->db->f('name');
						$cat_info->description = $this->db->f('description');
						$cat_info->lang = $this->db->f('lang');
					}
					else
					{
						$cat_info->name = "This category has no data in any langugage: this should not happen";
					}
				}
				
				return $cat_info;
			}
			else
			{
				return false;
			}
		}

		function removealllang($lang)
		{
			$sql = "DELETE FROM phpgw_sitemgr_categories_lang WHERE lang='$lang'";
			$this->db->query($sql, __LINE__,__FILE__);
		}

		function migratealllang($oldlang,$newlang)
		{
			$sql = "UPDATE phpgw_sitemgr_categories_lang SET lang='$newlang' WHERE lang='$oldlang'";
			$this->db->query($sql, __LINE__,__FILE__);
		}
	}
?>
