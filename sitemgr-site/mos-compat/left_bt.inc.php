<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - SiteMgr support for Mambo Open Source templates     *
	* http://www.egroupware.org                                                *
	* Written and (c) by RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

class left_bt
{
	function apply_transform($title,$content)
	{
		return
'<table class="moduletable">
	<tr>
		<th>'.$title.'</th>
	</tr>
	<tr>
		<td>
			'.$content.'
		</td>
	</tr>
</table>
';
	}
}
