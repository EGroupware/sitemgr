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
			//echo "<p>translation status for lang='$details'</p>\n";

			$lang_ctimes = $GLOBALS['egw_info']['server']['lang_ctimes'];
			$lastest = 0;
			foreach($lang_ctimes as $lang => $ctimes)
			{
				if ($lastest < ($l = max($ctimes))) $lastest = $l;
			}
			//echo "<p>latest lang modification = $lastest = ".date('Y-m-d H:i',$lastest)."</p>\n";

			$cache_file = $GLOBALS['egw_info']['server']['temp_dir'].'/translation_status.cache';
			$ctime_cache = @filectime($cache_file);
			//echo "<p>cache file = '$cache_file', filectime = $ctime_cache = ".date('Y-m-d H:i',$ctime_cache)."</p>\n";

			$cache = array();
			if (file_exists($cache_file) && (int) $ctime_cache > $lastest)
			{
				$cache = json_decode(file_get_contents($cache_file), true);
			}
			if (!$cache || !isset($cache[$details]))	// requested details are not in the cache ==> query the database
			{
				$cache[$details] = array();
				if (!$details)
				{
					$en_phrases = array_keys(translation::load_app_files(null, 'en', 'all-apps'));
					foreach(translation::get_available_langs() as $lang => $language)
					{
						$lang_phrases = array_keys(translation::load_app_files(null, $lang, 'all-apps'));
						$valid_phrases = array_intersect($lang_phrases, $en_phrases);
						$cache[$details][] = array(
							'lang' => $lang,
							'lang_name' => $language,
							'count' => count($valid_phrases),
						);
					}
				}
				else
				{
					$cache['en'] = array();
					foreach(array_keys($GLOBALS['egw_info']['apps']) as $app)
					{
						$en_phrases = array_keys(translation::load_app_files(null, 'en', $app));
						if (count($en_phrases) <= 2) continue;
						$cache['en'][] = array(
							'app_name' => $app,
							'lang' => 'en',
							'count' => count($en_phrases),
						);
						$lang_phrases = array_keys(translation::load_app_files(null, $details, $app));
						$valid_phrases = array_intersect($lang_phrases, $en_phrases);
						$cache[$details][] = array(
							'app_name' => $app,
							'lang' => $details,
							'count' => count($valid_phrases),
						);
					}
					usort($cache['en'], function($a, $b) {
						return $b['count'] - $a['count'];
					});
				}
				usort($cache[$details], function($a, $b) {
					return $b['count'] - $a['count'];
				});
				//echo "read details for '$details'"; _debug_array($cache[$details]);
				$c = fopen($cache_file,'w');
				fputs($c, json_encode($cache));
				fclose($c);
			}

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
				foreach($cache[$details] as $row)
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
						'percent' => html::progressbar($percent,$percent.'%','','50px',$color,'8px'),
						'total'   => $row[count],
						'details' => '<a href="'.$this->link(array('details'=>$row['lang'])).'" title="'.lang('Show details for the applications').'">('.lang('details').')</a>'
					);
				}
				return html::table($table,'cellspacing="5"');
			}
			$table[] = array(
				'app'     => lang('Application'),
				'percent' => lang('Percentage'),
				'total'   => lang('Phrases in total')
			);

			$max = array();
			foreach($cache['en'] as $row)
			{
				$max[$row['app_name']] = $row['count'];
			}
			foreach($cache[$details] as $row)
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
					'percent' => html::progressbar($percent,$percent.'%','','50px',$color,'8px'),
					'total'   => $row[count].' / '.$m
				);
			}
			foreach($max as $app => $m)
			{
				$table[] = array(
					'app' => ($app == 'common' ? 'API' : $this->try_lang($app)).' ('.$app.')',
					'percent' => html::progressbar(0,'0.0%','','50px',$color,'8px'),
					'total'   => '0 / '.$m
				);
			}
			$lang_name = translation::lang2language($details);

			return '<h3>'.lang('Details for language %1 (%2)',$this->try_lang($lang_name),$details)."</h3>\n".
				html::table($table,'cellspacing="5"').
				'<a href="'.$this->link().'">('.lang('Back to the list of languages').')</a>';
		}
	}
