<?php
/**
 * EGroupware SiteMgr - Expires / Cache-Control header
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker(at)outdoor-training.de>
 * @package sitemgr
 * @subpackage modules
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * Expires / Cache-Control header
 *
 * Sets a from global value different time of Expires and Cache-Control header for all pages it is contained in.
 * It does NOT output any content.
 */
class module_expires extends Module
{
	function __construct()
	{
		$this->arguments = array(
			'max_age' => array(
				'type' => 'textfield',
				'params' => array('size' => 10),
				'label' => lang('Expires time (1 hour = 3600, 1 day = 86400, 1 week = 604800)', 1),
			),
		);
		$this->title = lang('Expires / Cache-Control');
		$this->description = lang('Sets a from global value different time of Expires and Cache-Control header for all pages it is contained in.');
	}

	/**
	 * Render module content
	 *
	 * @see Module::get_content()
	 */
	function get_content(&$arguments,$properties)
	{
		switch ($GLOBALS['sitemgr_info']['mode'])
		{
			case 'Production':
				if (is_numeric($arguments['max_age']))
				{
					egw_session::cache_control((int)$arguments['max_age']);
				}
				break;
			case 'Edit':
				if (!is_numeric($arguments['max_age']))
				{
					return lang('Expires time "%1" is NOT numeric!', $arguments['max_age']);
				}
				else
				{
					return lang('Expires time %1 seconds.', (int)$arguments['max_age']);
				}
				break;
		}
		return '';
	}
}
