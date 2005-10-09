<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Copyright (C) 2004 RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class module_translation_status extends Module
	{
		function module_translation_status()
		{
			$this->arguments = array(
				'colors' => array(
					'type' => 'textfield',
					'label' => 'Colors to use from which percentage on (eg. "green: 80, yellow: 40, red")',
					'default' => 'green: 80, yellow: 40, red',
					'params' => array('size' => 50),
				),
			);
			$this->get = array('details');
			$this->properties = array();
			$this->title = lang('Translation Status');
			$this->description = lang('This module show the status / percentage of the translation of eGW');

			if (!is_object($GLOBALS['egw']->html))
			{
				$GLOBALS['egw']->html =& CreateObject('phpgwapi.html');
			}
			$this->html = &$GLOBALS['egw']->html;
			$this->db = $GLOBALS['egw']->db;
		}

		function try_lang($message_id,$args='')
		{
			return $GLOBALS['egw']->translation->translate($message_id,$args,'');
		}
	
		function get_content(&$arguments,$properties)
		{
			$details = $arguments['details'];

			$colors = array();
			foreach(split(', ?',$arguments['colors']) as $value)
			{
				list($color,$minimum) = split(': ?',$value);
				$colors[$minimum] = $color;
			}
			krsort($colors);

			$table['.0'] = 'style="font-weight: bold;"';
			if (empty($details))
			{
				$table[] = array(
					'lang' => lang('Language'),
					'percent' => lang('Percentage'),
					'total'   => lang('Phrases in total'),
					'.total'  => 'colspan="2"',
				);
//				we use a join with phpgw_lang itself to eliminate additional (obsolete) phrases not in the english langfile
				$this->db->query("SELECT l.lang,lang_name,count( l.message_id ) AS count FROM phpgw_lang l,phpgw_lang en LEFT JOIN phpgw_languages ON l.lang=lang_id WHERE l.app_name=en.app_name AND l.message_id=en.message_id AND en.lang='en' GROUP BY l.lang,lang_name ORDER BY count DESC,l.lang");
				while($row = $this->db->row(True))
				{
					if (empty($row['lang']) || empty($row['lang_name']))
					{
						continue;
					}
					if (!isset($max)) $max = $row['count'];
					$percent = sprintf('%0.1lf',100.0 * $row['count'] / $max);
					foreach($colors as $minimum => $color)
					{
						if ($percent >= $minimum)
						{
							break;
						}
					}
					$table[] = array(
						'lang' => $this->try_lang($row['lang_name']).' ('.$row['lang'].')',
						'percent' => $this->html->progressbar($percent,$percent.'%','','50px',$color,'8px'),
						'total'   => $row[count],
						'details' => '<a href="'.$this->link(array('details'=>$row['lang'])).'" title="'.lang('Show details for the applications').'">('.lang('details').')</a>'
					);
				}
				return $this->html->table($table,'cellspacing="5"');
			}
			$table[] = array(
				'app'     => lang('Application'),
				'percent' => lang('Percentage'),
				'total'   => lang('Phrases in total')
			);
//			we use a join with phpgw_lang itself to eliminate additional (obsolete) phrases not in the english langfile
			$this->db->query("SELECT l.app_name,l.lang,count( l.message_id ) AS count,l.lang,CASE WHEN l.lang='en' THEN 1 ELSE 0 END AS is_en FROM phpgw_lang l,phpgw_lang en WHERE l.app_name=en.app_name AND l.message_id=en.message_id AND en.lang='en' AND l.lang IN (".$this->db->quote($details).",'en') GROUP BY l.app_name,l.lang,is_en ORDER BY is_en DESC,count DESC,l.app_name");

			while($row = $this->db->row(True))
			{
				if (empty($row['app_name'])) continue;

				if ($row['lang'] != $details)
				{
					$max[$row['app_name']] = $row['count'];
					continue;
				}
				$m = $max[$row['app_name']] ? $max[$row['app_name']] : $row['count'];
				$percent = sprintf('%0.1lf',100.0 * $row['count'] / $m);
				unset($max[$row['app_name']]);
				foreach($colors as $minimum => $color)
				{
					if ($percent >= $minimum)
					{
						break;
					}
				}
				$table[] = array(
					'app' => ($row['app_name'] == 'common' ? 'API' : $this->try_lang($row['app_name'])).' ('.$row['app_name'].')',
					'percent' => $this->html->progressbar($percent,$percent.'%','','50px',$color,'8px'),
					'total'   => $row[count].' / '.$m
				);
			}
			foreach($max as $app => $m)
			{
				$table[] = array(
					'app' => ($app == 'common' ? 'API' : $this->try_lang($app)).' ('.$app.')',
					'percent' => $this->html->progressbar(0,'0.0%','','50px',$color,'8px'),
					'total'   => '0 / '.$m
				);
			}
			$this->db->query('SELECT lang_name FROM phpgw_languages WHERE lang_id='.$this->db->quote($details),__FILE__,__LINE__);
			$row = $this->db->row(True);
			return '<h3>'.lang('Details for language %1 (%2)',$this->try_lang($row['lang_name']),$details)."</h3>\n".
				$this->html->table($table,'cellspacing="5"').
				'<a href="'.$this->link().'">('.lang('Back to the list of languages').')</a>';
		}
	}
