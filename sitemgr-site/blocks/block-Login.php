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

$title = lang('Login');
$boxstuff = '<form name="login" action="'.phpgw_link('/login.php').'" method="post">';
$boxstuff .= '<input type="hidden" name="passwd_type" value="text">';
$boxstuff .= '<input type="hidden" name="phpgw_forward" value="/sitemgr/">';
$boxstuff .= '<center><font class="content">' . lang('Login Name') .'<br>';
$boxstuff .= '<input type="text" name="login" size="8" value=""><br>';
$boxstuff .= lang('Password') . '<br>';
$boxstuff .= '<input name="passwd" size="8" type="password"><br>';
$boxstuff .= '<input type="submit" value="' . lang('Login') .'" name="submitit">';
$boxstuff .= '</font></center></form>';
$boxstuff .= '<center><font class="content">' . lang("Don't have an account?") .'  ';
$boxstuff .= '<a href="'.phpgw_link('/registration/index.php').'">';
$boxstuff .= lang('Register for one now.') . '</a></font></center>';

$content = $boxstuff;

?>
