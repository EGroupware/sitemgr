<?php

class left_bt
{
	function apply_transform($title,$content)
	{
	return "
	<img src=\"templates/realss/images/left_bt_top.png\" 
	     style=\"display:block\"/>
	<div class=\"left_bt\">
	<h3>$title</h3>
	$content
	</div>
	<img src=\"templates/realss/images/left_bt_bottom.png\" 
	     style=\"display:block; margin-bottom: 10px\"/>
	";
	}
}
