<?php
/**
 * EGroupware SiteMgr CMS - index.php for generated website
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage sitemgr-site
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

use EGroupware\Api;

$site_id=0;
/**
 * Determine the site from the URL ($_SERVER['PHP_SELF'])
 *
 * @param array &$anon_account anon account_info with keys 'user', 'passwd' and optional 'passwd_type'
 * @return boolean true if a site is found or dies if not site defined for the URL
 */
function sitemgr_get_site(&$anon_account)
{
	global $site_url, $site_id, $sitemgr_info;

	$site_urls[] = $path = preg_replace('/\/[^\/]*$/','',$_SERVER['PHP_SELF']) . '/';
	$site_urls[] = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_ADDR'] . $path;
	$site_urls[] = $site_url  = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $path;

	//echo "<p>sitemgr_get_site('$site_url')</p>\n";
	$GLOBALS['egw']->db->select('egw_sitemgr_sites','anonymous_user,anonymous_passwd,site_id',
		array('site_url' => $site_urls),__LINE__,__FILE__,false,'','sitemgr');

	if ($GLOBALS['egw']->db->next_record())
	{
		$anon_account = array(
			'login'  => $GLOBALS['egw']->db->f('anonymous_user'),
			'passwd' => $GLOBALS['egw']->db->f('anonymous_passwd'),
			'passwd_type' => 'text',
		);

		$sitemgr_info['anonymous_user'] = $anon_account['login'];

		if($GLOBALS['egw_info']['server']['allow_cookie_auth'])
		{
			$eGW_remember = explode('::::',stripslashes($_COOKIE['eGW_remember']));

			if (count($eGW_remember) == 3 && $GLOBALS['egw']->accounts->name2id($eGW_remember[0],'account_lid','u'))
			{
				$anon_account = array(
					'login' => $eGW_remember[0],
					'passwd' => $eGW_remember[1],
					'passwd_type' => $eGW_remember[2],
				);
			}
		}
		if (!$anon_account['login'])
		{
			die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
		}
		$site_id = $GLOBALS['egw']->db->f('site_id');

		//echo "<p>sitemgr_get_site('$site_url') site_id=$site_id, anon_account=".print_r($anon_account,true)."</p>\n";
		return true;
	}
	die(lang('THERE IS NO WEBSITE CONFIGURED FOR URL %1.  NOTIFY THE ADMINISTRATOR.',$site_url.' ('.$GLOBALS['egw_info']['server']['default_domain'].')'));
}

$GLOBALS['egw_info'] = array(
	'flags' => array(
		'disable_Template_class' => True,
		'noheader'   => True,
		'currentapp' => 'sitemgr-link',
		'autocreate_session_callback' => 'sitemgr_get_site',
));

/* Preset the domain variables in case the environment variable is set.
 * Can be overwritten by config.inc.php, but eases the configuration and update procedure
 * when the variable is set in the apache configuration for the sitemgr directory.
 */
if (($domain = getenv('EGW_SITEMGR_DOMAIN')) !== False) {
	$GLOBALS['egw_info']['user']['domain'] =
		$GLOBALS['egw_info']['server']['default_domain'] =
		$domain;
	unset($domain);
}

include('./config.inc.php');
if (!file_exists($sitemgr_info['egw_path'] . 'header.inc.php'))
{
	die("Header file not found.  Either your path to eGroupWare in the config.inc.php file is bad, or you have not setup eGroupWare.");
}
// do we use a different domain and are already loged in?
require_once($sitemgr_info['egw_path'].'api/src/Session.php');
if (isset($GLOBALS['egw_info']['server']['default_domain']) &&
	Api\Session::get_request('domain') != $GLOBALS['egw_info']['server']['default_domain'])
{
	// force our default domain
	$_GET['domain'] = $_COOKIE['domain'] = $_REQUEST['domain'] = $GLOBALS['egw_info']['server']['default_domain'];
	unset($_GET['sessionid']);
	unset($_COOKIE['sessionid']);
	unset($_REQUEST['sessionid']);
}
include($sitemgr_info['egw_path'] . 'header.inc.php');

if ($sitemgr_info['webserver_url'])
{
	$GLOBALS['egw_info']['server']['webserver_url'] = $sitemgr_info['webserver_url'];
}
if (!$site_id)
{
	sitemgr_get_site($anon_account);
}

// switch to current website.
if ($GLOBALS['egw_info']['user']['preferences']['sitemgr']['currentsite'] != $site_id)
{
	$GLOBALS['egw_info']['user']['preferences']['sitemgr']['currentsite'] = $site_id;
	$GLOBALS['egw']->preferences->add('sitemgr','currentsite', $site_id);
	$GLOBALS['egw']->preferences->save_repository(True);
}

if ($GLOBALS['egw_info']['server']['usecookies'] && $_COOKIE['sessionid'] != $GLOBALS['egw_info']['user']['sessionid'] &&
	(!$GLOBALS['egw_info']['server']['cookiedomain'] || // set SiteMgr cookie only if eGW's cookiedomain does not fit
	substr($_SERVER['SERVER_NAME'],-strlen($GLOBALS['egw_info']['server']['cookiedomain'])) != $GLOBALS['egw_info']['server']['cookiedomain']))
{
	if (count(explode('.',$domain = $_SERVER['SERVER_NAME'])) <= 1) $domain = '';
	// we dont sue session::egw_setcookie() as it would set the domain and path of the eGW install and not the one from sitemgr
	setcookie('sessionid',$GLOBALS['egw_info']['user']['sessionid'],0,'/',$domain);
	setcookie('kp3',$GLOBALS['egw_info']['user']['kp3'],0,'/',$domain);
	setcookie('domain',$GLOBALS['egw_info']['user']['domain'],0,'/',$domain);
}
include('./functions.inc.php');

require_once './inc/class.site_controler.inc.php';
$site_controler = new site_controler();
$site_controler->processRequest();
