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
		parent::__construct();
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
				'label' => lang('Index')
			),
			'css' => array(
				'type' => 'textarea',
				'label' => lang('CSS classes').':<br />'.
					lang('Slideshow').': nivoSlider<br/>'.
					lang('Caption').': nivo-caption<br />'.
					lang('Index').': nivo-controlNav, nivo-control, active<br />',
					// These two lines added later, when block->id is available
					/*
					($this->block->id ? 'Div ID: #slider'.$this->block->id : '').
					lang('Custom CSS:'),
					*/
				'large' => true,
				'params' => array('cols' => 100, 'rows' => 8),
			),
			'transitions' => array(
				'type' => 'select',
				'label' => lang('transitions'),
				'multiple' => 5,
				'default' => array('fade'),
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
		$values = $this->block->arguments;

		// Add in block unique ID for user reference
		$this->arguments['css']['label'] .=
			($this->block->id ? 'Div ID: #slider'.$this->block->id.'<br />' : '').
			lang('Custom CSS:');
		// Some default CSS stuff - snippets for various effects
		if(!$values['css'])
		{
			$this->arguments['css']['default'] = "
/* Index below images */
#slider{$this->block->id} .nivoSlider {
	margin-bottom: 30px;
}
#slider{$this->block->id} .nivo-controlNav {
	bottom: -25px;
}

/* Index inside images
#slider{$this->block->id} .nivo-controlNav {
	bottom: -5px;
}
#slider{$this->block->id} .nivo-caption p {
	padding-bottom: 15px;
}
*/

/* Numeric index
#slider{$this->block->id} .nivo-control {
	background: inherit;
	text-indent: 0px;
}
*/

/* Caption on right
#slider{$this->block->id} .nivo-caption {
	left: inherit;
	right: 0px;
	height: 100%;
	width: 40%;
	border-left: 1ex solid silver;
}
#slider{$this->block->id} .nivo-caption p {
	height: 100%;
	padding: 20px 60px 20px 20px;
}
*/
";
		}
		$interface = parent::get_user_interface();

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

			if(count($ls_dir) == 0) {
				$interface[] = array(
					'label' => '<b>'.lang('No images found').'</b><hr/>',
					'large' => true,
					'form' => ''
				);
				return $interface;
			}
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
					html::image('',egw::link('/etemplate/thumbnail.php',array('path'=>$file['path']))) . '<br />'.$file['name'],
					html::checkbox("element[{$this->block->version}][i18n][images][{$path}][include]", $file['include']),
					html::fckEditor("element[{$this->block->version}][i18n][images][{$path}][caption]",$file['caption'],
						'advanced', array('toolbar_expanded' =>'false'), '100px'),
					html::input("element[{$this->block->version}][i18n][images][{$path}][link]",$file['link']),
					html::input("element[{$this->block->version}][i18n][images][{$path}][order]",$file['order'],'','size="3"')
				);
			}
			$interface[] = array(
				'label' => '<b>'.lang('Images').'</b><hr/>',
				'large' => true,
				'form' => html::table($table, 'width="100%"')
			);

		}
		return $interface;
	}
	function get_content(&$arguments,$properties)
	{
		$div_id = 'slider'.$this->block->id;

		// Get files
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
			if(!$arguments['images'][$path]) $arguments['images'][$path] = array('include'=>false);
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
			$sort = array_flip($sort);
			shuffle($sort);
		}
		$ls_dir = array_values($ls_dir);
		array_multisort($sort, SORT_ASC, $ls_dir);

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
		$base_url = $GLOBALS['egw_info']['server']['webserver_url'].'/sitemgr/modules/nivo-slider';
		$html = '<script type="text/javascript">
	var fileref = document.createElement("script");
	fileref.setAttribute("type", "text/javascript");
	fileref.setAttribute("src", "'.$base_url.'/jquery.nivo.slider.js'.'");
	document.getElementsByTagName("head")[0].appendChild(fileref);

	// Required layout, basic styles
	var fileref = document.createElement("link");
	fileref.setAttribute("rel", "stylesheet");
	fileref.setAttribute("type", "text/css");
	fileref.setAttribute("href", "'.$base_url.'/nivo-slider.css'.'");
	document.getElementsByTagName("head")[0].appendChild(fileref);

	// Make it look a little nicer
	var styleref = document.createElement("link");
	styleref.setAttribute("rel", "stylesheet");
	styleref.setAttribute("type", "text/css");
	styleref.setAttribute("href", "'.$base_url.'/themes/default/default.css'.'");
	document.getElementsByTagName("head")[0].appendChild(styleref);
	
	var style = document.createElement("style");
	style.setAttribute("type", "text/css");';
	$customCSS = '#'.$div_id.'.nivoSlider {'.
		($arguments['width'] ? 'width:' .$arguments['width'] .'px;' : '').
 		($arguments['height']? 'height:'.$arguments['height'].'px;' : '').
		'}
		.nivoSlider a {
			padding: 0px !important;
		} '.htmlentities($arguments['css']);
	$html .= '  style.setHTML("'.str_replace(array("\n","\r")," ",$customCSS).'");
	document.getElementsByTagName("head")[0].appendChild(style);
</script>
';
		// Needed for JS
		$arguments['class'] .= ' nivoSlider theme-default';

		$html .= '<div id="'.$div_id.'" ';
		foreach(array('class') as $option)
		{
			if ($arguments[$option]) $html .= ' '.$option.'="'.htmlspecialchars($arguments[$option]). ($option !='class' ? 'px':'').'"';
		}

		$html .= ">\n";

		// No images?
		if(count($ls_dir) == 0) return $html;

		foreach($ls_dir as &$file)
		{
			$path = $file['path'];
			if($file['link']) $html .= '<a href="' . $file['link'] . '">';
			$url = egw_vfs::download_url($path);
			// only use egw_link, if url is not yet a full url, eg. filesystem stream-wrapper can set a direct download url!
			if ($url[0] == '/') $url = egw::link($url);
			$html .= "\t".'<img src="'.htmlspecialchars($url).
				($file['caption'] ? '" title="#'.$div_id.'_'.$i++ : '').
				'" alt="'.$file['caption'].'" />'."\n";
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
		$html .= '
		<script type="text/javascript">
jQuery(document).ready(function() {
jQuery("#'.$div_id.'").nivoSlider({
	animSpeed: 500, // Slide transition speed
        pauseTime: '.($arguments['slide_time'] ? $arguments['slide_time'] * 1000 : 3000) .', // How long each slide will show
	prevText: "'.lang('Prev').'",
	nextText: "'.lang('Next').'",
	controlNav: '.($arguments['controlNav'] ? 'true' : 'false').',
	effect: "'.($arguments['transitions'] ? implode(',',$arguments['transitions']) : 'random').'"
});
});
</script>';
		return $html;
	}
}
