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

if (isset($GLOBALS['objui']) && $GLOBALS['objui'] instanceof ui && file_exists($file=$GLOBALS['objui']->templateroot.'/'.basename(__FILE__)))
{
	include($file);
}
else
{
	class right_bt
	{
		function apply_transform($title,$content,$block)
		{
			global $mos_style;
			switch ($mos_style) {
				case -1: // raw
					return $content;
				case -3: // extra divs
					return
						"\n".
						"<div class=\"module\">\n".
						"	<div class=\"rightmodule_open\">\n".
						"		<div class=\"rightmodule_title\">\n".
						"			$title\n".
						"		</div>\n".
						"	</div>\n".
						"	<div class =\"rightmodule_main\">\n".
						"		<div class=\"rightmodule_content\">\n".
						"			$content\n".
						"		</div>\n".
						"	</div>\n".
						"	<div class=\"rightmodule_close\">\n".
						"	</div>\n".
						"</div>\n";
				case -2: // XHTML
				case  1: // horizontal
				case  0: // normal
				default:
					return
						"\n".
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
}