<?php
/**
 * EGroupware SiteMgr - Accordion
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker(at)outdoor-training.de>
 * @package sitemgr
 * @subpackage modules
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * Accordion: clickable headers show otherwise hidden content
 */
class module_accordion extends Module
{
	function __construct()
	{
		$this->i18n = true;
		$this->arguments = array(
			'num_groups' => array(
				'type' => 'select',
				'label' => lang('Number of groups'),
				'options' => array(),
				'params' => array('onChange' => 'this.form.submit();'),
			),
			'mode' => array(
				'type' => 'select',
				'label' => lang('Opening mode'),
				'options' => array(
					'linked_first' => lang('Initially first group open').', '.lang('groups linked: opening one closes others'),
					'independent_first' => lang('Initially first group open').', '.lang('groups open/close independent'),
					'linked' => lang('Initially all closed').', '.lang('groups linked: opening one closes others'),
					'independent' => lang('Initially all closed').', '.lang('groups open/close independent'),
				),
			),
			'header_1' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('%1. group', 1),
			),
			'content_1' => array(
				'type' => 'htmlarea',
				'label' => '',
				'large' => True,	// show label above content
				'i18n' => True,
			)
		);
		for ($n = 1; $n <= 20; ++$n) $this->arguments['num_groups']['options'][$n] = $n;
		$this->title = lang('Accordion');
		$this->description = lang('This module shows an accordion: clickable headers show otherwise hidden content.');
	}

	/**
	 * Render module content
	 *
	 * @see Module::get_content()
	 */
	function get_content(&$arguments,$properties)
	{
		static $first = true;

		$html = '<div class="accordion" id="accordion_'.$this->block->id.'">'."\n";
		for($n = 1; $n <= $arguments['num_groups']; ++$n)
		{
			if (empty($arguments['header_'.$n])) continue;

			$html .= '<div class="accordion-group">'."\n";
			$html .= '	<div class="accordion-header"><a class="accordion-toggle" href="#">'.htmlspecialchars($arguments['header_'.$n])."</a></div>\n";
			$html .= '	<div class="accordion-content'.
				(strpos($arguments['mode'],'linked') !== false ? '-linked' : '').'"'.
				(strpos($arguments['mode'],'first') !== false && $n==1 ? 'style="display:block"' : '').'>'.
				$arguments['content_'.$n]."</div>\n";
			$html .= "</div>\n";
		}
		$html .= "</div>\n";

		if ($first)
		{
			$html .= '<script type="text/javascript">
egw_LAB.wait(function() {
	jQuery(function() {
		jQuery(".accordion-toggle").click(function() {
			jQuery(this).parent().parent().parent().find("div.accordion-content-linked").slideUp();
			jQuery(this).parent().next().slideToggle(function() {';
			if(strpos($arguments['mode'],'linked') !== false)
			{
				$html .= '
				// Slide into view
				jQuery("html, body").animate({
					scrollTop: jQuery(this).parent().offset().top
				});';
			}
			$html .= '
			});
			return false;
		});
	});
});
</script>
';
			$first = false;
		}
		return $html;
	}

	/**
	 * Generate user-interface, reimplemented to add N groups and react on changed number of groups
	 *
	 * @see Module::get_user_interface()
	 */
	function get_user_interface()
	{
		if (isset($_POST['element'][$this->block->id]['num_groups']))
		{
			$this->block->arguments['num_groups'] = (int)$_POST['element'][$this->block->id]['num_groups'];
		}
		for($n = 2; $n <= $this->block->arguments['num_groups']; ++$n)
		{
			$this->arguments['header_'.$n] = $this->arguments['header_1'];
			$this->arguments['header_'.$n]['label'] = lang('%1. group', $n);
			$this->arguments['content_'.$n] = $this->arguments['content_1'];
		}
		return parent::get_user_interface();
	}
}
