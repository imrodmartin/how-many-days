<?PHP
 
$date2 = date('m,d,Y');
 
$first_date = MKTIME(0,0,0,1,18,1961);
$second_date = MKTIME(0,0,0,$date2);
 
$offset = $second_date-$first_date;
 
?>
<?php $days =  FLOOR($offset/60/60/24);
$days = number_format($days);
echo $days . " days and counting";
 



?>