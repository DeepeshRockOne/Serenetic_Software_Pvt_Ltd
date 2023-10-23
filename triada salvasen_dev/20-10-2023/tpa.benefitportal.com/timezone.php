<?php
date_default_timezone_set("US/Eastern");
echo "EST:".date("Y-m-d H:i:s");
$pst=convert(date("Y-m-d H:i:s"),"EST","PST");
echo "<br>PST:".$pst;
echo "<br>EST:".convert($pst,"PST","EST");


function convert($curTime = null, $from = "EST", $to = "EST") {
  if (!isset($curTime)) {
    $curTime = date("Y-m-d h:i:s");
  }
  $d = new DateTime($curTime, new DateTimeZone($from));
  $d->setTimeZone(new DateTimeZone($to));
  return $d->format('Y-m-d h:i:s');
}
?>