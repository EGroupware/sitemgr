<?php
	class Categories_SO
	{
		var $cats;

		function Categories_SO()
		{
			$this->cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
		}

		function getChildrenIDList($parent=0)
		{
			if ($parent)
			{
				$cats = $this->cats->return_array('all','',False,'','','cat_data',False,$parent);
			}
			else
			{
				$cats = $this->cats->return_array('mains','',False,'','','cat_data',False, 0);
			}
			while (is_array($cats) && list(,$subs) = each($cats))
			{
				if ($subs['parent']==$parent)
				{
					$subs_id_list[] = $subs['id'];
				}
			}
			return $subs_id_list;
		}

		function getFullChildrenIDList($parent = '')
		{
			if (!$parent)
			{
				$parent = 0;
			}

			if ($parent == 0)
			{
				$cats = $this->cats->return_array('mains','',False,'','','cat_data',False, $parent);
			}
			else
			{
				$cats = $this->cats->return_array('all','',False,'','','cat_data',False,$parent);
			}

			while (is_array($cats) && list(,$subs) = each($cats))
			{
				$subs_id_list[] = $subs['id'];
			}
			return $subs_id_list;
		}

		function getFullCategoryIDList()
		{
			$cats = $this->cats->return_array('all','',False,'','','cat_data',False);

			while (is_array($cats) && list(,$cat) = each($cats))
			{
				$cat_id_list[] = $cat['id'];
			}
			return $cat_id_list;
		}

		function addCategory($name, $description, $parent = '')
		{
			$data = array
			(
				'name'		=> $name,
				'descr'		=> $description,
				'access'	=> 'public',
				'parent'	=> $parent
			);

			return $this->cats->add($data);
		}

		function removeCategory($cat_id)
		{
			$this->cats->delete($cat_id,False,True);
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
				'parent'	=> $cat_info->parent
			);

			$this->cats->edit($data);
		}

		function getCategory($cat_id)
		{
			$cat = $this->cats->return_single($cat_id);

			if (is_array($cat))
			{
				$cat_info				= CreateObject('sitemgr.Category_SO', True);
				$cat_info->id			= $cat[0]['id'];
				$cat_info->name			= $cat[0]['name'];
				$cat_info->sort_order	= $cat[0]['data'];
				$cat_info->description	= $cat[0]['description'];
				$cat_info->parent		= $cat[0]['parent'];
				$cat_info->depth		= $cat[0]['level'];
				$cat_info->root			= $cat[0]['main'];
				return $cat_info;
			}
			else
			{
				return false;
			}
		}
	}
?>
