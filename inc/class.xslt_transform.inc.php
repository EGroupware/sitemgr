<?php 

class xslt_transform
{
	var $arguments;

	function xslt_transform($xsltfile)
	{
		$this->xsltfile = $xsltfile;
	}

	function apply_transform($title,$content)
	{
		$xh = xslt_create();
		$xsltarguments = array('/_xml' => $content);
		$result = xslt_process($xh, 'arg:/_xml', $this->xsltfile, NULL, $xsltarguments);
		xslt_free($xh);
		return $result;
	}
}
