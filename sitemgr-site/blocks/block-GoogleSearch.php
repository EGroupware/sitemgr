<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2002 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (eregi("block-GoogleSearch.php", $PHP_SELF)) {
    Header("Location: index.php");
    die();
}

$title = 'Google Search';
$content = '<form action="http://www.google.com/search" name=f>';
$content .= '<img src="images/Google_25wht.gif" border="0" align="middle" hspace="0" vspace="0"><br>';
$content .= '<center><input type=hidden name=hl value=en>';
$content .= '<input type=hidden name=ie value="ISO-8859-1">';
$content .= '<input maxLength=256 size=20 name=q value=""><br>';
$content .= '<input type=submit value="Google Search" name=btnG></center>';
$content .= '</form>';

?>
