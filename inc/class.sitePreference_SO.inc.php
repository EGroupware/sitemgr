<?php
	class sitePreference_SO
	{
		var $db;

		function sitePreference_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function setPreference($name, $value)
		{
			$sql = 'SELECT pref_id FROM phpgw_sitemgr_preferences WHERE name=\'' . $name . '\'';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				$sql = 'UPDATE phpgw_sitemgr_preferences SET value=\'' . $value . 
					'\' WHERE pref_id=\'' . $this->db->f('pref_id') . '\'';
				$this->db->query($sql,__LINE__,__FILE__);
				return true;
			}
			else
			{
				$sql = 'INSERT INTO phpgw_sitemgr_preferences (name, value) VALUES ' .
					'(\'' . $name . '\',\'' . $value . '\')';
				$this->db->query($sql,__LINE__,__FILE__);
			}
		}

		function getPreference($name)
		{
			$sql = 'SELECT value FROM phpgw_sitemgr_preferences WHERE name=\'' . $name . '\'';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('value');
			}
			else
			{
				return '';
			}
		}

		function getallprefs()
		{
			$sql = 'SELECT name,value FROM phpgw_sitemgr_preferences';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$result[$this->db->f('name')] = $this->db->f('value');
			}
			return $result;
		}
	}

?>
