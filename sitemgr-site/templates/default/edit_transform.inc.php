<?php
class edit_transform
{
	function edit_transform()
	{
		if (!is_object($GLOBALS['phpgw']->html))
		{
			$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
		}
		$this->modulebo = &$GLOBALS['Common_BO']->modules;
		$this->content_ui = CreateObject('sitemgr.Content_UI');
	}

	function apply_transform($title,$content,$block)
	{
		$link_data['menuaction'] = 'sitemgr.Content_UI.manage';
		$link_data['block_id'] = $block->id;
		$frame = '<div class="edit"><div class="editIcons">';
		$frame .= '<span class="editIconText" title="'.
			lang('Module: %1, Scope: %2, Contentarea: %3',$block->module_name,$block->page_id ? lang('Page') : lang('Site wide'),$block->area).
			'">'.$block->module_name."</span>\n";
		foreach(array(
			'up.button' => array(lang('Move block up (decrease sort order)').": $block->sort_order-1",'sort_order' => -1),
			'down.button' => array(lang('Move block down (increase sort order)').": $block->sort_order+1",'sort_order' => 1),
			'edit' => array(lang('Edit this block')),
			'delete' => array(lang('Delete this block'),'deleteBlock' => $block->id),
		) as $name => $data)
		{
			$label = array_shift($data);
			$frame .= $GLOBALS['phpgw']->html->a_href(
				$GLOBALS['phpgw']->html->image('sitemgr',$name,$label,'border="0"'),$link_data+$data,False,'target="editwindow"');
		}
		$frame .= "</div>\n";
		return $frame . $content . '</div>';
	}

	function area_transform($contentarea,$content,$page)
	{
		$frame = '<div class="edit"><div class="editIcons">';
		//$frame .= $GLOBALS['phpgw']->html->image('sitemgr','question.button',
		//	lang('Contentarea').': '.$contentarea);
		$frame .= '<span class="editIconText" title="'.lang('Contentarea').': '.$contentarea.'">'.$contentarea."</span>\n";

		$permittedmodules = $this->modulebo->getcascadingmodulepermissions($contentarea,$page->cat_id);

		$link_data['menuaction'] = 'sitemgr.Content_UI.manage';
		if ($page->id || !$page->cat_id)
		{
			$link_data['page_id'] = intval($page->id);
		}
		else
		{
			$link_data['cat_id'] = $page->cat_id;
		}

		if ($permittedmodules)
		{
			$frame .= ' <select onchange="if (this.value > 0) window.open(\''.$GLOBALS['phpgw']->link('/index.php',$link_data).'&area='.$contentarea.'&add_block=\'+this.value,\'editwindow\',\'width=800,height=600,scrollbars=yes\')">' .
				'<option value="0">'.lang('Add block ...').'</option>'.
				$this->content_ui->inputmoduleselect($permittedmodules) .
				'</select>';
		}
		$frame .= "</div>\n";
		return $frame . $content . '</div>';
	}
}
