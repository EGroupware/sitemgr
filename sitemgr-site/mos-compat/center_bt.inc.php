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

	/* $Id: left_bt.inc.php 13831 2004-02-22 01:46:27Z ralfbecker $ */

class center_bt {
	function apply_transform($title,$content,$block) {
		global $mos_style;
		switch ($mos_style) {
			case -1: // raw
				return $content;
			case -3: // extra divs
				return 
					"<div class=\"module\">".
					"	<div class=\"centermodule_title\">".
					"		$title\n".
					"	</div>".
					"	<div class=\"centermodule_content\">".
					"		$content\n".
					"	</div>".
					"	<div class=\"centermodule_close\">".
					"	</div>".
					"</div>";
			case -2: // XHTML
			case  1: // horizontal
			case  0: // normal
			default: 
				return
					"<table class=\"moduletable\">\n".
					"	<tr>\n".
					"		<th>$title</th>\n".
					"	</tr>\n".
					"	<tr>\n".
					"		<td>\n".
					"			$content\n".
					"		</td>\n".
					"	</tr>\n".
					"</table>\n";
		}
		
	}
}
