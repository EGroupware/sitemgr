<?php 

	class module_news extends Module
	{
		function module_news()
		{
			//specification of options is postponed into the get_user_interface function
			$this->arguments = array(
				'category' => array('type' => 'select', 'label' => lang('Choose a category'), 'options' => array()),
				'rsslink' => array('type' => 'checkbox', 'label' => lang('Do you want to publish a RSS feed for this news category'))
			);
			$this->get = array('item');
			$this->properties = array();
			$this->title = lang('News module');
			$this->description = lang('This module publishes news from the news_admin application on your website.');
			$this->template;
		}

		function get_user_interface()
		{
			//we could put this into the module's constructor, but by putting it here, we make it execute only when the block is edited,
			//and not when it is generated for the web site, thus speeding the latter up slightly
			$cat = createobject('phpgwapi.categories','','news_admin');
			$cats = $cat->return_array('mains',0,False);
			$cat_ids = array(0 => lang('Mains'));
			while (list(,$category) = @each($cats))
			{
				$cat_ids[$category['id']] = $category['name'];
			}
			$this->arguments['category']['options'] = $cat_ids;
			return parent::get_user_interface();
		}

		function get_content(&$arguments,$properties)
		{
			$bonews = CreateObject('news_admin.bonews');

			$this->template = Createobject('phpgwapi.Template');
			$this->template->set_root($this->find_template_dir());
			$this->template->set_file('news','newsblock.tpl');
			$this->template->set_block('news','NewsBlock','newsitem');
			$this->template->set_block('news','RssBlock','rsshandle');

			$item = $arguments['item'];
			if ($item)
			{
				$newsitem = $bonews->get_news($item);
				if ($newsitem)
				{
					$this->render($newsitem[$item]);
					return $this->template->get_var('newsitem');
				}
				else
				{
					return lang('No matching news item');
				}
			}

			$newslist = $bonews->get_NewsList($arguments['category'], false);

			if ($arguments['rss'])
			{
				$this->template->set_var('rsslink',
					$GLOBALS['phpgw_info']['server']['webserver_url'] . '/news_admin/website/export.php?cat_id=' . $arguments['category']);
				$this->template->parse('rsshandle','RssBlock');
			}
			else
			{
				$this->template->set_var('rsshandle','');
			}

			while (list(,$newsitem) = @each($newslist))
			{
				$this->render($newsitem);
			}
			return $this->template->parse('out','news');
		}

		function render($newsitem)
		{
			$this->template->set_var(array(
				'news_title' => $newsitem['subject'],
				'news_submitter' => $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']),
				'news_date' => $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
				'news_content' => $newsitem['content']
			));
			$this->template->parse('newsitem','NewsBlock',True);
		}
	}
