<?php
    /***************************************************************************\
    * phpGroupWare - Web Content Manager
    * http://www.phpgroupware.org                                               *
    * -----------------------------------------------                           *
    * This program is free software; you can redistribute it and/or modify it   *
    * under the terms of the GNU General Public License as published by the     *
    * Free Software Foundation; either version 2 of the License, or (at your    *
    * option) any later version.                                                *
    \***************************************************************************/
	/* $Id$ */

	function about_app($tpl,$handle)
	{
		$s = '<b>' . lang('Web Content Manager') . '</b><p>' . lang('written by:') . '&nbsp;Patrick Walsh<br>Tina Alinaghian<br>&nbsp;Fang Ming Lo<br>&nbsp;Austin Lee<br>&nbsp;Siu Leung';
		return $s;
	}
?>
