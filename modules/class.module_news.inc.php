<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class module_news extends Module
	{
		function module_news()
		{
			//specification of options is postponed into the get_user_interface function
			$this->arguments = array(
				'category' => array('type' => 'select', 'label' => lang('Choose a category'), 'options' => array()),
				'rsslink' => array('type' => 'checkbox', 'label' => lang('Do you want to publish a RSS feed for this news category')),
				'limit' => array(
					'type' => 'textfield', 
					'label' => lang('Number of news items to be displayed on page'),
					'params' => array('size' => 3)
				),
				'layout' => array(
					'type' => 'select',
					'label' => lang('Choose news layout'),
					'options' => array(
						'complete' => lang('Complete News'),
						'header' => lang('show only headers containing links for complete news')
					),
				),
			);
			$this->get = array('item','start');
			$this->session = array('item','start');
			$this->properties = array();
			$this->title = lang('News module');
			$this->description = lang('This module publishes news from the news_admin application on your website. Be aware of news_admin\'s ACL restrictions.');
			$this->template;
		}

		function get_user_interface()
		{
			if (!is_dir(EGW_SERVER_ROOT.'/news_admin') || !isset($GLOBALS['egw_info']['apps']['news_admin']))
			{
				return lang("Application '%1' is not installed !!!<br>Please install it, to be able to use the block.",'news_admin');
			}
			//we could put this into the module's constructor, but by putting it here, we make it execute only when the block is edited,
			//and not when it is generated for the web site, thus speeding the latter up slightly
			$cat = createobject('phpgwapi.categories','','news_admin');
			$cats = $cat->return_array('all',0,False,'','','cat_name',True);
			if ($cats)
			{
				$cat_ids['all'] = lang('All categories');
				while (list(,$category) = each($cats))
				{
					$cat_ids[$category['id']] = $category['name'];
				}
			}
			$this->arguments['category']['options'] = $cat_ids;
			return parent::get_user_interface();
		}

		function get_content(&$arguments,$properties)
		{
			if (!is_dir(EGW_SERVER_ROOT.'/news_admin') || !isset($GLOBALS['egw_info']['apps']['news_admin']))
			{
				return lang("Application '%1' is not installed !!!<br>Please install it, to be able to use the block.",'news_admin');
			}
			$bonews =& CreateObject('news_admin.bonews');
			
			$arguments['layout'] = $arguments['layout'] ? $arguments['layout'] : 'complete';
			$this->template = Createobject('phpgwapi.Template',$this->find_template_dir());
			$this->template->set_file('news',$arguments['layout'].'_style.tpl');
			$this->template->set_block('news','NewsBlock','newsitem');
			$this->template->set_block('news','RssBlock','rsshandle');

			$limit = $arguments['limit'] ? $arguments['limit'] : 5;

			if ($arguments['rsslink'])
			{
				$this->template->set_var('rsslink',
					$GLOBALS['egw_info']['server']['webserver_url'] . '/news_admin/website/export.php?cat_id=' . $arguments['category']);
				$this->template->parse('rsshandle','RssBlock');
			}
			else
			{
				$this->template->set_var('rsshandle','');
			}

			// somehow $arguments['item'] is set to some whitespace
			// i have no idea why :( 
			// so i added trim
			// lkneschke 2004-02-24
			$item = $arguments['item'] ? $arguments['item'] : $_GET['item'];
			trim($item);
			if ($item)
			{
				$newsitem = $bonews->get_news($item);
				if ($newsitem && ($newsitem['category'] == $arguments['category']))
				{
					$this->render($newsitem,$arguments['layout']);
					$link_data['item'] = 0;
					$this->template->set_var('morelink',
						'<a href="' . $this->link($link_data) . '">' . lang('More news') . '</a>'
					);
					return $this->template->parse('out','news');
//					return $this->template->get_var('newsitem');
				}
				else
				{
					return lang('No matching news item');
				}
			}


			$newslist = $bonews->get_newslist($arguments['category'],$arguments['start'],'','',$limit,True);
			
			while (list(,$newsitem) = @each($newslist))
			{
				$this->render($newsitem,$arguments['layout']);
			}
			if ($arguments['start'])
			{
				$link_data['start'] = $arguments['start'] - $limit;
				$this->template->set_var('lesslink',
					'<a href="' . $this->link($link_data) . '">&lt;&lt;&lt;</a>'
				);
			}
			if ($bonews->total > $arguments['start'] + $limit)
			{
				$link_data['start'] = $arguments['start'] + $limit;
				$this->template->set_var('morelink',
					'<a href="' . $this->link($link_data) . '">' . lang('More news') . '</a>'
				);
			}
			return $this->template->parse('out','news');
		}

		function render($newsitem,$layout='complete')
		{
			switch($layout)
			{
				case 'header' :
					$this->template->set_var(array(
						'news_date' => $arguments['header_show_date'] ? $GLOBALS['egw']->common->show_date($newsitem['date'],'d.m.y') : '',
						'news_title' => '<a href="'. $this->link(false,false,array( 0 => 
							array(
								'module_name' => 'news',
								'arguments' => array(
									'layout' => 'complete',
									'item' => $newsitem['id'],
									'category' => $newsitem['category'],
								),
								'page' => false,
								'area' => false,
								'sort_order' => false
							)
						)). '">'. $newsitem['subject']. '</a>'
					));
					break;

				default:
				case 'complete' :
					$data = $GLOBALS['egw']->accounts->get_account_data($newsitem['submittedby']);
					$this->template->set_var(array(
						'news_title' => $newsitem['subject'],
						'news_submitter' => lang('Submitted by') . ' ' . $data[$newsitem['submittedby']]['fullname'],
						'news_date' => $GLOBALS['egw']->common->show_date($newsitem['date']),
						'news_content' => $newsitem['content']
					));
					break;
			}
			$this->template->parse('newsitem','NewsBlock',True);
		}
	}
