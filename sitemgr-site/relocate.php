<?php

// Edit the two lines below to suit your needs.  
// Keep the trailing slash.  Don't add an http://machinename/
// to the URI.  
// -mr_e
$base_script_uri = "/url/to/sitemgr-site/";
$base_script_dir = "/filesystem/path/to/sitemgr-site/";

// Leave the rest of the file alone unless you
// really know what you're doing.

function localpassthru($u)
{
	$server = $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'];
	$fp = fsockopen($server, $GLOBALS['server_port']);
	if ($fp)
	{
		fputs ($fp, "GET $u HTTP/1.0\r\nHost: $server\r\n\r\n");
		$input = '';
		while (!feof($fp)) 
		{
			$input .= fread($fp,128);
		}
		$input=trim($input);
		$output = explode("\r\n\r\n",$input,2);
		$headers = explode("\r\n",$output[0]);
		foreach($headers as $header)
		{
			header($header);
		}
		echo ($output[1]);

		fclose ($fp);
	}
	else
	{
		echo "ERROR";
	}
}

function status($i)
{
	switch($i)
	{
		case 200:
			$msg = '200 OK';
			break;
		case 404:
			$msg = '404 Not Found';
			break;
	}
	if ($HTTP_SERVER_VARS["SERVER_PROTOCOL"]="HTTP/1.1") 
	{
		header("HTTP/1.1 $msg");
	} 
	else 
	{
		header("Status: $msg");
	}
}

$server_port = $HTTP_SERVER_VARS['SERVER_PORT'];

if (isset($HTTP_SERVER_VARS['HTTPS']))
{
	$base_script_server = 'https://'.$HTTP_SERVER_VARS['SERVER_NAME'].':'.$server_port;
}
else
{
	$base_script_server = 'http://'.$HTTP_SERVER_VARS['SERVER_NAME'].':'.$server_port;
}

$r = $HTTP_SERVER_VARS["REQUEST_URI"];
$r_doc = substr($r,strlen($base_script_uri));

$url_array = explode("/", $r_doc);

$section = array_shift($url_array);

$r_doc = implode('/',$url_array);

if (substr($r_doc,0,1)=='/') 
{
	//we have a full path... check it out
	$f = $base_script_dir . substr($r,1);
	$u = $base_script_server . substr($r,1);
} 
else 
{
	//we have a relative path
	$f = $base_script_dir . $r_doc;
	$u = $base_script_uri . $r_doc;
}

	//echo("f=$f and u=$u and r_doc=$r_doc and section=$section");

if (file_exists( $f ) && is_file( $f )) 
{
	localpassthru($u);
}
else 
{
	$display_page = 0;
	switch($url_array[0])
	{
		case '':
			$display_page = 1;
			break;
		case 'themes':
		case 'images':
		case 'templates':
		default:
			status(404);
			echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
			<HTML><HEAD>
			<TITLE>404 Not Found</TITLE>
			</HEAD><BODY>
			<H1>Not Found</H1>
			The requested URL '.$r.' was not found on this server.<P>
			</BODY></HTML>';
			break;
	}
	if ($display_page)
	{
		status(200);
		$GLOBALS['page_name'] = $section;
		include $base_script_dir . "/index.php";
	}

}
?>

