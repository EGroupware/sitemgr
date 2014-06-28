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

			$this->db =& $GLOBALS['egw']->db;
		}

		function try_lang($message_id,$args='')
		{
			return translation::translate($message_id,$args,'');
		}

		function get_content(&$arguments,$properties)
		{
			unset($properties);	// not used, but required by function signature

			$details = $arguments['details'];
			$stats = translation::statistics($details);
			//echo "<p>translation status for lang='$details'</p>\n";

			$colors = array();
			foreach(preg_split('/, ?/',$arguments['colors']) as $value)
			{
				list($color,$minimum) = preg_split('/: ?/',$value);
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
				$max = null;
				foreach($stats as $lang => $num)
				{
					if (!isset($max)) $max = $num;
					$percent = sprintf('%0.1lf',100.0 * $num / $max);
					foreach($colors as $minimum => $color)
					{
						if ($percent >= $minimum)
						{
							break;
						}
					}
					$table[] = array(
						'lang' => $this->try_lang(translation::lang2language($lang)).' ('.$lang.')',
						'percent' => html::progressbar($percent,$percent.'%','','50px',$color,'8px'),
						'total'   => $num,
						'details' => '<a href="'.$this->link(array('details'=>$lang)).'" title="'.lang('Show details for the applications').'">('.lang('details').')</a>'
					);
				}
				return html::table($table,'cellspacing="5"');
			}
			$table[] = array(
				'app'     => lang('Application'),
				'percent' => lang('Percentage'),
				'total'   => lang('Phrases in total')
			);

			foreach(translation::statistics('en') as $app => $max)
			{
				$percent = sprintf('%0.1lf',100.0 * $stats[$app] / $max);
				foreach($colors as $minimum => $color)
				{
					if ($percent >= $minimum)
					{
						break;
					}
				}
				$table[] = array(
					'app' => ($app == 'phpgwapi' ? 'API' : $this->try_lang($app)).' ('.$app.')',
					'percent' => html::progressbar($percent,$percent.'%','','50px',$color,'8px'),
					'total'   => $stats[$app].' / '.$max
				);
			}
			$lang_name = translation::lang2language($details);

			return '<h3>'.lang('Details for language %1 (%2)',$this->try_lang($lang_name),$details)."</h3>\n".
				html::table($table,'cellspacing="5"').
				'<a href="'.$this->link().'">('.lang('Back to the list of languages').')</a>';
		}
	}
