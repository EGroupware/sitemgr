<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/* 									*/
/* Yahoo-Stocks  Version 1.0 by Venkat Addanki (kumarvja@yahoo.com)     */
/* http://www.geocities.com/kumarvja					*/
/* PHP-Nuke 5.4 Blocks							*/
/* To Display Major Indices in a Block					*/
/* Copy this file to blocks directory					*/
/* Add a new block from the administration menu and select block-yahoo	*/
/************************************************************************/

/*****************************************************************
 Here is the information for developers to customize

	symbol 			<-- $data[0],
	company 		<-- $data[1],
	lastprice 		<-- $data[2],
	tradedate 		<-- $data[3],
	tradetime 		<-- $data[4],
	change 			<-- $data[5],
	changepercent 	<-- $data[6],
	volume 			<-- $data[7],
	avgvolume 		<-- $data[8],
	bid 			<-- $data[9],
	ask 			<-- $data[10],
	yesterdaysclose <-- $data[11],
	open 			<-- $data[12],
	dayrange 		<-- $data[13],
	yearrange 		<-- $data[14],
	earnpershare 	<-- $data[15],
	pe 				<-- $data[16],
	divdate 		<-- $data[17],
	yield 			<-- $data[18],
	divshr 			<-- $data[19],
	marketcap 		<-- $data[20]

********************************************************************/


if (eregi("block-ystock.php",$PHP_SELF)) {
    Header("Location: index.php");
    die();
}

$mycontent = "<table width=0 cellpadding=0 cellspacing=1 border=0>";
$mycontent .= "<tr><td><b>Index</b></td><td><b>Value</b></td></tr>";
$allsymbols="^DJI+^IXIC+^SPC+^TV.N+^TV.O";
$file = fopen("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=$allsymbols","r");

while ($data = fgetcsv($file,4096, ",")) {
    $mycontent .= "<tr><td>" . $data[1] . "</td><td>" . $data[2] . "</td></tr>";
}

fclose($file);
$mycontent .= "</table>";

$content = $mycontent;

?>


