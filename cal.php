<?php
// use composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// set default timezone (PHP 5.4)
date_default_timezone_set('Europe/London');

$vCalendar = new \Eluceo\iCal\Component\Calendar('CampusLondon');
$vCalendar->setName("Google Campus London Events");

$dom = pQuery::parseFile("https://www.campus.co/london/en/events");
foreach ($dom->query("._day li._item") as $elem) {

   $vEvent = new \Eluceo\iCal\Component\Event();

   // Time
   $time = $elem->query("._clock")->text();
   if (trim($time) == "") {
      continue;
   }
   $format = "l, F j Y h:i A";
   $calendar = $elem->query("._calendar")->html();

   $startTime = substr($time, 1, 1) == ":" ? substr($time, 0, 4) . " PM" : substr($time, 0, 5) . " AM";
   $dtStartTime = DateTime::createFromFormat($format, "$calendar 2015 $startTime");

   $endTime = substr($time, strpos($time, "–")+3);
   $dtEndTime = DateTime::createFromFormat($format, "$calendar 2015 $endTime");

   $vEvent->setDtStart($dtStartTime);
   $vEvent->setDtEnd($dtEndTime);

   // Title
   $title = $elem->query("h4")->html();
   $vEvent->setSummary($title);

   // Description
   $description = trim($elem->query(".-detail")->html());
   $description = preg_replace("/\s{2,}/", "", $description);
   $vEvent->setDescription($description);

   $vEvent->setUseTimezone(true);

   $vCalendar->addComponent($vEvent);
}
file_put_contents("/home1/cosmoses/www/dhilip/campus/cal.ics", $vCalendar->render());
