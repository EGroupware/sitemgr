<?php
/* blockconfig: <title>Choose language</title> */
/* blockconfig: <description>This block lets users of multilingual sites change language</description> */
/* blockconfig: <view>0</view> (everybody) */

if (eregi("block-Choose_lang.php", $PHP_SELF)) {
    Header("Location: index.php");
    die();
}

function getlangname($lang)
{
  $GLOBALS['phpgw']->db->query("select lang_name from languages where lang_id = '$lang'",__LINE__,__FILE__);
  $GLOBALS['phpgw']->db->next_record();
  return $GLOBALS['phpgw']->db->f('lang_name');
}

if ($GLOBALS['sitemgr_info']['sitelanguages'])
{
  $boxstuff = '<form name="langselect" action="'.$_SERVER['REQUEST_URI'].'" method="post">';
  $boxstuff .= '<select onChange="this.form.submit()" name="language">';
  foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
    {
      $selected='';
      if ($lang == $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
	{
	  $selected = 'selected="selected" ';
	}
      $boxstuff .= '<option ' . $selected . 'value="' . $lang . '">'. getlangname($lang) . '</option>';
    }
  $boxstuff .= '</select></form>';
  
  $content = $boxstuff;
}
else
{ 
  $content = lang('No sitelanguages configured');
}