<?php
/* blockconfig: <title>Administration</title> */
/* blockconfig: <description>This block lets registered phpgw users go back to phpgw</description> */
/* blockconfig: <view>2</view> (phpgw user) */

if (eregi("block-Administration.php", $PHP_SELF)) {
    Header("Location: index.php");
    die();
}

  $content = '&nbsp;&nbsp;<strong><big>&middot;</big></strong><a href="'.phpgw_link('/index.php','menuaction=sitemgr.MainMenu_UI.DisplayMenu').'">' . lang('Content Manager') . '</a>';
