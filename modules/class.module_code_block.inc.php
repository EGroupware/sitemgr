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

	class module_code_block extends Module
	{
    function module_code_block()
		{
			$this->arguments = array(
        'file' => array(
          'type' => 'textfield',
          'label' => lang('File')
        ),
        'title' => array(
          'type' => 'textfield',
          'label' => lang('Title')
        ),
        'link' => array(
          'type' => 'checkbox',
          'label' => lang('Show link to file')
        ),
        'start' => array(
          'type' => 'textfield',
          'label' => lang('First line')
        ),
        'end' => array(
          'type' => 'textfield',
          'label' => lang('Last line')
        )
			);
			$this->title = 'Code Block';
			$this->description = lang('This module displays a source code file as an TT-styled block with line numbers.');
		}

		function get_content(&$arguments,$properties)
		{  
      $lines = file(@$arguments['file']);

      $content="<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" bgcolor=\"#dedebb\" width=\"100%\"><tr><td align=\"left\">
<font class=\"content\" color=\"#363636\"><b>".@$arguments['title']."</b></font></td>";
      if (@$arguments['link']) {
        $ii=strrchr(@$arguments['file'],"/");
        $content.="<td align=\"right\">
<font class=\"content\" color=\"#363636\"><b><a href=\"".@$arguments['file']."\" alt=\"".@$arguments['title']."\">".($ii?substr($ii,1):@$arguments['file'])."</b></font></td>";
      }
      $content.="</tr></table><pre>";

// Loop through our array, show HTML source as HTML source; and line numbers too.
      if ((!isset($arguments['end']))||(@$arguments['end']<=0)||(@$arguments['end']>count($lines)))
        @$arguments['end']=count($lines);

      if ((!isset($arguments['start']))||(@$arguments['start']<=0)||(@$arguments['start']>=@$arguments['end']))
        @$arguments['start']=1;
      
      $start=@$arguments['start']-1;
      $end=@$arguments['end']-1;
      while($start<=$end) {
         $content.= substr("    ".($start+1).": ",-6) . htmlspecialchars($lines[$start++]);
      }
      $content.="</pre>";
			return $content;
		}
	}
?>
