<?php

require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.module.inc.php');

	class Modules_BO
	{
		var $so;

		function Modules_BO()
		{
			//all sitemgr BOs should be instantiated via a globalized Common_BO object,
			$this->so = CreateObject('sitemgr.Modules_SO', true);
		}

		function getmoduleid($appname,$modulename)
		{
			return $this->so->getmoduleid($appname,$modulename);
		}

		function getmodule($module_id)
		{
			return $this->so->getmodule($module_id);
		}

		function savemoduleproperties($module_id,$element,$contentarea,$cat_id)
		{
			$this->so->savemoduleproperties($module_id,$element,$contentarea,$cat_id);
		}

		function deletemoduleproperties($module_id,$contentarea,$cat_id)
		{
			$this->so->deletemoduleproperties($module_id,$contentarea,$cat_id);
		}

		function createmodule($appname,$modulename)
		{
			$class = $appname . '.module_' . $modulename;
			return CreateObject($class);
		}

		function getallmodules()
		{
			return $this->so->getallmodules();
		}
		function findmodules()
		{
			$appbo = CreateObject('admin.boapplications');
			$app_list = $appbo->get_list();
			while(list($app_name,$app) = each($app_list))
			{
				$incdir = PHPGW_SERVER_ROOT . SEP . $app_name . SEP . 'inc';
				if (is_dir($incdir))
				{
					$d = dir(PHPGW_SERVER_ROOT . SEP . $app_name . SEP . 'inc');
					while ($entry = $d->read())
					{
						if (preg_match ("/class\.module_(.*)\.inc\.php$/", $entry, $module)) 
						{
							$modulename = $module[1];
							$moduleobject = $this->createmodule($app_name,$modulename);
							if ($moduleobject)
							{
								$this->so->registermodule($app_name,$modulename,$moduleobject->description);
							}
						}
					}
					$d->close();
				}
			}
		}

		function savemodulepermissions($contentarea,$cat_id,$modules)
		{
			$this->so->savemodulepermissions($contentarea,$cat_id,$modules);
		}

		//this function looks for a configured value for the combination contentareara,cat_id
		function getpermittedmodules($contentarea,$cat_id)
		{
			return $this->so->getpermittedmodules($contentarea,$cat_id);
		}

		//this function looks for a module's configured propertiese for the combination contentareara,cat_id
		//if module_id is 0 the fourth and fith argument should provide appname and modulename
		function getmoduleproperties($module_id,$contentarea,$cat_id,$appname=False,$modulename=False)
		{
			return $this->so->getmoduleproperties($module_id,$contentarea,$cat_id,$appname,$modulename);
		}

		//this function calculates the permitted modules by asking first for a value contentarea/cat_id
		//if it does not find one, climbing up the category hierarchy until the site wide value for the same contentarea
		//and if it still does not find a value, looking for __PAGE__/cat_id, and again climbing up until the master list
		function getcascadingmodulepermissions($contentarea,$cat_id)
		{
			$cat_ancestorlist = $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id);
			$cat_ancestorlist[] = 0;

			$cat_ancestorlist_temp = $cat_ancestorlist;

			do
			{
				$cat_id = array_shift($cat_ancestorlist_temp);

				while($cat_id !== NULL)
				{
					$permitted = $this->so->getpermittedmodules($contentarea,$cat_id);
					if ($permitted)
					{
						return $permitted;
					}
					$cat_id = array_shift($cat_ancestorlist_temp);
				}
				$contentarea = ($contentarea != "__PAGE__") ? "__PAGE__" : False;
				$cat_ancestorlist_temp = $cat_ancestorlist;
			} while($contentarea);
			return array();
		}

		//this function calculates the properties by climbing up the hierarchy tree in the same way as 
		//getcascadingmodulepermissions does
		function getcascadingmoduleproperties($module_id,$contentarea,$cat_id,$appname=False,$modulename=False)
		{
			$cat_ancestorlist = $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id);
			$cat_ancestorlist[] = 0;

			$cat_ancestorlist_temp = $cat_ancestorlist;

			do
			{
				$cat_id = array_shift($cat_ancestorlist_temp);

				while($cat_id !== NULL)
				{
					$properties = $this->so->getmoduleproperties($module_id,$contentarea,$cat_id,$appname,$modulename);
					//we have to check for type identity since properties can be NULL in case of unchecked checkbox
					if ($properties !== false)
					{
						return $properties;
					}
					$cat_id = array_shift($cat_ancestorlist_temp);
				}
				$contentarea = ($contentarea != "__PAGE__") ? "__PAGE__" : False;
				$cat_ancestorlist_temp = $cat_ancestorlist;
			} while($contentarea);
		}
	}