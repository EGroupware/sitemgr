<?php 

class browser_transform
{
	function browser_transform($prevlink,$nextlink)
	{
		$this->prevlink = $prevlink;
		$this->nextlink = $nextlink;
	}

	function apply_transform($title,$content)
	{
		$result = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		$result .= $content;
		$result .= '<div align="center">';
		$result .= $this->prevlink;
		$result .= $this->nextlink;
		$result .= '</form></div>';
		return $result;
	}
}
