<?php
/**
 * EGroupware SiteMgr CMS - native template support
 *
 * @link http://www.egroupware.org
 * @package sitemgr
 * @subpackage sitemgr-site
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL2+ - GNU General Public License version 2, or (at your option) any later version
 * @copyright Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @version $Id$
 */

class ui implements site_renderer
{
	/**
	 * @var Template3
	 */
	var $t;

	function __construct()
	{
		$themesel = $GLOBALS['sitemgr_info']['themesel'];
		if ($themesel[0] == '/')
		{
			$templateroot = $GLOBALS['egw_info']['server']['files_dir'] . $themesel;
		}
		else
		{
			$templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
		}
		$this->t = new Template3($templateroot);
	}

	function generatePage()
	{
		// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
		header('Content-type: text/html; charset='.$GLOBALS['egw']->translation->charset());

		echo $this->t->parse();
	}
}
