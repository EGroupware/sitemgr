<?php 

class module_amazon extends Module
{
	function module_amazon()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = "Amazon";
		$this->description = "Use this module for displaying book ads for the amazon web site ";
	}

	function get_content(&$arguments,$properties)
	{
		$amazon_id = "phpgwsitemgr-20";

		mt_srand((double)microtime()*1000000);
		$imgs = dir('images/amazon');
		while ($file = $imgs->read()) {
			if (eregi("gif", $file) || eregi("jpg", $file)) {
			$imglist .= "$file ";
			}
		}
		closedir($imgs->handle);
		$imglist = explode(" ", $imglist);
		$a = sizeof($imglist)-2;
		$random = mt_rand(0, $a);
		$image = $imglist[$random];
		$asin = explode(".", $image);
		$content = "<br><center><a href=\"http://www.amazon.com/exec/obidos/ASIN/$asin[0]/$amazon_id\" target=\"_blank\">";
		$content .= "<img src=\"images/amazon/$image\" border=\"0\" alt=\"\"><br><br></center>";
	}

}