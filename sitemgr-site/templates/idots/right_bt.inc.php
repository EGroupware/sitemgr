<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Copyright (c) 2004 by RalfBecker@outdoor-training.de                     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

class right_bt
{
	function apply_transform($title,$content)
	{
		return '
<div class="divSidebox">
	<div class="divSideboxHeader"><span>'. $title .'</span></div>
	<div class="divSideboxEntry">
		'. str_replace('&middot;','<img src="templates/default/images/bullet.png" alt="+" />',$content). '</div>
</div>
<div class="sideboxSpace"></div>';
	}
}
