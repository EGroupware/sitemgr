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

if (eregi("block-Login.php", $PHP_SELF)) {
    Header("Location: index.php");
    die();
}

$title = 'Login';
$boxstuff = '<form name="login" action="'.phpgw_link('/login.php').'" method="post">';
$boxstuff .= '<input type="hidden" name="passwd_type" value="text">';
$boxstuff .= '<input type="hidden" name="phpgw_forward" value="/sitemgr/">';
$boxstuff .= '<center><font class="content">Login Name<br>';
$boxstuff .= '<input type="text" name="login" size="8" value=""><br>';
$boxstuff .= 'Password<br>';
$boxstuff .= '<input name="passwd" size="8" type="password"><br>';
$boxstuff .= '<input type="submit" value="Login" name="submitit">';
$boxstuff .= '</font></center></form>';
$boxstuff .= '<center><font class="content">Don\'t have an account?  ';
$boxstuff .= '<a href="'.phpgw_link('/registration/index.php').'">';
$boxstuff .= 'Register for one now.</a></font></center>';

$content = $boxstuff;

?>
