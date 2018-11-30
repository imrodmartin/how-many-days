<?php

// this sets up the days between two Dates

if(isset($_POST['submit'])  > 0)
{
	$sday= $_POST['bdayyear'].'-'.$_POST['bdaymonth'] .'-' .$_POST['bdayday'];
	$eday= $_POST['ddayyear'].'-'.$_POST['ddaymonth'] .'-' .$_POST['ddayday'];
//	echo $sday .' / ' .$eday;
$sdayfull = date("F jS, Y", strtotime($sday));
$edayfull = date("F jS, Y", strtotime($eday));

// sets up starting date and ending date
  $ndays = (strtotime($eday) - strtotime($sday)) / (60 * 60 * 24);
  $years = (strtotime($eday) - strtotime($sday)) / (60 * 60 * 24 * 365.25);
  $months = (strtotime($eday) - strtotime($sday)) / (60 * 60 * 24 * 365.25 / 12);
  $hours =  (strtotime($eday) - strtotime($sday)) / (60 * 60);
  $minutes = (strtotime($eday) - strtotime($sday)) / (60);
  $seconds = (strtotime($eday) - strtotime($sday));

  $ndays = number_format($ndays,0,'.',',');
  $years = number_format($years,2,'.',',');
  $hours = number_format($hours,0,'.',',');
  $months = number_format($months,0,'.',',');
  $minutes = number_format($minutes,0,'.',',');
  $seconds = number_format($seconds,0,'.',',');



  $contentdiv = '<div style="margin: 0px auto; width: 600px;"><div style="font-size: 20px;">There have been <b>' .$ndays .' </b>days <br />between ' .$sdayfull .' and ' .$edayfull .'</div>' .'<br /><br /></div>'
      . '<p style="font-size: 14px;"><b>That equals</b><br /> ' .$years .' years<br />' .$months .' months<br />' .$hours .' hours<br />' .$minutes .' minutes<br />' .$seconds .' seconds</p>';


}else{
	$contentdiv = '<div id="content">Please enter the start date and end date below.</div>';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Calculate the Days Between Two Dates</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript" src="calendar.js"></script>
</head>
<body id="main_body" >
	<?php echo $contentdiv; ?>
	<img id="top" src="top.png" style="height: 30px;" alt="">
	<div id="form_container">

		<h1><a>The Days Between</a></h1>
		<form id="form_551998" class="appnitro"  method="post" action="<?=$_SERVER['PHP_SELF']?>">
					<div class="form_description">
			<h2>The Days Between</h2>
			<p>Enter the start date and the end date to calculate the days between</p>
		</div>
			<ul >

			<li>

<li>
<?php
echo "Start Date:&nbsp;" .date_picker("bday");?>
</li>
<li>
<?php
echo "End Date:&nbsp;&nbsp; " .date_picker("dday")?>
</li>

					<li class="buttons">

				<input id="submit" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>
	</div>
	<img id="bottom" src="bottom.png" alt="">
	</body>
</html>

<?php



        date_default_timezone_set('UTC');

		function date_picker($name, $startyear=NULL, $endyear=NULL)
{
    if($startyear==NULL) $startyear = date("Y");
    if($endyear==NULL) $endyear=date("Y")-300;

    $months=array('','January','February','March','April','May',
    'June','July','August', 'September','October','November','December');

    // Month dropdown
    $html="<select name=\"".$name."month\">";

    for($i=1;$i<=12;$i++)
    {
       $html.="<option value='$i'>$months[$i]</option>";
    }
    $html.="</select> ";

    // Day dropdown
    $html.="<select name=\"".$name."day\">";
    for($i=1;$i<=31;$i++)
    {
       $html.="<option $selected value='$i'>$i</option>";
    }
    $html.="</select> ";

    // Year dropdown
    $html.="<select name=\"".$name."year\">";

    for($i=$startyear;$i>=$endyear;$i--)
    {
      $html.="<option value='$i'>$i</option>";
    }
    $html.="</select> ";

    return $html;
}
?>
