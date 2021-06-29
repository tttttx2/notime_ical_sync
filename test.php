<?php


include 'ICS.php';

header('Content-Type: text/calendar; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Disposition: attachment; filename=calendar.ics');


$content = array();

$content[]=array(
  'location' => "loc1",
  'description' => "desc1",
  'dtstart' => '2021-06-29T14:03:01.012345Z',
  'dtend' => '2021-06-29T15:03:01.012345Z',
  'summary' => "summ1",
  'url' => "url1"
);

$content[]=array(
  'location' => "loc2",
  'description' => "desc2",
  'dtstart' => '2021-06-29T16:03:01.012345Z',
  'dtend' => '2021-06-29T17:03:01.012345Z',
  'summary' => "summ2",
  'url' => "url2"
);

$content[]=array(
  'location' => "loc3",
  'description' => "desc3",
  'dtstart' => '2021-06-29T16:03:01.012345Z',
  'dtend' => '2021-06-29T18:03:01.012345Z',
  'summary' => "summ3",
  'url' => "url3"
);




$ics = new ICS($content);
echo $ics->to_string();
