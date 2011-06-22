<?php
/**
 * EGroupware SiteMgr - HTML5 video module with Flash fallback
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker(at)outdoor-training.de>
 * @package sitemgr
 * @subpackage modules
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * HTML5 video module with Flash fallback
 */
class module_video extends Module
{
	function __construct()
	{
		$this->arguments = array(
			'mp4_url' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('MP4 URL (required)')
			),
			'webm_url' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('WebM URL')
			),
			'ogg_url' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('Ogg URL')
			),
			'poster' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('Poster URL')
			),
			'fallback' => array(
				'type' => 'textfield',
				'params' => array('size' => 100),
				'label' => lang('Fallback title')
			),
			'options' => array(
				'type' => 'select',
				'multiple' => 5,
				'label' => lang('Options'),
				'options' => array(
					'controls' => lang('controls'),
					'autoplay' => lang('autoplay'),
					'loop'     => lang('loop video'),
					'download' => lang('extra download'),
					'no_flash' => lang('no flash fallback'),
					'showhtml' => lang('show html markup'),
				),
			),
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
			'class' => array(
				'type' => 'textfield',
				'label' => 'CSS class',
			),
			'preload' => array(
				'type' => 'select',
				'label' => lang('Preload'),
				'options' => array(
					'' => lang('Not set'),
					'auto' => lang('Auto'),
					'metadata' => lang('Metadata'),
					'none' => lang('None'),
				),
			),
		);
		$this->title = lang('HTML5 video module with Flash fallback');
		$this->description = lang('This module lets you playback videos.');
	}

	function get_content(&$arguments,$properties)
	{
		$html = '<video';
		foreach(array('autoplay', 'controls', 'loop') as $option)
		{
			if (in_array($option, $arguments['options'])) $html .= ' '.$option.'="'.$option.'"';
		}
		foreach(array('poster', 'width', 'height', 'class', 'preload') as $option)
		{
			if ($arguments[$option]) $html .= ' '.$option.'="'.htmlspecialchars($arguments[$option]).'"';
		}
		$html .= "/>\n";
		foreach(array('mp4_url' => 'video/mp4', 'webm_url' => 'video/webm', 'ogg_url' => 'video/ogg') as $url => $mime)
		{
			if ($arguments[$url])
			{
				$html .= "\t".'<source src="'.htmlspecialchars($arguments[$url]).'" type="'.$mime.'" />'."\n";
				$download .= ($download?', ':'').html::a_href(ucfirst(substr($url,0,-4)), $arguments[$url]);
			}
		}
		// force firefox/opera to fallback to flash, if no webm or ogg url given, by not giving a html5 video tag,
		// as unfortunately it does not fallback automatically, if one is given but no supported video format
		if (in_array(html::$user_agent,array('firefox','opera')) && !$arguments['webm_url'] && !$arguments['ogg_url'])
		{
			$html = '';
			if (($key = array_search('no_flash',$arguments['options'])) !== false) unset($arguments['options'][$key]);
		}
		if (!in_array('no_flash',$arguments['options']) && $arguments['mp4_url'])
		{
			$html .= "\t".'<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" width="'.
				htmlspecialchars($arguments['width']).'" height="'.htmlspecialchars($arguments['height']).'">'."\n";
			$html .= "\t\t".'<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />'."\n";
			$html .= "\t\t".'<param name="allowFullScreen" value="true" />'."\n";
			$html .= "\t\t".'<param name="wmode" value="transparent" />'."\n";
			$html .= "\t\t".'<param name="flashVars" value="config={'."'playlist':[".($arguments['poster'] ? "'".htmlspecialchars($arguments['poster'])."'," : '').
				"{'url':'".htmlspecialchars($arguments['mp4_url'])."', 'autoPlay':".(in_array('autoplay', $arguments['options']) ? 'true' : 'false').'}]}" />'."\n";

			if ($arguments['poster'])
			{
				$html .= "\t\t".'<img alt="'.html::htmlspecialchars($arguments['fallback']).'" src="'.htmlspecialchars($arguments['poster']).
					'" width="'.htmlspecialchars($arguments['width']).'" height="'.htmlspecialchars($arguments['height']).
					'" title="'.html::htmlspecialchars(lang('No video playback capabilities').
					(in_array('download', $arguments['options']) ? ', '.lang('please download the video') : '')).'" />'."\n";
			}
			else
			{
				$html .= "\t\t<p>".html::htmlspecialchars($arguments['fallback'])."</p>\n";
			}
			$html .= "\t</object>\n";
		}
		if (!in_array(html::$user_agent,array('firefox','opera')) || $arguments['webm_url'] || $arguments['ogg_url'])
		{
			$html .= "</video>\n";
		}
		if(in_array('download', $arguments['options']) && $download)
		{
			$html .= '<p>'.lang('Download').': '.$download."</p>\n";
		}

		if (!$download)
		{
			return lang('No video URL specified!');
		}

		if (in_array('showhtml', $arguments['options']))
		{
			$html = "<pre>".htmlspecialchars($html)."</pre>\n";
		}
		return $html;
	}
}
