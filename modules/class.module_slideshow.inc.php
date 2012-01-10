<?php
/**
 * EGroupware SiteMgr - HTML5 image slideshow with CSS3 transitions
 *
 * @link http://www.egroupware.org
 * @author Nathan Gray
 * @package sitemgr
 * @subpackage modules
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * HTML5 image slideshow.
 * Uses Flux Slider by Joe Lambert for the good stuff.
 * http://www.joelambert.co.uk/flux/
 */
class module_slideshow extends Module
{
	function __construct()
	{
		$this->i18n = true;
		$this->arguments = array(
			'width' => array(
				'type' => 'textfield',
				'label' => lang('Width'),
				'default' => 400,
			),
			'height' => array(
				'type' => 'textfield',
				'label' => lang('Height'),
				'default' => 300,
			),
			'slide_time' => array(
				'type' => 'textfield',
				'label' => lang('Slide display time (s)'),
				'default' => 3
			),
			'random' => array(
				'type' => 'checkbox',
				'label' => lang('Random order'),
				'default' => false
			),
			'controlNav' => array(
				'type' => 'checkbox',
				'label' => lang('Index'),
				'default' => true
			),
			'class' => array(
				'type' => 'textfield',
				'label' => 'CSS classes:<br />'.
					lang('Slideshow').': nivoSlider<br/>'.
					lang('Caption').': nivo-caption<br />'.
					lang('Index').': nivo-controlNav, nivo-control, active<br />'.
					lang('Additional CSS class').':<br/>',
				'large' => true,
				'i18n' => true,

			),
			'transitions' => array(
				'type' => 'select',
				'label' => lang('transitions'),
				'multiple' => 5,
				'options' => array(
					'random' => lang('Random'),
					'sliceDown'=>'sliceDown',
					'sliceDownLeft'=>'sliceDownLeft',
					'sliceUp'=>'sliceUp',
					'sliceUpLeft'=>'sliceUpLeft',
					'sliceUpDown'=>'sliceUpDown',
					'sliceUpDownLeft'=>'sliceUpDownLeft',
					'fold'=>'fold',
					'fade'=>'fade',
					'slideInRight'=>'slideInRight',
					'slideInLeft'=>'slideInLeft',
					'boxRandom'=>'boxRandom',
					'boxRain'=>	'boxRain',
					'boxRainReverse'=>	'boxRainReverse',
					'boxRainGrow'=>	'boxRainGrow',
					'boxRainGrowReverse'=>	'boxRainGrowReverse'
				),
			),
			'image_dir' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'i18n' => true,
				'label' => lang('VFS Image directory (required)')
			),
		);
		$this->title = lang('HTML5 slideshow module');
		$this->description = lang('This module lets you create a slideshow.');
	}

	function get_user_interface()
	{
		$interface = parent::get_user_interface();
		$values = $this->block->arguments;
		if (!egw_vfs::file_exists($values['image_dir']) || !egw_vfs::is_readable($values['image_dir'] || !is_dir($values['image_dir'])))
		{
			$interface[] = array(
				'label' => lang('Image directory is not set'),
				'large' => true
			);
		}
		else
		{
			$ls_dir = egw_vfs::find($values['image_dir'],array(
				'need_mime' => true,
				'maxdepth' => 1,
				'type' => 'f'
			),true);

			$table = array(
				'h1'	=> array(
					lang('Image'),
					lang('Include'),
					lang('Caption'),
					lang('Link'),
					lang('Order'),
				)
			);
			$sort = array();
			foreach($ls_dir as $path => &$file)
			{
				// Add data from block
				if($values['images'][$path]) $file += $values['images'][$path];
				$sort[$path] = $file['order'];
			}
			array_multisort($sort, SORT_ASC, $ls_dir);
			foreach($ls_dir as $path => &$file)
			{
				$table[] = array(
					html::image('',egw::link('/etemplate/thumbnail.php',array('path'=>$file['path']))),
					html::checkbox("element[{$this->block->version}][i18n][images][{$path}][include]", $file['include']),
					html::fckEditor("element[{$this->block->version}][i18n][images][{$path}][caption]",$file['caption'],
						'simple', array('toolbar_expanded' =>'false'), '100px'),
					html::input("element[{$this->block->version}][i18n][images][{$path}][link]",$file['link']),
					html::input("element[{$this->block->version}][i18n][images][{$path}][order]",$file['order'],'','size="3"')
				);
			}
			$interface[] = array(
				'label' => '<b>'.lang('Images').'</b><hr/>',
				'large' => true,
				'form' => html::table($table)
			);

		}
		return $interface;
	}
	function get_content(&$arguments,$properties)
	{
		$div_id = 'slider'.$this->block->id;
/* Flux slider
		$html = '<script src="'.$GLOBALS['sitemgr_info']['site_url'].'../modules/joelambert-Flux-Slider-bf5d327/js/flux.min.js'.'"></script>
		<script type="text/javascript">
jQuery(document).ready(function() {
	if(!flux.browser.supportsTransitions)
					alert("Flux Slider requires a browser that supports CSS3 transitions");
				
				window.f = new flux.slider("#slider", {';
		$options = array();
		foreach($arguments['options'] as $option)
		{
			$options[] = "$option: true";
		}
		foreach(array('width', 'height') as $option)
		{
			if($arguments[$option]) $options[] = "$option: {$arguments[$option]}";
		}
		
		if($options) $html .= implode(', ', $options);
		$html .= '});

});</script>';
*/

		// Nivo slider
		$html = '<script src="'.$GLOBALS['sitemgr_info']['site_url'].'../modules/nivo-slider/jquery.nivo.slider.js'.'"></script>

		<!-- Required layout, basic styles -->
		<link rel="stylesheet" href="'.$GLOBALS['sitemgr_info']['site_url'].'../modules/nivo-slider/nivo-slider.css'.'" type="text/css" media="screen" />
		<!-- Make it look a little nicer -->
		<link rel="stylesheet" href="'.$GLOBALS['sitemgr_info']['site_url'].'../modules/nivo-slider/themes/default/default.css'.'" type="text/css" media="screen" />

		<style>
			.nivoSlider {';
				
		if($arguments['width']) $html .= 'width:'.$arguments['width'] .'px;';
 		if($arguments['height']) $html .= 'height:'.$arguments['height'].'px;';
		$html .= '
			}
		</style>';
_debug_array($arguments);
		$html .= '
		<script type="text/javascript">
jQuery(document).ready(function() {
jQuery("#'.$div_id.'").nivoSlider({
	animSpeed: 500, // Slide transition speed
        pauseTime: '.($arguments['slide_time'] ? $arguments['slide_time'] * 1000 : 3000) .', // How long each slide will show
	prevText: "'.lang('Prev').'",
	nextText: "'.lang('Next').'",
	controlNav: '.($arguments['controlNav'] ? 'true' : 'false').',
	effect: "'.implode(',',$arguments['transitions']).'"
});
});
</script>';

		// Needed for JS
		$arguments['class'] .= ' nivoSlider theme-default';

		$html .= '<div id="'.$div_id.'" ';
		foreach(array('width', 'height', 'class') as $option)
		{
			if ($arguments[$option]) $html .= ' '.$option.'="'.htmlspecialchars($arguments[$option]). ($option !='class' ? 'px':'').'"';
		}
		
		$html .= ">\n";
		

		$ls_dir = egw_vfs::find($arguments['image_dir'],array(
			'need_mime' => true,
			'maxdepth' => 1,
			'type' => 'f'
		),true);
		$props = egw_vfs::propfind(array_keys($ls_dir));

		$i = 0; // Used for IDs

		$sort = array();
		foreach($ls_dir as $path => &$file)
		{
			// Add data from block
			$file += $arguments['images'][$path];
			if($file['include'])
			{
				$sort[$path] = $file['order'];
			}
			else
			{
				unset($ls_dir[$path]);
			}
		}
		if($arguments['random'])
		{
			$sort = array_flip(shuffle(array_flip($sort)));
		}
		array_multisort($sort, SORT_ASC, $ls_dir);
		foreach($ls_dir as $path => &$file)
		{
			if($file['link']) $html .= '<a href="' . $file['link'] . '">';
			$html .= "\t".'<img src="'.htmlspecialchars(egw::link(egw_vfs::download_url($path))).($file['caption'] ? '" title="#'.$div_id.'_'.$i++ : '').'" />'."\n";
			if($file['link']) $html .= '</a>';
		}
		$html .= '</div>';

		// Add captions (assume all are HTML)
		$i = 0;
		foreach($ls_dir as $path => &$file)
		{
			if(!$file['caption']) continue;
			$html .= "<div id='{$div_id}_".$i++."' class='nivo-html-caption'>\n";
			$html .= $file['caption']."\n";
			$html .= '</div>';
		}
		return $html;
	}
}
