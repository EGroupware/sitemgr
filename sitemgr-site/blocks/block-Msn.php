<?
 if (eregi("block-Msn.php",$PHP_SELF)) {
      Header("Location: index.php");
      die();
 }

 function Haber() {
  $result ="";
  if (!$kategori) $kategori = array("News","Web Exclusives","More Business and Money","More International News");

  if (!$font_face) $font_face = "Verdana";
  if (!header_size) $header_size = "3";
  if (!$font_size) $font_size = "2";
  if (!$font_color) $font_color = "#000000";
  if (!$list_color) $list_color = "#00AA00";

  $satir = file("http://www.msnbc.com/news/BCList2.txt") or die("Serverla Ýletiþim Saðlanamadý!");

  for ($x = 0; $x < sizeof($kategori); $x++)
  {
   $result .="<font color=\"$font_color\" size=\"$header_size\"><b><p>$kategori[$x]</p></b></font>\n"
   		   ."<font color=\"$list_color\" size=\"$font_size\">\n";

   for ($i = 0; $i < sizeof($satir); $i++) {
    if (trim($satir[$i]) == "+$kategori[$x]") {
     $bas = $i + 1;
     break;
    }
   }

   $son = $bas + 5;

   for ($i = $bas; $i < $son; $i++) {
    $yazi = trim($satir[$i]);

    if ($yazi != "-" && !eregi("$kategori[$x] Ön Sayfa", $yazi)) {
     $yazi = str_replace("/news/./", "http://www.msnbc.com/news/",$yazi);
     $haber = explode("|", $yazi);
     $result .="<li><a href=\"$haber[0]\" target=\"_blank\">$haber[1]</a>\n";
    }
   }
   $result .="</font>\n";
  }
  return $result;
 }

 $content = Haber();
?>