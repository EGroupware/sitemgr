<?php
class edit_transform
{
	function apply_transform($title,$content,$block)
	{
		if (!is_object($GLOBALS['phpgw']->html))
		{
			$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
		}
		$link_data['menuaction'] = 'sitemgr.Content_UI.manage';
		$link_data['block_id'] = $block->id;
		$frame = '<div class="edit"><div class="editIcons">';
		$frame .= $GLOBALS['phpgw']->html->image('sitemgr','question.button',
			lang('Module: %1, Scope: %2, Contentarea: %3',$block->module_name,$block->page_id ? lang('Page') : lang('Site wide'),$block->area)).' ';
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
}
