<?php
class right_bt
{
	function apply_transform($title,$content)
	{
		return '
<div class="divSidebox">
	<div class="divSideboxHeader"><span>'. $title .'</span></div>
	<div class="divSideboxEntry">
		'. str_replace('&middot;','<img src="templates/idots/images/orange-ball.png" alt="+" />',$content). '</div>
</div>
<div class="sideboxSpace"></div>';
	}
}
