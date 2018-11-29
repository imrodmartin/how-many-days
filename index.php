<?php

// main page for calculating how many days you've been alive
// created by Rod Martin - @imrodmartin
// testing git too... :)

        date_default_timezone_set('America/Detroit');

if(isset($_POST['submit'])  > 0)
{
	$sday= $_POST['bdayyear'].'-'.$_POST['bdaymonth'] .'-' .$_POST['bdayday'];
	$eday= date('Y-m-d');

  $ndays = (strtotime($eday) - strtotime($sday)) / (60 * 60 * 24);
  $ndays2 = $ndays+1;

  $two = 2000 - $ndays;
  $five = 5000 - $ndays;
  $ten = 10000 - $ndays;
  $nineteen = 19000 - $ndays;
  $twenty = 20000 - $ndays;
  $thirty = 30000 - $ndays;

  $tw = number_format(2000/365.25, 0, '.','');
  $fi =number_format(5000/365.25, 0, '.','');
  $te=number_format(10000/365.25, 0, '.','');
  $ni = number_format(19000/365.25, 0, '.','');
  $twe=number_format(20000/365.25, 0, '.','');
  $t3=number_format(30000/365.25, 0, '.','');

  $ndays = number_format($ndays,0,'.',',');

 $contentdiv = '<div style="margin: 0px auto; width: 100%;"><div style="font-size: 30px;">You have been alive for ' .$ndays .' days.</div>' .'<br /><br />'
  				."<div style='text-align: left; '>"
  				."2,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+2000 days")) .' - (' .$tw.' and a half)' ."<br />"
  				."5,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+5000 days"))  .' - (almost ' .$fi.')' ."<br />"
  				."10,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+10000 days"))  .' - (' .$te.')' ."<br />"
 				."19,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+19000 days")) .' - (' .$ni.')' ."<br />"
  				."20,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+20000 days"))  .' - (almost ' .$twe.')' ."<br />"
  				."30,000 days and counting - " .date('l, F jS, Y', strtotime($sday ."+30000 days"))  .' - (just turned ' .$t3.')' ."<br /></div></div>";




}else{
	$contentdiv = '<div id="content">Please enter your birthdate below</div>';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>My Days</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript" src="calendar.js"></script>
</head>
<body id="main_body" >
	<?php echo $contentdiv; ?>
	<img id="top" src="top.png" style="width: 100%" alt="">
	<div id="form_container" style="width:100%;">

		<h1><a>My Days</a></h1>
		<form id="form_551998" class="appnitro"  method="post" action="<?=$_SERVER['PHP_SELF']?>">
					<div class="form_description">
			<h2>My Days</h2>
			<p>Enter your birthday and find out how many days you've lived!</p>
		</div>
			<ul >

			<li>
<?php
echo date_picker("bday")?>

			</li>


					<li class="buttons">

				<input id="submit" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>
	</div>
	</body>
</html>

<?php
		function date_picker($name, $startyear=NULL, $endyear=NULL)
{
    if($startyear==NULL) $startyear = date("Y");
    if($endyear==NULL) $endyear=date("Y")-100;

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
