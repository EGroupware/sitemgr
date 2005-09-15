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

	/**
	 * Navigation framework module
	 * The idea behind this module is, just to have ONE highly configurable module FOR ALL navigation elements
	 * If it's possible, we don't use extra functions for different views to reduce code
	 * There are some predefined views wich are quite commen or are needed for backward compability
	 *
	 * The views are customizeable by css. See default.css in folder sitemgr-site/templates/default/style/.
	 * There is a horde of options in this module. Look at 'custom view' to see them all.
	 *
	 * @author Cornelius Weiss<egw@von-und-zu-weiss.de>
	 * @package sitemgr
	 * 
	 */
	class module_navigation extends Module
	{
		function module_navigation()
		{
			$this->arguments = array(
				'nav_type' => array(
					'type' => 'select', 
					'label' => lang('Select type of Navigation'),
					'options' => array(
						0 => lang('Select one'),
						1 => 'currentsection',
						2 => 'index',
						3 => 'index_block',
						4 => 'navigation',
						5 => 'sitetree',
						6 => 'toc',
						7 => 'toc_block',
						8 => 'path',
// 						9 => lang('custom')
					)
				)	
			);
			$this->nav_args = array(
				1 => array( // Currentsection
					'description' => lang('This block displays the current section\'s table of contents'),
					'suppress_current_page' => array(
						'type' => 'checkbox',
						'label' => lang('Suppress the current page')
					),
					'suppress_parent' => array(
						'type' => 'checkbox',
						'label' => lang('Suppress link to parent category')
					),
					'suppress_show_all' => array(
						'type' => 'checkbox',
						'label' => lang('Suppress link to index (show all)')
					)),
				2 => array( // Index
					'description' => lang('This module provides the site index, it is automatically used by the index GET parameter')
					),
				3 => array( // Index_block
					'description' => lang('This module displays the root categories, its pages and evtl. subcategories. It is meant for side areas'),
					'sub_cats' => array(
						'type' => 'checkbox',
						'label' => lang('Show subcategories')
					),
					'no_full_index' => array(
						'type' => 'checkbox',
						'label' => lang('No link to full index')
					),
					'expand' => array(
						'type' => 'checkbox',
						'label' => lang('Expand current category')
					)),
				4 => array( // Navigation
					'description' => lang("This module displays the root categories in one block each, with pages and subcategories (incl. their pages if activated).")
					),
				5 => array( // Sitetree
					'description' => lang('This block displays a javascript based tree menu')
					),
				6 => array( // Toc
					'description' => lang('This module provides a complete table of contents, it is automatically used by the toc and category_id GET parameters'),
					'category_id' =>array(
						'type' => 'textfield', 
						'label' => lang('The category to display, 0 for complete table of contents')
					)),
				7 => array( // Toc_block
					'description' => lang('This module provides a condensed table of contents, meant for side areas')
					),
				8 => array( // Path
					'description' => lang('This module provides the path to the element currently shown')
					),
// 				9 => array( //Custom
// 					'description' => lang('This module is a customisable navigation element'),
// 					'allingment' => array(
// 						'type' => 'select', 
// 						'label' => lang('Allignment of navigation elements'),
// 						'options' => array(
// 							'vertical' => lang('Vertical'),
// 							'horizontal' => lang('Horizontal'))
// 					),
// 					'textallign' => array(
// 						'type' => 'select',
// 						'label' => lang('Text allignment'),
// 						'options' => array(
// 							'left' => lang('Left'),
// 							'center' => lang('Center'),
// 							'right' => lang('Right'))))
					);
			$this->title = 'Navigation element';
			$this->description = lang("This module displays any kind of navigation element.");
		}
		
		function get_user_interface()
		{
			if(!@is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file('tabs','tabs');
				
			// I know, this is ugly. If you find a better solution for this, please help!
			$interface[] = array(
				'label' => "
				<style type=\"text/css\">
					div.activetab{ display:inline; position: relative; left: -0px; text-align:left;}
					div.inactivetab{ display:none; }
				</style>
				<script type=\"text/javascript\">
					var tab = new Tabs('".(string)(count($this->arguments['nav_type']['options']) -1)."',
					'activetab','inactivetab','tab','tabcontent','','','tabpage');
					tab.init();
				</script>",
			);
			$this->arguments['nav_type']['params'] = array(
				'onchange' => 'javascript:tab.display(this.value)'
			);
			
			$elementname = 'element[' . $this->block->version . '][nav_type]';
			$interface[] = array(
				'label' => '<b>'.$this->arguments['nav_type']['label'].'</b>'.
					parent::build_input_element($this->arguments['nav_type'],$this->block->arguments['nav_type'],$elementname)
			);
			
			// build the tab elements
			$tabs = '';
			for($id = 1; $id < count($this->arguments['nav_type']['options']); $id++)
			{
				$description = $this->nav_args[$id]['description'];
				unset($this->nav_args[$id]['description']);
				
				$tmpargs = $this->arguments;
				$this->arguments = $this->nav_args[$id];
				$tabs .= '<div id="tabcontent'. $id. '" class="inactivetab"><table>';
				$tabs .= '<tr><td colspan="2"><i>'. $description. '</i></td></tr>';
				if(count($this->nav_args[$id]) >= 1)
				{
					foreach (parent::get_user_interface() as $param)
					{
						$tabs .= '<tr><td>'.$param['label'].'</td><td>'.$param['form'].'</td></tr>';
					}
				}
				else
				{
					$tabs .= '<td>'. lang('No additional arguments required'). '</td><td></td>';
				}
				$tabs .= '</table></div>';
				$this->arguments = $tmpargs;
			}
			$interface[] = array('label' => $tabs);
			
			// show current tab
			$interface[] = array(
				'label' => "
				<script type=\"text/javascript\">
					tab.display(". $this->block->arguments['nav_type']. ");
				</script>",
			);

			return $interface;
		}
		
		// strip options from other nav_types
		function validate(&$data)
		{
			$val_data = array('nav_type' => $data['nav_type']);
			foreach($data as $key => $val)
			{
				if($this->nav_args[$data['nav_type']][$key]) $val_data[$key] = $val;
			}
			$data = $val_data;
			return true;
		}
		
		function get_content(&$arguments,$properties)
		{
			$out =  "<!-- navigation-context begins here -->\n".
				"<div id=\"navigation-context\">\n".
				"  <div id=\"navigation-";
			switch ($arguments['nav_type'])
			{
				case 1 : // Currentsection
					$out .= "currentsection\">\n";
					$arguments = array_merge($arguments, array(
						'nav_title' => lang('Pages:'),
						'current_section_only' => true,
						'suppress_current_cat' => true,
						'highlight_current_page' => true,
						'max_cat_depth' => '+0',
						'max_pages_depth' => '+0',
						'showhidden' => false,
						'no_full_index' => true,
						'show_subcats_above' => true,
					));
					break;
				case 2 : // Index
					$out .= "index\">\n";
					$arguments = array_merge($arguments, array(
						'max_cat_depth' => '2',
						'max_pages_depth' => '2',
						'showhidden' => false,
						'suppress_parent' => true,
						'suppress_show_all' => true,
						'suppress_cat_link' => true,
						'show_edit_icons' => true,
						'show_cat_description' => true,
						'show_page_description' => true,
						'no_full_index' => true,
					));
					break;
				case 3 : // Index_Block
					$out .= "index_block\">\n";
					$arguments = array_merge($arguments, array(
						'max_cat_depth' => $arguments['sub_cats'] ? '2' : '1',
						'max_pages_depth' => '1',
						'showhidden' => false,
						'suppress_parent' => true,
						'suppress_show_all' => true,
					));
					break;
				case 5 : // Sitetree
					$out .= "sitetree\">\n";
					$out .= $this->type_sitetree($arguments,$properties);
					$out .= "  </div>\n<!-- navigation context ends here -->\n</div>\n";
					return $out;
				case 6 : // Toc
					$out .= "toc\">\n";
					$arguments = array_merge($arguments, array(
						'suppress_show_all' => true,
						'show_edit_icons' => true,
						'show_cat_description' => true,
						'suppress_parent' => true,
						));
					// Topic overview
					if((int)$arguments['category_id'] == 0)
					{
						$arguments = array_merge($arguments, array(
							'nav_title' => lang('Choose a category'),
							'max_cat_depth' => '10',
							'max_pages_depth' => '0',
							'no_full_index' => true,
							
						));
					}
					// like currentsection of a certain cat
					else
					{
						$arguments = array_merge($arguments, array(
							'nav_title' => lang('Pages:'),
							'suppress_current_cat' => true,
							'max_cat_depth' => '1',
							'max_pages_depth' => '1',
							'show_page_description' => true,
							'show_subcats_above' => true,
						));
					}
					break;
				case 7 : // Toc_block
					$out .= "toc_block\">\n";
					$arguments = array_merge($arguments, array(
						'suppress_show_all' => true,
						'no_full_index' => true,
						'suppress_parent' => true,
						'max_cat_depth' => '10',
						'max_pages_depth' => '0',
					));
					break;
				case 8 : // Path
					$out .= "path\">\n";
					$arguments = array_merge($arguments, array(
						'suppress_parent' => true,
						'suppress_show_all' => true,
						'path_only' => true,
						'no_full_index' => true,
					));
					break;
					
				case 4 : // Navigation
				default:
					$out .= "navigation\">\n";
					$out .= $this->type_navigation($arguments,$properties);
					$out .= "  </div>\n<!-- navigation context ends here -->\n</div>\n";
					return $out;
			}
				
			$this->objbo =& $GLOBALS['objbo'];
			$this->page =& $GLOBALS['page'];
			
			if (!$arguments['suppress_parent'])
			{
				$category = $this->objbo->getcatwrapper($this->page->cat_id);
				$parent = $category->parent;
				if ($parent && $parent != CURRENT_SITE_ID) // do we have a parent?
				{
					$p = $this->objbo->getcatwrapper($parent);
					$entry['link'] = '<a href="'.sitemgr_link2('/index.php','category_id='.$parent).'" title="'.$p->description.'">'.$p->name.'</a>';
					$out .= "\n<div class=\"nav-header-parent\">".lang('Parent Section:')."</div>\n";
					$out .= $this->encapsulate($arguments,array($parent => $entry),'cat',$parent);
					$out .= "\n<br />\n";
				}
			}
			
			if($arguments['show_subcats_above'])
			{
				$catlinks = $arguments['category_id'] ?
					$this->objbo->getCatLinks((int)$arguments['category_id'],False,True) :
					$this->objbo->getCatLinks((int)$this->page->cat_id,False,True);
				if(count($catlinks))
				{
					$out .= "\n<div class=\"nav-header-subsection\">".lang('Subsections:')."</div>\n";
					$out .= $arguments['category_id'] ?
						$this->encapsulate($arguments,$catlinks,'cat',(int)$arguments['category_id']) :
						$this->encapsulate($arguments,$catlinks,'cat',(int)$this->page->cat_id);
					$out .= "\n<br />\n";
				}
				
			}
			
			if($arguments['nav_title'])
			{
				$out .= "\n<span class=\"nav-title\">".$arguments['nav_title']."</span>\n";
			}
			
			if (!$arguments['suppress_show_all'])
			{
				$out .= ' (<a href="'.sitemgr_link2('/index.php','category_id='.$this->page->cat_id).
					'"><i>'.lang('show all').'</i></a>)'."\n";
			}

			$cat_tree = $cat_tree_data = array('root');
			foreach($this->objbo->getCatLinks(0,true,true) as $cat_id => $cat)
			{
				// relative cat or pages depth ?
				if (strpos($arguments['max_cat_depth'],'+') === 0) (int)$arguments['max_cat_depth'] += $cat['depth'];
				if (strpos($arguments['max_pages_depth'],'+') === 0) (int)$arguments['max_pages_depth'] += $cat['depth'];
															
				if(array_key_exists($cat['depth'],$cat_tree))
				{
					$pop_depth = count($cat_tree);
					for($depth=$cat['depth']; $depth < $pop_depth; $depth++)
					{
						array_pop(&$cat_tree); array_pop(&$cat_tree_data);
					}
				}
				array_push(&$cat_tree,$cat_id); array_push(&$cat_tree_data,$cat);

				if($arguments['expand'] && $cat_id == $this->page->cat_id && $cat['depth'] >= $arguments['max_cat_depth'])
				{
					//strip allready displayed contets of cat_tree
					unset($cat_tree[0]); unset($cat_tree_data[0]);
					foreach($cat_tree_data as $num => $category)
					{
						if($category['depth'] < $arguments['max_cat_depth'])
						{
							unset($cat_tree[$num]); unset($cat_tree_data[$num]);
						}
						// we need only pages of this cat, but not cat itseve!
						if($category['depth'] ==  $arguments['max_cat_depth'] && $this->page->cat_id != $cat_tree[$num])
						{
							$cat_tree_data[$num]['pages_only'] = true;
						}
					}
					
					//expand rest
					$cat_tree = array_reverse($cat_tree); $cat_tree_data = array_reverse($cat_tree_data);
					$outstack = array($cat_tree[count(&$cat_tree) -1]); $outstack_data = array($cat_tree_data[count(&$cat_tree) -1]);
					$popcat = array_pop(&$outstack); $popcat_data = array_pop(&$outstack_data);
					while($popcat)
					{
						if(!$popcat_data['pages_only'])
						{
							$out .= $this->encapsulate($arguments,array($popcat => $popcat_data),'cat',$popcat,$popcat_data['depth']);
						}
						if(array_search($popcat,$cat_tree) !== false)
						{
							$pages = $this->objbo->getPageLinks($popcat,$arguments['showhidden'],true);
							$out .= $this->encapsulate($arguments,$pages,'page',$popcat,$popcat_data['depth'] +1);
						}
						$subcats = array_reverse($this->objbo->getCatLinks($popcat,false,true),true);
						foreach($subcats as $subcat_id => $subcat)
						{
							array_push(&$outstack,$subcat_id); array_push(&$outstack_data,$subcat);
						}
						$popcat = array_pop(&$outstack); $popcat_data = array_pop(&$outstack_data);
					}
					continue;
				}
				
				if($arguments['path_only'])
				{
					if($cat_id != $this->page->cat_id) continue;
					unset($cat_tree_data[0]);
					$pages = $this->objbo->getPageLinks($cat_id,true,true);
					if($this->page->id) $cat_tree_data[] = $pages[$this->page->id];
					$out .= $this->encapsulate($arguments,$cat_tree_data,'cat',$cat_id);
					break;
				}
				
				if($arguments['current_section_only'] && $this->page->cat_id != $cat_id) continue;
				if((int)$arguments['category_id'] > 0 && (int)$arguments['category_id'] != $cat_id) continue;
				
				if($cat['depth'] <= $arguments['max_cat_depth'])
				{
					if(!($arguments['suppress_current_cat'] && $this->page->cat_id == $cat_id))
					{
						if($arguments['suppress_cat_link'])
						{
							$cat['link'] = $cat['name'];
						}
						$out .= $this->encapsulate($arguments,array($cat_id => $cat),'cat',$cat_id,$cat['depth']);
					}
					
					if($cat['depth'] <= $arguments['max_pages_depth'])
					{ 
						$pages = $this->objbo->getPageLinks($cat_id,$arguments['showhidden'],true);
						if($arguments['suppress_current_page']) unset($pages[$this->page->id]);
						$out .= $this->encapsulate($arguments,$pages,'page',$cat_id,$cat['depth'] +1);
					}
				}
			}
			if (!$arguments['no_full_index'])
			{
				$out .= "    <div class=\"nav-full-index\">\n";
				$out .= "      <a href=\"".sitemgr_link2('/index.php','index=1')."\">". lang('View full index') . "</a>\n";
				$out .= "    </div>\n";
			}

			$out .= "  </div>\n<!-- navigation context ends here -->\n</div>\n";
			return $out;
		}
		
		/**
		 * encapsulates navigation elements
		 *
		 * @param $arguments of module.
		 * @param $data 
		 * @param $type string 'cat' or 'page'
		 * @param $cat_id of cat itselve or of cat page belongs to.
		 * @param $depth logical deps of cat or page.
		 *
		 */
		function encapsulate($arguments,$data,$type,$cat_id,$depth=1)
		{
			$out  = "    <div class=\"nav-".$type."-entry depth-".$depth."\">\n";
			$out .= "      <ul>\n";
			foreach($data as $id => $entry)
			{
				if($arguments['highlight_current_page'] && $id == $this->page->id && $type == 'page')
				{
					$entry['link'] = "<div class=\"nav-highlight_current_page\">".$entry['link'].'</div>';
				}

				$out .= "        <li>\n";
				$out .= "          ".$entry['link']."\n";
				
				if($arguments['show_edit_icons'])
				{
					$out .= "<span class=\"nav-edit-icons\">";
					$out .= $type == 'cat' ? 
						$this->objbo->getEditIconsCat($id) :
						$this->objbo->getEditIconsPage($id,$cat_id);
					$out .= "</span>\n";
				}
// 				_debug_array($entry);
				if(($arguments['show_cat_description'] && $type == 'cat') || ($arguments['show_cat_description'] && $type == 'page'))
				{
					$out .= "<span class=\"nav-".$type."-description\">";
					$out .= $type =='cat' ? $entry['description'] : $entry['subtitle'];
					$out .= "</span>\n";
				}
				
				$out .= "        </li>\n";
			}
			$out .= "      </ul>\n";
			$out .= "    </div>\n";
			return $out;
		}
		
		function type_navigation(&$arguments,$properties)
		{
			global $objbo,$page;
			$index_pages = $objbo->getIndex(False,False,True);

			if (!count($index_pages))
			{
				return lang('You do not have access to any content on this site.');
			}
			$index_pages[] = array(	// this is used to correctly finish the last block
				'cat_id'	=> 0,
				'catdepth'	=> 1,
			);

			$this->template =& CreateObject('phpgwapi.Template',$this->find_template_dir());
			$this->template->set_file('cat_block','navigation.tpl');
			$this->template->set_block('cat_block','block_start');
			$this->template->set_block('cat_block','level1');
			$this->template->set_block('cat_block','level2');
			$this->template->set_block('cat_block','block_end');
			
			$last_cat_id = 0;
			foreach($index_pages as $ipage)
			{
				preg_match('/href="([^"]+)"/i',$ipage['catlink'],$matches);
				$this->template->set_var(array(
					'item_link' => $matches[1],
					'item_name' => $ipage['catname'],
					'item_desc' => $ipage['catdescrip'],
				));
				if ($ipage['cat_id'] != $last_cat_id)	// new category
				{
					switch ($ipage['catdepth'])
					{
						case 1:	// start of a new level-1 block
							if ($last_cat_id)	// if there was a previous block, finish that one first
							{
								$content .= $this->template->parse('out','block_end');
							}
							// start the new block
							if ($ipage['cat_id'])
							{
								$content .= $this->template->parse('out','block_start');
							}
							break;
						case 2:
							$content .= $this->template->parse('out','level1');
					}
				}
				$last_cat_id = $ipage['cat_id'];
				
				// show the pages of the active cat or first-level pages
				if ($ipage['page_id'] && ($ipage['cat_id'] == $page->cat_id || $ipage['catdepth'] == 1))
				{
					preg_match('/href="([^"]+)"/i',$ipage['pagelink'],$matches);
					$this->template->set_var(array(
						'item_link'		=> $matches[1],
						'item_name'		=> $ipage['pagesubtitle'],
						'item_desc'		=> $ipage['pagetitle'],
					));
					$content .= $this->template->parse('out',$ipage['catdepth'] == 1 ? 'level1' : 'level2');
				}
			}
			return $content;
		}
		
		function type_sitetree(&$arguments,$properties)
		{
			$title = '';
			if ($arguments['menutree'])
			{
				$this->expandedcats = array_keys($arguments['menutree']);
			}
			else
			{
				$this->expandedcats = Array();
			}
			$topcats = $GLOBALS['objbo']->getCatLinks(0,False);
	
			$content = "<script type='text/javascript'>
				// the whole thing only works in a DOM capable browser or IE 4*/
				
				function add(catid)
				{
					document.cookie = 'block[" . $this->block->id . "][menutree][' + catid + ']=';
				}	
				
				function remove(catid)
				{
					var now =& new Date();
					document.cookie = 'block[" . $this->block->id . "][menutree][' + catid + ']=; expires=' + now.toGMTString();
				}
				
				function toggle(image, catid)
				{
					if (document.getElementById)
					{ //DOM capable
						styleObj = document.getElementById(catid);
					}
					else //we're helpless
					{
					return 
					}
				
					if (styleObj.style.display == 'none')
					{
						add(catid);
						image.src = 'images/tree_collapse.gif';
						styleObj.style.display = 'block';
					}
					else
					{
						remove(catid);
						image.src = 'images/tree_expand.gif';
						styleObj.style.display = 'none';
					}
				}
				</script>";
				
			if (count($topcats)==0)
			{
				$content=lang('You do not have access to any content on this site.');
			}
			else
			{
				$content .= "\n" . 
					'<table border="0" cellspacing="0" cellpadding="0" width="100%">' .
					$this->showcat($topcats) .
					'</table>' .
					"\n";
				$content .= '<br><a href="'.sitemgr_link('toc=1').'"><font size="1">(' . lang('Table of contents') . ')</font></a>';
			}
			return $content;
		}
		
		function showcat($cats)
		{
			while(list($cat_id,$cat) = each($cats))
			{
				$status = in_array($cat_id,$this->expandedcats);
				$childrenandself = array_keys($GLOBALS['objbo']->getCatLinks($cat_id));
				$childrenandself[] = $cat_id;
				$catcolour = in_array($GLOBALS['page']->cat_id,$childrenandself) ? "red" : "black";
				$tree .= "\n" . 
					'<tr><td width="10%">' . 
					'<img src="images/tree_' .
					($status ? "collapse" : "expand") .
					'.gif" onclick="toggle(this, \'' . 
					$cat_id . 
					'\')"></td><td><b title="' .
					$cat['description'] .
					'" style="color:' .
					$catcolour .
					'">'.
					$cat['name'] . 
					'</b></td></tr>' . 
					"\n";
				$subcats = $GLOBALS['objbo']->getCatLinks($cat_id,False);
				$pages = $GLOBALS['objbo']->getPageLinks($cat_id);
				if ($subcats || $pages)
				{
					$tree .= '<tr><td></td><td><table style="display:' .
						($status ? "block" : "none") .
						'" border="0" cellspacing="0" cellpadding="0" width="100%" id="'.
						$cat_id .
						'">';
					while(list($page_id,$page) = @each($pages))
					{
						//we abuse the subtitle in a nonstandard way: we want it to serve as a *short title* that is displayed
						//in the tree menu, so that we can have long titles on the page that would not be nice in the tree menu
						$title = $page['subtitle'] ? $page['subtitle'] : $page['title'];
						$tree .= '<tr><td colspan="2">' . 
							(($page_id == $GLOBALS['page']->id) ? 
								('<span style="color:red">' . $title . '</span>') :
								('<a href="' . sitemgr_link('page_name='. $page['name']) . '">' . $title . '</a>')
							) . 
							'</td></tr>';
					}
					if ($subcats)
					{
						$tree .= $this->showcat($subcats);
					}
	
					$tree .= '</table></td></tr>';
				}
			}
			return $tree;
		}
	}
?>
