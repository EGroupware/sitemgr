<?php
/*******************************************************\
* This file is for global functions needed by the       *
* sitemgr-site program.  This includes:                 *
*    - phpgw_link($url, $extravars)                     *
*    - sitemgr_link2($url, $extravars)                  *
\*******************************************************/

	function phpgw_link($url, $extravars = '')
	{
		return $GLOBALS['phpgw']->session->link($url, $extravars);
	} 

	function sitemgr_link2($url, $extravars = '')
	{
		$kp3 = $GLOBALS['HTTP_GET_VARS']['kp3'] ? $GLOBALS['HTTP_GET_VARS']['kp3'] : $GLOBALS['HTTP_COOKIE_VARS']['kp3'];

		if (! $kp3)
		{
			$kp3 = $GLOBALS['phpgw_info']['user']['kp3'];
		}


		$url = $GLOBALS['sitemgr_info']['sitemgr-site_url'] . $url;

		$url = ereg_replace('//','/',$url);

		// build the extravars string from a array
			
		if (is_array($extravars))
		{
			while(list($key,$value) = each($extravars))
			{
				if (!empty($new_extravars))
				{
					$new_extravars .= '&';
				}
				$new_extravars .= "$key=$value";
			}
			// This needs to be explictly reset to a string variable type for PHP3
			settype($extravars,'string');
			$extravars = $new_extravars;
		}
		if (isset($GLOBALS['phpgw_info']['server']['usecookies']) && $GLOBALS['phpgw_info']['server']['usecookies'])
		{
			if ($extravars)
			{
				$url .= '?' . $extravars;
			}
		}
		else
		{
			$sessionID  = 'sessionid=' . @$GLOBALS['phpgw_info']['user']['sessionid'];
			$sessionID .= '&kp3=' . $kp3;
			$sessionID .= '&domain=' . @$GLOBALS['phpgw_info']['user']['domain'];
			// This doesn't belong in the API.
			// Its up to the app to pass this value. (jengo)
			// Putting it into the app requires a massive number of updates in email app. 
			// Until that happens this needs to stay here (seek3r)
			if (isset($GLOBALS['phpgw_info']['flags']['newsmode']) && 
				$GLOBALS['phpgw_info']['flags']['newsmode'])
			{
				$url .= '&newsmode=on';
			}
			if ($extravars)
			{
				$url .= '?' . $extravars . '&' . $sessionID;
			}
			else
			{
				$url .= '?' . $sessionID;
			}
		}
		return $url;
	}
?>