<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Паркър</title>
<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<style>
body {
    padding: 0;
    margin: 0;
    font-family: 'Roboto', sans-serif;
    font-size: 4vw;
}

#copyright {
    font-size: 0.8em;
    background-color: #5887d3;
    color: white;
    padding: 2vw;
}

#prices_container {
    color: white;
}

#logo {
    width: 32vw;
    margin-left: 5vw;
    margin-top: 5vw;
}

#price_selector_id {
    font-family: 'Roboto', sans-serif;
    font-size: 1.8vw;
    width: 45vw;
    height: 5vh;
    margin-top: 5vh;
}

#flex-container {
    position: relative;
    display: flex;
    flex-flow: row wrap;
    height: 85vh;
}

#header {
    width: 100vw;
    height: 15vh;
    background-color: #2e55a4;
    z-index: 100;
}

#hours {
    width: 100vw;
    height: 25vh;
    text-align: center;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-around;
    align-items: baseline;
    align-content: space-around;
    padding-left: 5vw;
    padding-right: 5vw;
}

.hour {
    cursor: pointer;
    color: #2e55a4;
    vertical-align: middle;
    border: 0.8vw solid #2e55a4;
    border-radius: 2vw;
    width: 11vw;
    height: 11vw;
    line-height: 11vw;
    margin-left: 0.5vw;
    margin-right: 0.5vw;
}

.hour:hover {
    background-color: #2e55a4;
    color: white;
}

.hour_clicked {
    background-color: #2e55a4;
    color: white;
}

#map {
    width: 100vw;
    height: 60vh;
    background-color: #959595;
    overflow: hidden;
}

#footer {
    background-color: #2e55a4;
    text-align: center;
    width: 100vw;
    height: 32vh;
}

@media screen and (orientation:portrait) {
    body {
        font-size: 4vw;
    }
    
    #logo {
        width: 32vw;
        margin-left: 5vw;
        margin-top: 5vw;
    }

    #price_selector_id {
        font-size: 4vw;
        width: 90vw;
        height: 7vh;
    }

    #header {
        position: fixed;
    }

    #flex-container {
        top: 15vh;
        flex-direction: row;
    }

    #hours {
        order: 1;
        height: 25vh;
        width: 100vw;
        padding-left: 5vw;
        padding-right: 5vw;
    }
    
    .hour {
        border: 0.8vw solid #2e55a4;
        border-radius: 2vw;
        width: 11vw;
        height: 11vw;
        line-height: 11vw;
        margin-left: 0.5vw;
        margin-right: 0.5vw;
    }
    
    #map {
        order: 2;
        width: 100vw;
        height: 60vh;
    }
    
    #footer {
        order: 3;
        width: 100vw;
        height: 32vh;
    }
}

@media screen and (orientation:landscape) {
    body {
        font-size: 2vw;
    }

    #logo {
        width: 14vw;
        margin-left: 2vw;
        margin-top: 2vw;
    }

    #price_selector_id {
        font-size: 1.8vw;
        width: 45vw;
        height: 5vh;
    }

    #header {
        position: relative;
    }

    #flex-container {
        top: 0;
        flex-direction: column;
    }

    #hours {
        order: 2;
        height: 30vh;
        width: 50vw;
        padding-left: 0vw;
        padding-right: 0vw;
    }
    
    .hour {
        border: 0.4vw solid #2e55a4;
        border-radius: 1vw;
        width: 5vw;
        height: 5vw;
        line-height: 5vw;
        margin-left: 1vw;
        margin-right: 1vw;
    }
    
    #map {
        order: 1;
        width: 50vw;
        height: 85vh;
    }
    
    #footer {
        order: 3;
        width: 50vw;
        height: 55vh;
    }
}

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="snap.svg-min.js"></script>
<script>
<?php

# Neighbourhoods we're interested in
$neighbourhoods["centar"] = "Център";
$neighbourhoods["oborishte"] = "Оборище";
$neighbourhoods["hladilnika"] = "Хладилника";
$neighbourhoods["lozenec"] = "Лозенец";
#$neighbourhoods["serdika"] = "Сердика";
$neighbourhoods["b18"] = "Зона Б18";
$neighbourhoods["b19"] = "Зона Б19";
$neighbourhoods["b5"] = "Зона Б5";
$neighbourhoods["qvorov"] = "Яворов";
$neighbourhoods["ivan_vazov"] = "Иван Вазов";

# The median prices file name
$file = "./median_prices.html";

# Initialize the parser
$dom = new DOMDocument;

# Check if file is up to date, if not - download the new one
$ctime = filectime($file);
if ($ctime - date("U", time() - 60 * 60 * 24 * 7) > 0) {
  #echo "It's not old";
} else {
  #echo "It's old, downloading";
  $new_data = file_get_contents("https://www.imoti.net/bg/sredni-ceni?ad_type_id=3&city_id=1&region_id=&property_type_id[]=6&property_type_id[]=11&property_type_id[]=17&currency_id=2");
  file_put_contents($file, $new_data);
}

# Load HTML in parser and parse for median prices
$dom->loadHTMLFile($file);
$tables = $dom->getElementsByTagName('table');
$table = $tables->item(0);
$rows = $table->getElementsByTagName('tr');

$median_prices = array();

foreach ($rows as $row) {
  $cells = $row->getElementsByTagName('td');
  $neighbourhood = $cells[0]->nodeValue;
  $neighbourhoods_values = array_values($neighbourhoods);
  if (in_array($neighbourhood, $neighbourhoods_values)) {
    #echo $cells[0]->nodeValue . " ";
    $price1 = (float) filter_var($cells[2]->nodeValue, FILTER_VALIDATE_FLOAT);
    #echo $price1 . " ";
    $price2 = (float) filter_var($cells[4]->nodeValue, FILTER_VALIDATE_FLOAT);
    #echo $price2 . " ";
    $price3 = (float) filter_var($cells[6]->nodeValue, FILTER_VALIDATE_FLOAT);
    #echo $price3 . " ";
    $median_prices[$neighbourhood] = ($price1 + $price2 + $price3) / 3;
    #echo $median_prices[$neighbourhood] . "<br />\n";
  }
}

$i = 1;
$median_price_var = 0.0;

foreach ($median_prices as $key => $value) {
#for ($i = 0; $i < count($median_prices); $i++) {
    #echo "value for " . $key . " is " . $value . " iterator is " . $i . "<br />\n";
    $median_price_var += $value;
    $i++;
}

$median_price_var = $median_price_var / $i;
echo "var median_price = '" . $median_price_var . "';\n";
$coeff = 1.5 / $median_price_var;
#echo " coeff is " . $coeff . "<br />\n";

/*

sutrin 7:30 do 9:30 e 4as pik
ve4er 16:30 do 18:30 e 4as pik
za 4as pik * 0.85
za ne4as pik * 1

*/

echo "var median_prices = [];\n";

foreach ($median_prices as $key => $value) {
    echo "median_prices['" . $key . "'] = '" . $value * $coeff . "';\n";
}

?>

var hourly_coefficient = 0.85;

window.onload = function () {
    var map_svg = Snap("#map_svg");
    var svg_animatables = [];
    svg_animatables["Център"] = map_svg.selectAll("#centur .fillable");
    svg_animatables["Оборище"] = map_svg.selectAll("#oborishte .fillable");
    svg_animatables["Хладилника"] = map_svg.selectAll("#hladilnika .fillable");
    svg_animatables["Лозенец"] = map_svg.selectAll("#lozenec .fillable");
    svg_animatables["Сердика"] = map_svg.selectAll("#serdika .fillable");
    svg_animatables["Зона Б18"] = map_svg.selectAll("#b18 .fillable");
    svg_animatables["Зона Б19"] = map_svg.selectAll("#b19 .fillable");
    svg_animatables["Зона Б5"] = map_svg.selectAll("#b5 .fillable");
    svg_animatables["Яворов"] = map_svg.selectAll("#qvorov .fillable");
    svg_animatables["Иван Вазов"] = map_svg.selectAll("#ivan_vazov .fillable");
    
    svg_animatables["Център"].animate({fill: '#f8941e'}, 1000, mina.ease);
    svg_animatables["Център"].forEach(function(elem){elem.addClass('filled');});
    
    jQuery(document).ready(function(){
        jQuery('.hour').click(function(){
            clicked_time = jQuery(this).html();
            jQuery('.hour_clicked').removeClass("hour_clicked");
            jQuery(this).addClass("hour_clicked");
            if (clicked_time == "7:30" || clicked_time == "8:30" || clicked_time == "16:30" || clicked_time == "17:30") {
                hourly_coefficient = 1;
            } else {
                hourly_coefficient = 0.85;
            }
        });
        
        jQuery('#prices_container').html('Цена за 1 час паркиране в Центъра: ' + Math.round(median_prices['Център'] * 100) / 100);
        
        jQuery('select[name="price_selector"]').change(function(){
            var my_thing = jQuery(this).val();
            if (my_thing == 'Сердика'){
                jQuery('#prices_container').html('Цена за 1 час паркиране: ' + Math.round(median_prices['Център'] * 100) / 100);
            } else {
                jQuery('#prices_container').html('Цена за 1 час паркиране: ' + Math.round(median_prices[my_thing] * 100) / 100);
            }
            map_svg.selectAll(".filled").forEach(function(elem){elem.stop();});
            map_svg.selectAll(".filled").animate({fill: '#2e55a4'}, 1000, mina.ease);
            svg_animatables[my_thing].forEach(function(elem){elem.stop();});
            svg_animatables[my_thing].animate({fill: '#f8941e'}, 1000, mina.ease);
            map_svg.selectAll(".filled").forEach(function(elem){elem.removeClass('filled');});
            svg_animatables[my_thing].forEach(function(elem){elem.addClass('filled');});
        });
    });


}



</script>
</head>
<body>
<div id="header">
<svg id="logo"
     version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 168 45" xml:space="preserve">
<style type="text/css">
	.st0{fill:#2E55A4;}
	.st1{fill:#FFFFFF;}
</style>
<g id="Layer_2">
	<rect x="-1" y="-1" class="st0" width="170" height="47"/>
</g>
<g id="Layer_4">
	<g>
		<path class="st1" d="M28.6,13.3h-5.4v8.9h5.2c1.5,0,2.7-0.4,3.5-1.1c0.8-0.7,1.2-1.8,1.2-3.1c0-1.4-0.4-2.5-1.2-3.4
			S30,13.3,28.6,13.3z"/>
		<path class="st1" d="M43.5,37h-6.2l8.2-22.1V6.6c0-2.8-2.3-5.1-5.1-5.1H8.6c-2.8,0-5.1,2.3-5.1,5.1v31.8c0,2.8,2.3,5.1,5.1,5.1
			h31.8c2.8,0,5.1-2.3,5.1-5.1v-7.3h0L43.5,37z M36.3,24.5c-1.9,1.6-4.5,2.4-7.9,2.4h-5.1v10h-5.9V8.5h11.1c2.1,0,4,0.4,5.6,1.2
			s2.9,1.9,3.7,3.3c0.9,1.4,1.3,3.1,1.3,4.9C39.1,20.7,38.2,22.9,36.3,24.5z"/>
		<path class="st1" d="M156,25.1c1.7-0.8,3-1.8,3.8-3.1c0.8-1.3,1.3-2.9,1.3-4.9c0-2.7-0.9-4.9-2.7-6.3c-1.8-1.5-4.4-2.2-7.8-2.2
			H140V37h5.9V26.6h4.7l5.3,10.4h6.3v-0.3L156,25.1z M154,20.7c-0.8,0.7-1.9,1.1-3.4,1.1h-4.7v-8.5h4.7c1.6,0,2.7,0.4,3.5,1.2
			c0.8,0.8,1.1,1.8,1.1,3.2C155.2,18.9,154.8,19.9,154,20.7z"/>
		<polygon class="st1" points="123.4,24.6 134.6,24.6 134.6,20.1 123.4,20.1 123.4,13.3 136.5,13.3 136.5,8.5 117.5,8.5 117.5,37 
			136.6,37 136.6,32.3 123.4,32.3 		"/>
		<polygon class="st1" points="115,8.5 107.8,8.5 100.6,17.9 98,21.4 98,8.5 92.1,8.5 92.1,37 98,37 98,28.8 101,25.6 108.3,37 
			115.3,37 104.9,21.2 		"/>
		<path class="st1" d="M86.4,22c0.8-1.3,1.3-2.9,1.3-4.9c0-2.7-0.9-4.9-2.7-6.3c-1.8-1.5-4.4-2.2-7.8-2.2H66.6V37h5.9V26.6h4.7
			L82.5,37h6.3v-0.3l-6.2-11.6C84.3,24.3,85.6,23.3,86.4,22z M80.6,20.7c-0.8,0.7-1.9,1.1-3.4,1.1h-4.7v-8.5h4.7
			c1.6,0,2.7,0.4,3.5,1.2c0.8,0.8,1.1,1.8,1.1,3.2C81.8,18.9,81.4,19.9,80.6,20.7z"/>
		<path class="st1" d="M47.9,8.5l-2.4,6.4v16.2h10.2l2,5.9h6.2L53.3,8.5H47.9z M47,26.4l3.5-10.6l3.6,10.6H47z"/>
	</g>
</g>
</svg>
</div>
<div id="flex-container">
    <div id="hours">
        <div class="hour">7:30</div>
        <div class="hour">8:30</div>
        <div class="hour">9:30</div>
        <div class="hour">10:30</div>
        <div class="hour">11:30</div>
        <div class="hour">12:30</div>
        <div class="hour">13:30</div>
        <div class="hour">14:30</div>
        <div class="hour">15:30</div>
        <div class="hour">16:30</div>
        <div class="hour">17:30</div>
        <div class="hour">18:30</div>
    </div>
    <div id="map">
<?php
#        <img id="map-svg" src="map.svg"></img>
?>
<svg id="map_svg" viewBox="0 0 1200 1200" enable-background="new 0 0 1200 1200">
<g id="Layer_16">
	<g>
		<g>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="352.7,830 390.3,850 367.5,883.5 
				372.8,909.5 405.5,916 428.9,870.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="673,705 733.4,655.3 807,722.5 
				856,678.4 800,651.7 804,622 752,589 722,571.4 735.9,524.9 778,530.6 799.7,548.1 866.7,602 866.3,577 844.3,538.7 861.5,530.6 
				810.7,439.8 798.3,440.3 717,489.7 691.7,513.3 680.4,507.6 592.3,494.3 447.7,596.6 367.6,652.7 385.8,671.7 413.7,700.8 
				443.5,655.3 468,647.7 468.5,650.9 486,652.7 472.4,675.6 472.8,678.4 495,667 508,619 566.7,576.3 606.9,685.1 518,750.3 
				518,807.7 485,807.7 459.8,766.5 472.8,787.7 457,801 447,880.3 462.5,886.5 539,932 556,886 556,844 565.7,810 616,819 
				643.1,810 673,766.5 673,745.3 			"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M374.3,486c0,0,41.9,5,63.2,7.4l5.9-35.1H437
				h-14L388,436v-29l-71,31.3l50,15.3L374.3,486z"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M307.7,597.4l22.7-33.1l-6.7-64.4l-1.5-14
				c0,0-27.2,1.4-26.2,1.7c1,0.3-4.3,21-4.3,21l-34,3l-10-11.7l-62,5.4l-0.9,15.3l30.3,11.9l17,20h24.5L307.7,597.4z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="374.5,495 374.3,486 367,453.7 
				317,438.3 322.2,486 323.7,500 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="432.5,264.7 413,243.7 363.7,299 
				388,310.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="504,354.7 522.5,360.3 520.9,359.7 
				511,329.7 523.7,269 510.6,260.1 480,331.1 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="463.4,377.8 413,331.1 355.4,382.6 
				388,407 432.5,394.3 			"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M506,391l-18.7,9l-37.4,15l29,24.5v18.7
				l13.4,34.8l-28.9,9.3l-23.6-22.1l3.7-22l-5.9,35.1c6.9,0.8,11.7,1.3,12.1,1.3c1.7,0-4.7,60.6-4.7,60.6l-54.9,23.9l-41,54.2
				l18.6,19.4l80.1-56.1l144.6-102.3l88,13.2L597,465l-60.2-48.5L506,391z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="375,530.6 325.8,520.7 323.7,500 
				330.3,564.3 353.7,555.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="810.7,439.8 808.5,436 756.5,413 
				696.5,422.5 666,445.5 717,489.7 798.3,440.3 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="460.5,749.7 459.8,766.5 485,807.7 
				518,807.7 518,750.3 606.9,685.1 566.7,576.3 508,619 495,667 472.8,678.4 472.4,675.6 467.6,683.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="153,429 176.7,397.3 173,402.3 
				127.3,358.7 106.7,360.1 101.7,367.9 76,408 124,408 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="257.3,218.7 285.7,251.7 323,214 
				323,192 310.1,182.8 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="174,247 136,223 105,255 124,263 
				134,255 160,277 178.8,250.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="247.9,302.2 229,327 160,277 
				143.8,302.2 208.8,354.4 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="890,458.2 848.3,438.3 810.7,439.8 
				861.5,530.6 897,513.3 948.3,469.7 919.3,494.4 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="818.3,772.3 794.3,803 805.7,813.7 
				799.7,860.3 853.7,874.3 876.3,824.3 890.1,812.1 807,722.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="733.4,655.3 673,705 697.2,685.1 
				716,702.5 727.5,736.5 759.1,767 807,722.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="818.3,957 824.3,1008.3 797.7,1047.7 
				901.7,1052.3 901.7,1037.3 872.3,996.7 848.3,962.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="789,597.4 807,585 794.3,561 
				799.7,548.1 778,530.6 735.9,524.9 722,571.4 752,589 763,585 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="890.1,812.1 935,772.3 892.3,727 
				883.7,691.7 856,678.4 807,722.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="650,841 680,805 648.2,802.6 
				673,766.5 643.1,810 616,819 591.6,814.6 585.2,801.8 567.3,804.1 556,844 556,886 539,932 608.9,971.1 620.6,977.7 628.9,915.1 
							"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="438.3,231 426.3,196 395.4,186.7 
				381.8,201.5 413,243.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="528.1,216.4 510.6,260.1 523.7,269 
				511,329.7 520.9,359.7 522.5,360.3 522.5,360.3 584.3,385.7 603,345.7 609.7,308.3 632.3,307 633.7,277.7 651,251 571,219 
				566.7,237.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="682.9,410.3 663.2,380.3 633.7,428.3 
				597,465 680.4,507.6 694.2,509.7 717,489.7 666,445.5 696.5,422.5 756.5,413 776.3,387.7 773.7,371 707,394.3 702.3,410.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="844.3,538.7 866.3,577 866.7,602 
				867.7,602.8 890.2,620.8 891.7,622 919,644 940,634.3 948,591 899,538.7 897,513.3 861.5,530.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="1024.1,967.7 1010,974.1 957.7,972.3 
				929,971 925,983.7 953,1025.7 943.7,1054.3 1020.3,1064.3 1051.7,1031 1056.3,953 1024.1,967.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="935,772.3 890.1,812.1 876.3,824.3 
				893.5,836.5 910.3,860.3 902.1,888.2 935,897.7 978.5,907.5 981,880.3 953,832 960.3,811.7 967,803.7 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="970.3,666.5 952.6,704.8 949.5,702.5 
				1010,748 990,802.5 1021.5,822 1005.9,854.1 1057.7,902.3 1079,902.3 1094.3,810.4 1045.7,649 1051,623.7 1052.7,621.9 
				986,602.2 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="1003,469.7 1031.7,469.7 1091,447 
				1088.3,393 1049,392.3 1047.7,380.3 996,380.3 981.7,393.7 981,429.7 972.3,434.3 939.7,434.3 948.3,469.7 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="1079.7,525.7 1076.3,517 1047,491.7 
				1010,485 1003,469.7 948.3,469.7 897,513.3 899,538.7 948,591 986,602.2 1052.7,621.9 1101.7,571.4 1123.7,571.4 1123.7,563.7 
				1146.3,551.7 1175.7,545.7 1175.7,530.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="794.3,561 807,585 818,591 
				824.3,630.5 804,622 800,651.7 856,678.4 892.1,622.3 890.5,621.1 881,613.5 867.7,602.8 866.7,602 866.7,602 799.7,548.1 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="818,591 807,585 789,597.4 763,585 
				752,589 804,622 824.3,630.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="818.3,772.3 807,722.5 759.1,767 
				794.3,803 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="887.4,1051.7 797.7,1047.7 
				778.3,1043.3 804.3,1017.3 776.3,1003.7 743.2,1040.1 719.9,1032.6 745.7,994 731.7,932 736.9,909.1 712.6,909.1 702.1,940.6 
				670.6,915.1 628.9,915.1 620.6,977.7 717,1031.7 752.3,1043 748.3,1067 758,1077.7 747.7,1112.3 793.3,1094.7 803.7,1109.3 
				745.3,1158.3 750,1169.3 762,1166.7 777,1145 830,1113.7 906,1052.5 943.7,1054.3 901.7,1052.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="807,868.3 788.2,941 818.3,957 
				853.7,874.3 799.7,860.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="925,983.7 929,971 957.7,972.3 
				957.7,962.3 929.7,959.7 935,897.7 902.1,888.2 910.3,956.3 901.7,971.3 872.3,996.7 901.7,1037.3 901.7,1052.3 943.7,1054.3 
				953,1025.7 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="818.3,957 788.2,941 807,868.3 
				799.7,860.3 783.7,885.3 752,871.6 736.9,909.1 731.7,932 745.7,994 719.9,1032.6 743.2,1040.1 776.3,1003.7 804.3,1017.3 
				778.3,1043.3 797.7,1047.7 824.3,1008.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="848.3,962.5 872.3,996.7 901.7,971.3 
				910.3,956.3 888,951.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="929.7,959.7 957.7,962.3 957.7,972.3 
				1010,974.1 1024.1,967.7 995.5,917 978.5,907.5 935,897.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="1005.9,854.1 960.3,811.7 953,832 
				981,880.3 978.5,907.5 979.8,892.9 1006,917 1056.3,953 1087,953 1099,902.3 1079,902.3 1057.7,902.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="853.7,874.3 818.3,957 848.3,962.5 
				888,951.5 910.3,956.3 902.1,888.2 902.1,888.2 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="902.1,888.2 910.3,860.3 893.5,836.5 
				876.3,824.3 853.7,874.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="979.8,892.9 978.5,907.5 995.5,917 
				1024.1,967.7 1056.3,953 1006,917 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="697.2,685.1 673,705 673,745.3 
				727.5,736.5 716,702.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="759.1,767 710.5,787 680,805 650,841 
				628.9,915.1 670.6,915.1 702.1,940.6 712.6,909.1 736.9,909.1 752,871.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="794.3,803 759.1,767 752,871.6 
				783.7,885.3 799.7,860.3 805.7,813.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="759.1,767 727.5,736.5 673,745.3 
				673,766.5 648.2,802.6 680,805 710.5,787 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="608.9,971.1 539,932 462.5,886.5 
				444.5,934 484.5,936 489,967.7 462.5,1010 496.4,1037.3 535.7,1069 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="428.9,870.7 405.5,916 425.5,936 
				444.5,934 462.5,886.5 447,880.3 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="535.7,1069 496.4,1037.3 486,1069 
				566.5,1124.5 608.9,1111.5 628.9,1086.3 600,1039 610.9,1006.5 620.6,977.7 608.9,971.1 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="628.9,1086.3 608.9,1111.5 616,1145 
				660.5,1145 694.5,1186.5 704,1180 665.5,1137.5 665,1117.5 674.5,1118 667.5,1108 656.5,1096.5 657.5,1076.5 685,1043.5 
				682,1036.5 662,1047.7 640,1031 645,1006.5 610.9,1006.5 600,1039 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="489,967.7 484.5,936 444.5,934 
				405.5,984 405.5,993 395,1006.5 405.5,1011 418.5,997 434,1016 421,1028 431,1034.5 444.5,1020 451,1022 462.5,1010 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="425.5,936 395,969.5 405.5,984 
				444.5,934 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="372.8,909.5 367.5,883.5 390.3,850 
				352.7,830 320,863 320,907 341,923 351.5,908 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="405.5,916 372.8,909.5 351.5,908 
				341,923 341,948.5 383,971.1 395,969.5 425.5,936 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="451,1022 491,1053.7 496.4,1037.3 
				462.5,1010 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="299.5,698.8 279,680.5 289,650 
				312.5,630.5 333,646.5 312.5,666 331,678.4 367.6,652.7 349,633.3 307.7,597.4 256.5,552.5 232,552.5 261,593 255.5,615.5 
				189.5,615.5 175,634.3 166.5,643.5 184,683 198,710.5 232.5,749 275,717.5 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="301,802.3 263.5,815.5 264.5,863.5 
				320,863 352.7,830 428.9,870.7 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="225.5,850 165.5,815.5 158,837.5 
				159,863 139.5,864 134.5,885.8 189,885.8 211,860 213.2,843 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="113.5,791.5 58,803.5 13.9,839 
				17.4,853 41.5,855.5 72.3,827.5 104.5,850 123,832.5 111,817 123,801.5 158.5,805 175.5,780 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="447,880.3 457,801 381.2,750.3 
				337,722.5 329,732 315.8,745.3 301,802.3 428.9,870.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="264.5,863.5 247.5,880.3 247.5,911.5 
				303,911.5 320,907 320,863 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="36,665 36,696.5 22,698.8 24.3,729 
				63,737 89.5,722.5 92,704.5 111.5,708.5 122.5,698.8 72.3,663 			"/>
			<path fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M80.5,579.9l-29,3.1l3,17l27,11l19-11
				C100.5,600,80.7,581.4,80.5,579.9z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="166.5,643.5 100.5,600 81.5,611 
				70.5,630.5 72.3,663 122.5,698.8 145,700 184,683 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="255.5,615.5 261,593 232,552.5 
				177,552.5 189.5,615.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="189.5,615.5 177,552.5 127,552.5 
				100.5,600 166.5,643.5 175,634.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="275,717.5 232.5,749 253.5,784 
				301,802.3 315.8,745.3 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="312.5,666 333,646.5 312.5,630.5 
				289,650 279,680.5 299.5,698.8 331,678.4 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="232.5,749 198,710.5 184,683 145,700 
				113.5,791.5 175.5,780 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="331,678.4 299.5,698.8 275,717.5 
				315.8,745.3 315.8,745.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="381.2,750.3 413.7,700.8 405,691.7 
				385.8,671.7 337,722.5 			"/>
			<polygon fill="none" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="413.7,700.8 385.8,671.7 405,691.7 			
				"/>
			<polygon fill="none" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="385.8,671.7 367.6,652.7 368,653.1 			
				"/>
			<polygon fill="none" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="321.7,722.5 331,678.4 315.8,745.3 			
				"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="321.7,722.5 315.8,745.3 315.8,745.3 
				329,732 337,722.5 385.8,671.7 368,653.1 367.6,652.7 331,678.4 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="892.3,727 933.3,770.5 967,803.7 
				960.3,811.7 1005.9,854.1 1021.5,822 990,802.5 1010,748 949.5,702.5 883.7,691.7 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="919,644 892.1,622.3 856,678.4 
				883.7,691.7 949.5,702.5 952.6,704.8 970.3,666.5 986,602.2 948,591 940,634.3 			"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M1157.5,418.5l-45.5-30c-1.5,0,3,74.5,3,74.5
				L1157.5,418.5z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="885,422.5 935.5,431.5 935.5,404 
				854,388.5 839,386.3 848.3,438.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="848.3,438.3 890,458.2 919.3,494.4 
				948.3,469.7 939.7,434.3 935.5,431.5 885,422.5 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="839,386.3 803,344.5 773.7,371 
				776.3,387.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="723.5,205 723.5,158 703,166.5 
				678,218 655.5,211.5 634.7,244.5 651,251 694.2,277.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="632.3,307 609.7,308.3 603,345.7 
				643.1,349.7 682.9,410.3 702.3,410.3 707,394.3 726,387.7 729,309 694.2,277.5 651,251 633.7,277.7 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="663.2,380.3 682.9,410.3 643.1,349.7 
				603,345.7 584.3,385.7 633.7,428.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="756.5,413 808.5,436 810.7,439.8 
				848.3,438.3 839,386.3 776.3,387.7 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="382.1,574.8 353.7,555.3 330.3,564.3 
				307.7,597.4 346.3,630.5 349,633.3 390.1,579.1 			"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M375,530.6l-21.3,24.6l28.4,19.6l46.5-28.2
				l9-53.3c-21.3-2.4-63.2-7.4-63.2-7.4l0.1,9h0L375,530.6z"/>
			<path fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M382.1,574.8h11.3l-3.3,4.3l54.9-23.9
				c0,0,6.3-60.6,4.7-60.6c-0.4,0-5.2-0.5-12.1-1.3l-9,53.3L382.1,574.8z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="382.1,574.8 390.1,579.1 393.3,574.8 
							"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="279.1,325.6 247.9,302.2 208.8,354.4 
				236,375.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="265.7,398 290.3,422.7 339.3,370.6 
				311.2,349.6 			"/>
			<path fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M96.3,527.3l87.7,5.2l0.7-11.9l0.9-15.3
				l62-5.4l10,11.7l34-3c0,0,5.3-20.7,4.3-21c-1-0.3,26.2-1.7,26.2-1.7l-5.2-47.6l71-31.3l-32.6-24.4l-16.1-12.1l-48.9,52.1l-24,11
				l-97.7,2L153,429l-29-21H76v98l3,55l17.3-4V527.3z"/>
			<path fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M127,552.5h50h55l-17-20l-30.3-11.9l-0.7,11.9
				l-87.7-5.2V557L79,561c0,0,1.3,17.3,1.5,18.9l20,20.1L127,552.5z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="11,511.3 19.3,545 47,558 78.5,551 
				76,506 55.2,490.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="247.9,302.2 178.8,250.6 160,277 
				229,327 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="208.8,354.4 143.8,302.2 125.3,331.1 
				188.7,381.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="173,402.3 176.7,397.3 186.3,384.4 
				188.7,381.3 125.3,331.1 113.6,349.3 106.7,360.1 127.3,358.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="249.2,385.6 265.7,398 311.2,349.6 
				279.1,325.6 236,375.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="223.3,400.3 249.2,385.6 236,375.7 
				208.8,354.4 197.5,369.5 188.7,381.3 201.1,390.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="249.2,385.6 223.3,400.3 230.7,434.4 
				168.7,435.7 266.3,433.7 290.3,422.7 265.7,398 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="223.3,400.3 201.1,390.7 188.7,381.3 
				183.2,388.6 153,429 168.7,435.7 230.7,434.4 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="432.5,264.7 388,310.3 413,331.1 
				480,331.1 			"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="363.7,299 373.7,210.3 371.9,225.8 
				323,214 285.7,251.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="413,243.7 381.8,201.5 373.7,210.3 
				363.7,299 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="388,310.3 363.7,299 285.7,251.7 
				247.9,302.2 339.3,370.6 355.4,382.6 413,331.1 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="480,331.1 510.6,260.1 432.5,264.7 
							"/>
			<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="438.3,231 467.7,231 486.3,207.3 
				509,214.7 516.6,210 443.5,169.7 443.5,153.7 432.5,146.3 395.4,186.7 426.3,196 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="480,331.1 413,331.1 463.4,377.8 
				463.4,377.8 487.3,400 506,391 536.8,416.5 597,465 633.7,428.3 584.3,385.7 522.5,360.3 504,354.7 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="522.5,360.3 522.5,360.3 584.3,385.7 
							"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="463.4,377.8 432.5,394.3 449.9,415 
				487.3,400 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="463.4,377.8 463.4,377.8 487.3,400 
							"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="423,458.2 437,458.2 443.5,458.2 
				439.8,480.2 463.4,502.3 492.3,493 478.9,458.2 478.9,439.5 449.9,415 432.5,394.3 388,407 388,436 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="528.1,216.4 516.6,210 509,214.7 
				486.3,207.3 467.7,231 510.6,260.1 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="510.6,260.1 467.7,231 438.3,231 
				413,243.7 432.5,264.7 			"/>
			<polygon fill="none" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="308.2,181.4 281,162 310.1,182.8 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="257.3,218.7 310.1,182.8 281,162 
				248,162 243.3,202.6 246.9,206.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="323,192 357.8,189.3 359.5,184.3 
				337.3,152.8 288,152.8 283.4,163.7 308.2,181.4 310.1,182.8 			"/>
			<polygon fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="247.9,302.2 285.7,251.7 257.3,218.7 
				246,205.7 243.3,202.6 220,175.5 174,247 178.8,250.6 			"/>
			<path fill="#DDDDDD" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" d="M516.6,210l11.5,6.4l38.6,21.3L571,219
				l52,20.8L638.5,125l15.5-20.5l6.5-19.5L623,82l-7,13.5h-16l3-23l8.5-36L584.3,33L562,70l-38.7-19.5l-79.8,103.2v16L516.6,210z
				 M508,109l20.1,15l10.9-7.5l-2.5-20L555,84l11.5,42l-17,41.5l-39-12L494,130L508,109z"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="549.5,167.5 566.5,126 555,84 
				536.5,96.5 539,116.5 528.1,124 508,109 494,130 510.5,155.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="375,530.6 374.5,495 323.7,500 
				325.8,520.7 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="374.5,495 374.5,495 375,530.6 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="457,801 472.8,787.7 459.8,766.5 
				460.5,749.7 413.7,700.8 381.2,750.3 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="486,652.7 468.5,650.9 472.4,675.6 
							"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="467.6,683.7 472.4,675.6 468.5,650.9 
				460.5,650 443.5,655.3 413.7,700.8 460.5,749.7 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="41.5,855.5 17.4,853 24.3,880.3 
				55,880.3 74,900.6 88.5,898.5 88.5,874 104.5,850 72.3,827.5 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="264.5,863.5 263.5,815.5 301,802.3 
				253.5,784 232.5,749 175.5,780 158.5,805 165.5,815.5 225.5,850 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="86,345.3 66.7,354.4 53,348.6 
				52.3,336.4 22.3,316.7 13.9,336.4 13.9,369.5 24.3,380.6 65.6,372.7 68.7,362 87.7,361.8 101.7,367.9 106.7,360.1 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="925,1113.3 934.7,1118.7 
				936.3,1124.7 923.3,1130.7 921.7,1138.3 934.7,1137 946,1127.7 955.7,1127.3 955.3,1142.3 941.3,1154 954,1158 960,1150.7 
				967,1151.3 969,1164 975.3,1164 975.3,1102.3 925,1103.7 			"/>
			<polygon fill="#B1DBBC" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="762,1166.7 806.7,1189.3 
				795.3,1158.3 808,1145 839,1133.7 830,1113.7 777,1145 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="225.5,850 213.2,843 211,860 
				247.5,880.3 264.5,863.5 			"/>
			<polygon fill="#C2C1C0" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="1142.3,775.5 1116,745.3 
				1109.7,722.5 1089.7,711.1 1089.7,727 1105,736.5 1099,772.9 1101.7,792.2 1123.7,816.3 1129.7,839.9 1142.3,836.3 1139,811.7 
				1109,787.7 1110.3,775.5 			"/>
		</g>
		<polygon fill="#85C194" stroke="#FFFFFF" stroke-width="2" stroke-miterlimit="10" points="626,552.5 643.1,569.6 733.4,655.3 
			673,705 673,766.5 643.1,810 616,819 591.6,814.6 574,779 571.5,730.5 606.9,613.5 577,576.3 		"/>
	</g>
</g>
<g id="centur">
	<g>
		<path class="fillable" fill="#2E55A4" d="M454.5,613l48.9-17l-20.5-23.4l-35.1,23.2l-14.5-21.7l42.7-18.4l35-24.1l7.7-14.1l-19.8-4.9l-64.1-13.1
			l4-19l15,3.7l14.4,2.3l5.2-27.3l-3.2-0.3l1.9-11.7l-1.3-2.8l7.3-5.3l2.2,4.2v10.7l23.4,0.7v-11.2h11.4v6.4l8.9,3.6l-1.9,9.8
			l18.1,1.3l3.8-28.8l-12.2-3.8l5.8-16.1l10.7,7.4l1.9-2.6l15.6,9.1l2.2-4.8c2.2,1.3,11.2,6.5,16.4,8.2c5.3,1.8,14.2,2.9,15.4,3
			l14.9,11.4l5.3,21l5.8,1c2,1.7,31.9,27,48.7,32.1c12.6,3.8,17.5,4.6,19.3,4.8v9.7l-0.8,8.9l-62.7-0.5v9.7l-1.9,9.1l47.9,13.4
			l-0.8,2.2l-32.5,10.1l-33.5,27.9l-52.8,23.2l-1,3.8c-1.4,0.9-10.6,6.8-15.8,8.6c-3.8,1.3-7.2,1.5-8.9,1.5c-0.9,0-1.4,0-1.4,0
			l-0.3,0l-0.1,0.1c-5.3-0.2-10.3-0.3-14.7-0.3c-13.6,0-22.3,0.9-26,2.7c-6.1,2.9-18.3,9.4-27.6,14.4L454.5,613z"/>
		<path fill="#FFFFFF" d="M537.8,416.6l9.8,6.8l0.8,0.5l0.6-0.8l1.4-1.8l14.7,8.6l0.9,0.6l0.5-1l1.8-3.8c2.8,1.6,11.1,6.4,16,8
			c5.3,1.8,13.7,2.8,15.4,3l14.7,11.3l5.2,20.5l0.2,0.6l0.6,0.1l5.3,0.9c3,2.6,32,27,48.8,32.1c11.7,3.5,16.8,4.5,19,4.8v9.2
			l-0.8,8.5l-61.8-0.5l-1,0v1v9.1l-1.8,8.6l-0.2,0.9l0.9,0.3l47,13.1l-0.5,1.5l-32.2,10l-0.2,0.1l-0.2,0.1l-33.3,27.8l0,0l-52.5,23
			l-0.4,0.2l-0.1,0.5l-0.9,3.4c-1.8,1.2-10.5,6.7-15.5,8.4c-3.8,1.3-7.1,1.4-8.8,1.4c-0.9,0-1.3,0-1.4,0l-0.6-0.1l-0.1,0.1
			c-5.2-0.2-10.1-0.3-14.4-0.3c-13.6,0-22.4,0.9-26.2,2.7c-6,2.9-17.7,9.1-27,14.1l-6.2-36.8l47.8-16.6l1.4-0.5l-1-1.1l-19.7-22.5
			L483,572l-0.7,0.5L448,595.2L434,574.4l42.1-18.2l0.1,0l0.1-0.1l34.9-24l0.2-0.1l0.1-0.2l7.3-13.5l0.6-1.1l-1.3-0.3l-19.2-4.7
			l-63.6-13l3.8-18.1l14.5,3.6l0,0l0,0l13.9,2.2l1,0.2l0.2-1l5-26.3l0.2-1.1l-1.1-0.1l-2.1-0.2l1.7-11.1l0-0.3l-0.1-0.3l-1.1-2.3
			l6.5-4.7l1.9,3.5v10.1v1l1,0l22.4,0.6l1,0v-1v-10.2h10.4v5.5v0.7l0.6,0.3l8.2,3.4l-1.8,8.8l-0.2,1.1l1.1,0.1l17.1,1.2l0.9,0.1
			l0.1-0.9l3.7-27.9l0.1-0.8l-0.8-0.3l-11.3-3.5L537.8,416.6 M537.3,415l-6.2,17.2l12.3,3.8l-3.7,27.9l-17.1-1.2l1.9-9.6l-8.9-3.7
			v-6.5h-12.4v11.2l-22.4-0.6v-10.4l-2.5-4.8l-8.1,5.9l1.4,3l-1.9,12.1l3.1,0.3l-5,26.3l-13.9-2.2l-15.5-3.8l-4.2,20l64.5,13.2
			l19.1,4.7l-7.3,13.5l-34.9,24L432.5,574l15.1,22.6l35.1-23.3l19.7,22.5L454,612.7l6.5,39c9.3-5,21.9-11.7,28.2-14.8
			c4.2-2,14.6-2.6,25.7-2.6c5,0,10.2,0.1,15,0.3l0-0.1c0,0,0.5,0.1,1.5,0.1c1.8,0,5.2-0.2,9.1-1.5c5.5-1.8,15.3-8.2,16-8.7l1-3.8
			l52.8-23.2l0,0l33.3-27.8l32.7-10.2l1.1-3l-48-13.4l1.8-8.7v-9.2l62.7,0.5l0.9-9.4v-10.2c-1.2,0-5.7-0.5-19.7-4.8
			c-17.4-5.3-48.6-32-48.6-32l-5.6-1l-5.3-20.9l-15.1-11.6c0,0-9.7-1.1-15.5-3c-5.7-1.9-16.7-8.4-16.7-8.4l-2.2,4.8L550,420
			l-1.9,2.6L537.3,415L537.3,415z"/>
	</g>
	<g display="none">
		<path display="inline" fill="#F7941E" d="M454.5,613l48.9-17l-20.5-23.4l-35.1,23.2l-14.5-21.7l42.7-18.4l35-24.1l7.7-14.1
			l-19.8-4.9l-64.1-13.1l4-19l15,3.7l14.4,2.3l5.2-27.3l-3.2-0.3l1.9-11.7l-1.3-2.8l7.3-5.3l2.2,4.2v10.7l23.4,0.7v-11.2h11.4v6.4
			l8.9,3.6l-1.9,9.8l18.1,1.3l3.8-28.8l-12.2-3.8l5.8-16.1l10.7,7.4l1.9-2.6l15.6,9.1l2.2-4.8c2.2,1.3,11.2,6.5,16.4,8.2
			c5.3,1.8,14.2,2.9,15.4,3l14.9,11.4l5.3,21l5.8,1c2,1.7,31.9,27,48.7,32.1c12.6,3.8,17.5,4.6,19.3,4.8v9.7l-0.8,8.9l-62.7-0.5v9.7
			l-1.9,9.1l47.9,13.4l-0.8,2.2l-32.5,10.1l-33.5,27.9l-52.8,23.2l-1,3.8c-1.4,0.9-10.6,6.8-15.8,8.6c-3.8,1.3-7.2,1.5-8.9,1.5
			c-0.9,0-1.4,0-1.4-0.1l-0.3,0l-0.1,0.1c-5.3-0.2-10.3-0.3-14.7-0.3c-13.5,0-22.3,0.9-26,2.7c-6.1,2.9-18.3,9.4-27.6,14.4
			L454.5,613z"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M537.8,416.6l9.8,6.8l0.8,0.5l0.6-0.8l1.4-1.8l14.7,8.6l0.9,0.6l0.5-1l1.8-3.8c2.8,1.6,11.1,6.4,16,8
				c5.3,1.8,13.7,2.8,15.4,3l14.7,11.3l5.2,20.5l0.2,0.6l0.6,0.1l5.3,0.9c3,2.6,32,27,48.8,32.1c11.7,3.5,16.8,4.5,19,4.8v9.2
				l-0.8,8.5l-61.8-0.5l-1,0v1v9.1l-1.8,8.6l-0.2,0.9l0.9,0.3l47,13.1l-0.5,1.5l-32.2,10l-0.2,0.1l-0.2,0.1l-33.3,27.8l0,0l-52.5,23
				l-0.4,0.2l-0.1,0.5l-0.9,3.4c-1.8,1.2-10.5,6.7-15.5,8.4c-3.8,1.3-7.1,1.4-8.8,1.4c-0.9,0-1.3,0-1.4,0l-0.6-0.1l-0.1,0.1
				c-5.2-0.2-10.1-0.3-14.4-0.3c-13.6,0-22.4,0.9-26.2,2.7c-6,2.9-17.7,9.1-27,14.1l-6.2-36.8l47.8-16.6l1.4-0.5l-1-1.1l-19.7-22.5
				L483,572l-0.7,0.5L448,595.2L434,574.4l42.1-18.2l0.1,0l0.1-0.1l34.9-24l0.2-0.1l0.1-0.2l7.3-13.5l0.6-1.1l-1.3-0.3l-19.2-4.7
				l-63.6-13l3.8-18.1l14.5,3.6l0,0l0,0l13.9,2.2l1,0.2l0.2-1l5-26.3l0.2-1.1l-1.1-0.1l-2.1-0.2l1.7-11.1l0-0.3l-0.1-0.3l-1.1-2.3
				l6.5-4.7l1.9,3.5v10.1v1l1,0l22.4,0.6l1,0v-1v-10.2h10.4v5.5v0.7l0.6,0.3l8.2,3.4l-1.8,8.8l-0.2,1.1l1.1,0.1l17.1,1.2l0.9,0.1
				l0.1-0.9l3.7-27.9l0.1-0.8l-0.8-0.3l-11.3-3.5L537.8,416.6 M537.3,415l-6.2,17.2l12.3,3.8l-3.7,27.9l-17.1-1.2l1.9-9.6l-8.9-3.7
				v-6.5h-12.4v11.2l-22.4-0.6v-10.4l-2.5-4.8l-8.1,5.9l1.4,3l-1.9,12.1l3.1,0.3l-5,26.3l-13.9-2.2l-15.5-3.8l-4.2,20l64.5,13.2
				l19.1,4.7l-7.3,13.5l-34.9,24L432.5,574l15.1,22.6l35.1-23.3l19.7,22.5L454,612.7l6.5,39c9.3-5,21.9-11.7,28.2-14.8
				c4.2-2,14.6-2.6,25.7-2.6c5,0,10.2,0.1,15,0.3l0-0.1c0,0,0.5,0.1,1.5,0.1c1.8,0,5.2-0.2,9.1-1.5c5.5-1.8,15.3-8.2,16-8.7l1-3.8
				l52.8-23.2l0,0l33.3-27.8l32.7-10.2l1.1-3l-48-13.4l1.8-8.7v-9.2l62.7,0.5l0.9-9.4v-10.2c-1.2,0-5.7-0.5-19.7-4.8
				c-17.4-5.3-48.6-32-48.6-32l-5.6-1l-5.3-20.9l-15.1-11.6c0,0-9.7-1.1-15.5-3c-5.7-1.9-16.7-8.4-16.7-8.4l-2.2,4.8L550,420
				l-1.9,2.6L537.3,415L537.3,415z"/>
		</g>
	</g>
</g>
<g id="b18">
	<g display="inline">
		<polygon class="fillable" fill="#2E55A4" points="454.5,483.3 458.5,458.8 472.2,460.1 467.5,485.4 		"/>
		<path fill="#FFFFFF" d="M458.9,459.3l12.8,1.2l-4.6,24.3l-11.9-1.9L458.9,459.3 M458.1,458.2l-4.1,25.5l13.9,2.2l5-26.3
			L458.1,458.2L458.1,458.2z"/>
	</g>
</g>
<g id="ujen_park">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="505.7,744.6 508.3,736 508.3,723.2 505.8,723 506.2,717.5 509.6,714.7 509.6,706.2 503.3,708.4 
			501.8,704.5 495.7,702.6 496.8,699.5 503.7,702.7 508.9,702.3 520.4,710.2 519.9,741.2 512.5,744.8 		"/>
		<path fill="#FFFFFF" d="M497.1,700.2l6.3,3l0.2,0.1l0.3,0l4.9-0.4l11.1,7.6l-0.5,30.4l-7,3.4l-6-0.3l2.3-7.9l0-0.1v-0.1v-12.3
			v-0.9l-0.9-0.1l-1.5-0.1l0.4-4.8l3-2.6l0.3-0.3v-0.5v-7.5v-1.4l-1.3,0.5l-5.2,1.8l-1.2-3.2l-0.2-0.4l-0.5-0.1l-5.4-1.7
			L497.1,700.2 M496.5,698.8l-1.4,4.1l6.4,2l1.6,4.1l6.1-2.1v7.5l-3.3,2.9l-0.5,6.2l2.6,0.2v12.3l-2.7,9.1l7.5,0.3l7.8-3.8l0.5-31.6
			l-11.8-8.1l-5.3,0.4L496.5,698.8L496.5,698.8z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="505.7,744.6 508.3,736 508.3,723.2 505.8,723 506.2,717.5 509.6,714.6 
			509.6,706.2 503.3,708.4 501.8,704.5 495.7,702.6 496.8,699.5 503.7,702.7 508.9,702.3 520.4,710.2 519.9,741.2 512.5,744.8 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M497.1,700.2l6.3,3l0.2,0.1l0.3,0l4.9-0.4l11.1,7.6l-0.5,30.4l-7,3.4l-6-0.3l2.3-7.9l0-0.1v-0.1v-12.3
				v-0.9l-0.9-0.1l-1.5-0.1l0.4-4.8l3-2.6l0.3-0.3v-0.5v-7.5v-1.4l-1.3,0.5l-5.2,1.8l-1.2-3.2l-0.2-0.4l-0.5-0.1l-5.4-1.7
				L497.1,700.2 M496.5,698.8l-1.4,4.1l6.4,2l1.6,4.1l6.1-2.1v7.5l-3.3,2.9l-0.5,6.2l2.6,0.2v12.3l-2.7,9.1l7.5,0.3l7.8-3.8
				l0.5-31.6l-11.8-8.1l-5.3,0.4L496.5,698.8L496.5,698.8z"/>
		</g>
	</g>
</g>
<g id="hladilnika">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="515.9,808.8 515.8,804.6 534.6,792.4 540.4,752.7 610.8,723.2 615.4,734.3 603.9,740.4 
			603.9,750.4 576.9,762.2 576,767.1 594.5,779.7 593.7,786 589,792.2 594.5,796.3 594.3,809.9 		"/>
		<path fill="#FFFFFF" d="M610.6,723.8l4.2,10.2l-10.8,5.8l-0.5,0.3v0.6v9.4l-26.5,11.5l-0.5,0.2l-0.1,0.5l-0.8,4.3l-0.1,0.6
			l0.5,0.4l18,12.3l-0.7,5.8l-4.3,5.7l-0.6,0.8l0.8,0.6l4.9,3.7l-0.1,12.8l-77.4-1.1l-0.1-3.4l18.3-12l0.4-0.2l0.1-0.4l5.8-39.1
			L610.6,723.8 M611.1,722.5L540,752.4l-5.9,39.7l-18.8,12.3l0.2,4.9l79.4,1.1L595,796l-5.3-4l4.5-5.9l0.8-6.7l-18.5-12.6l0.8-4.3
			l27.1-11.8v-10l11.6-6.2L611.1,722.5L611.1,722.5z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="515.9,808.8 515.8,804.6 534.6,792.4 540.4,752.7 610.8,723.2 615.4,734.3 
			603.9,740.4 603.9,750.4 576.9,762.2 576,767.1 594.5,779.7 593.7,786 589,792.2 594.5,796.3 594.3,809.9 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M610.6,723.8l4.2,10.2l-10.8,5.8l-0.5,0.3v0.6v9.4l-26.5,11.5l-0.5,0.2l-0.1,0.5l-0.8,4.3l-0.1,0.6
				l0.5,0.4l18,12.3l-0.7,5.8l-4.3,5.7l-0.6,0.8l0.8,0.6l4.9,3.7l-0.1,12.8l-77.4-1.1l-0.1-3.4l18.3-12l0.4-0.2l0.1-0.4l5.8-39.1
				L610.6,723.8 M611.1,722.5L540,752.4l-5.9,39.7l-18.8,12.3l0.2,4.9l79.4,1.1L595,796l-5.3-4l4.5-5.9l0.8-6.7l-18.5-12.6l0.8-4.3
				l27.1-11.8v-10l11.6-6.2L611.1,722.5L611.1,722.5z"/>
		</g>
	</g>
</g>
<g id="b19">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="434.6,500.8 434.7,500.5 463.5,506.4 462.6,513.2 457.7,519.9 423.8,541.6 		"/>
		<path fill="#FFFFFF" d="M435,501.1l27.9,5.7l-0.8,6.1l-4.8,6.6l-32.7,21L435,501.1 M434.3,500l-0.1,0.7L423,542.7l35.1-22.4l5-6.9
			l1-7.3L434.3,500L434.3,500z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="434.6,500.8 434.7,500.5 463.5,506.4 462.6,513.2 457.7,519.9 423.8,541.6 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M435,501.1l27.9,5.7l-0.8,6.1l-4.8,6.6l-32.7,21L435,501.1 M434.3,500l-0.1,0.7L423,542.7l35.1-22.4
				l5-6.9l1-7.3L434.3,500L434.3,500z"/>
		</g>
	</g>
</g>
<g id="lozenec">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="520.9,741.5 521.4,709.7 509.2,701.3 503.9,701.7 497.5,698.7 512.5,689.3 512,686 551.1,659.7 
			561.6,655.4 556.6,624.3 557.5,621 609.7,598 634.4,626 634.2,637 617.1,641.7 625.8,664.8 626.7,682.5 636.3,697 615.6,720.2 
			610.9,722 540.6,751.6 540.6,751.3 520.1,758.7 		"/>
		<path fill="#FFFFFF" d="M609.6,598.6l24.3,27.6l-0.1,10.4l-16.2,4.4l-1.1,0.3l0.4,1l8.4,22.5l0.9,17.5l0,0.3l0.2,0.2l9.3,14
			l-20.4,22.8l-4.6,1.8l-69.5,29.2l0-0.2l-1.6,0.6l-19,6.8l0.7-16.4l0.5-31.6l0-0.5l-0.4-0.3l-11.8-8.1l-0.3-0.2l-0.4,0l-5,0.4
			l-5.4-2.5l13.9-8.8l0.5-0.3l-0.1-0.6l-0.4-2.7l38.8-26.1l10.1-4.1l0.7-0.3L562,655l-4.9-30.6l0.8-3L609.6,598.6 M609.9,597.4
			l-52.8,23.2l-1,3.8c0,0,0,0,0,0l4.9,30.8l-10.2,4.1l-39.4,26.5l0.5,3.3l-15.5,9.7l0,0l7.3,3.4l5.3-0.4l11.8,8.1l-0.5,31.6
			l-0.8,17.9L540,752l0,0.3l71.1-29.9l4.8-1.9l21-23.6l-9.7-14.7l-1-17.7l-8.4-22.6l16.9-4.6l0.2-11.6L609.9,597.4L609.9,597.4z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="520.9,741.5 521.4,709.7 509.2,701.3 503.9,701.7 497.5,698.7 512.5,689.3 
			512,686 551.1,659.7 561.6,655.4 556.6,624.3 557.5,621 609.7,598 634.4,626 634.2,637 617.1,641.7 625.8,664.8 626.7,682.5 
			636.3,697 615.6,720.2 610.9,722 540.6,751.6 540.6,751.3 520.1,758.7 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M609.6,598.6l24.3,27.6l-0.1,10.4l-16.2,4.4l-1.1,0.3l0.4,1l8.4,22.5l0.9,17.5l0,0.3l0.2,0.2l9.3,14
				l-20.4,22.8l-4.6,1.8l-69.5,29.2l0-0.2l-1.6,0.6l-19,6.8l0.7-16.4l0.5-31.6l0-0.5l-0.4-0.3l-11.8-8.1l-0.3-0.2l-0.4,0l-5,0.4
				l-5.4-2.5l13.9-8.8l0.5-0.3l-0.1-0.6l-0.4-2.7l38.8-26.1l10.1-4.1l0.7-0.3L562,655l-4.9-30.6l0.8-3L609.6,598.6 M609.9,597.4
				l-52.8,23.2l-1,3.8c0,0,0,0,0,0l4.9,30.8l-10.2,4.1l-39.4,26.5l0.5,3.3l-15.5,9.7l0,0l7.3,3.4l5.3-0.4l11.8,8.1l-0.5,31.6
				l-0.8,17.9L540,752l0,0.3l71.1-29.9l4.8-1.9l21-23.6l-9.7-14.7l-1-17.7l-8.4-22.6l16.9-4.6l0.2-11.6L609.9,597.4L609.9,597.4z"/>
		</g>
	</g>
</g>
<g id="oborishte">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="678,556.3 629.5,542.8 631.2,534.4 631.2,525.7 693.9,526.2 694.7,516.3 694.7,506.6 725.9,514.8 
			735.4,525.1 736.4,556.6 729.9,570.8 		"/>
		<path fill="#FFFFFF" d="M695.2,507.2l30.4,8l9.3,10l0.9,31.3l-6.2,13.7l-51.6-14.4l-48-13.4l1.6-7.8l0-0.1v-0.1v-8.2l61.7,0.5
			l0.9,0l0.1-0.9l0.9-9.4l0,0v0V507.2 M694.6,506.1c0,0-0.1,0-0.2,0c0,0-0.1,0-0.2,0v10.2l-0.9,9.4l-62.7-0.5v9.2l-1.8,8.7
			l48.9,13.6l52.4,14.7l6.7-14.7l-1-31.9l-9.7-10.5L694.6,506.1L694.6,506.1z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="678,556.3 629.5,542.8 631.2,534.5 631.2,525.7 693.9,526.2 694.7,516.3 
			694.7,506.6 725.9,514.8 735.4,525.1 736.4,556.6 729.9,570.8 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M695.2,507.2l30.4,8l9.3,10l0.9,31.3l-6.2,13.7l-51.6-14.4l-48-13.4l1.6-7.8l0-0.1v-0.1v-8.2l61.7,0.5
				l0.9,0l0.1-0.9l0.9-9.4l0,0v0V507.2 M694.6,506.1c0,0-0.1,0-0.2,0c0,0-0.1,0-0.2,0v10.2l-0.9,9.4l-62.7-0.5v9.2l-1.8,8.7
				l48.9,13.6l52.4,14.7l6.7-14.7l-1-31.9l-9.7-10.5L694.6,506.1L694.6,506.1z"/>
		</g>
	</g>
</g>
<g id="qvorov">
	<g>
		<path class="fillable" fill="#2E55A4" d="M644.1,569.9l32.1-10l1-2.8l52.3,14.6l-11.9,26l-6.4,6.2l0.4,0.4c9.3,7.9,23.4,20,24.7,21.6
			c0.1,0.6,1,2.8,8.7,14l-11.7,14.6L644.1,569.9z"/>
		<path fill="#FFFFFF" d="M677.5,557.7L677.5,557.7l51.3,14.3l-11.6,25.4l-5.9,5.8l-0.8,0.8l0.8,0.7c8.8,7.4,22.8,19.5,24.5,21.4
			c0.2,1,1.7,3.7,8.5,13.8l-11.1,13.9l-88.2-83.7l31.1-9.7l0.5-0.1l0.2-0.5L677.5,557.7 M676.9,556.5l-1.1,3l-32.7,10.2l90.2,85.6
			l12.3-15.3c0,0-9.4-13.9-8.8-14.1S712,603.9,712,603.9l6.1-5.9l12.1-26.6l-52.4-14.7L676.9,556.5L676.9,556.5z"/>
	</g>
	<g display="none">
		<path display="inline" fill="#F7941E" d="M644.1,569.9l32.1-10l1-2.8l52.3,14.6l-11.9,26l-6.4,6.2l0.4,0.4
			c9.3,7.9,23.4,20,24.7,21.6c0.1,0.6,1,2.8,8.7,14l-11.7,14.6L644.1,569.9z"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M677.5,557.7L677.5,557.7l51.3,14.3l-11.6,25.4l-5.9,5.8l-0.8,0.8l0.8,0.7c8.8,7.4,22.8,19.5,24.5,21.4
				c0.2,1,1.7,3.7,8.5,13.8l-11.1,13.9l-88.2-83.7l31.1-9.7l0.5-0.1l0.2-0.5L677.5,557.7 M676.9,556.5l-1.1,3l-32.7,10.2l90.2,85.6
				l12.3-15.3c0,0-9.4-13.9-8.8-14.1S712,603.9,712,603.9l6.1-5.9l12.1-26.6l-52.4-14.7L676.9,556.5L676.9,556.5z"/>
		</g>
	</g>
</g>
<g id="ivan_vazov">
	<g>
		<path class="fillable" fill="#2E55A4" d="M493.8,680.8l-0.1,0c0,0-4.9-0.3-7.5-0.3c-1.5,0-1.7,0.1-1.9,0.2c-0.5,0.3-1.3,1.6-1.7,2.3l-14.5,0.2
			l-6.9-31l0-0.2c9.4-5.1,21.7-11.6,27.9-14.6c3.5-1.7,12.1-2.6,25.5-2.6c4.3,0,9,0.1,14.1,0.3l-29.1,49.3L493.8,680.8z"/>
		<path fill="#FFFFFF" d="M514.5,635.3c4,0,8.5,0.1,13.3,0.2l-28.4,48.1l-5.2-3.3l-0.2-0.1l-0.3,0c-0.2,0-4.9-0.3-7.5-0.3
			c-1.6,0-1.9,0.1-2.2,0.3c-0.5,0.3-1.1,1.3-1.7,2.2l-13.8,0.1l-6.8-30.4c9.3-5,21.4-11.5,27.5-14.4
			C491.5,636.7,497.9,635.3,514.5,635.3 M514.5,634.3c-11.1,0-21.6,0.6-25.7,2.6c-6.3,3-18.9,9.7-28.2,14.8l0.1,0.6l7,31.4l15.2-0.2
			c0,0,1.2-2.1,1.7-2.4c0.1-0.1,0.7-0.1,1.6-0.1c2.6,0,7.5,0.3,7.5,0.3l6,3.8l29.8-50.5C524.6,634.5,519.5,634.3,514.5,634.3
			L514.5,634.3z"/>
	</g>
	<g display="none">
		<path display="inline" fill="#F7941E" d="M493.8,680.8l-0.1,0c0,0-4.9-0.3-7.5-0.3c-1.5,0-1.7,0.1-1.9,0.2
			c-0.5,0.3-1.3,1.6-1.7,2.3l-14.5,0.2l-6.9-31l0-0.2c9.4-5.1,21.7-11.6,27.9-14.6c3.5-1.7,12.1-2.6,25.5-2.6c4.3,0,9,0.1,14.1,0.3
			l-29.1,49.3L493.8,680.8z"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M514.5,635.3c4,0,8.5,0.1,13.3,0.2l-28.4,48.1l-5.2-3.3l-0.2-0.1l-0.3,0c-0.2,0-4.9-0.3-7.5-0.3
				c-1.6,0-1.9,0.1-2.2,0.3c-0.5,0.3-1.1,1.3-1.7,2.2l-13.8,0.1l-6.8-30.4c9.3-5,21.4-11.5,27.5-14.4
				C491.5,636.7,497.9,635.3,514.5,635.3 M514.5,634.3c-11.1,0-21.6,0.6-25.7,2.6c-6.3,3-18.9,9.7-28.2,14.8l0.1,0.6l7,31.4
				l15.2-0.2c0,0,1.2-2.1,1.7-2.4c0.1-0.1,0.7-0.1,1.6-0.1c2.6,0,7.5,0.3,7.5,0.3l6,3.8l29.8-50.5
				C524.6,634.5,519.5,634.3,514.5,634.3L514.5,634.3z"/>
		</g>
	</g>
</g>
<g id="b5">
	<g>
		<polygon class="fillable" fill="#2E55A4" points="471.6,548.5 472.8,543.4 469.5,542.8 471.2,537.2 465.8,535.5 460.4,524.8 463.6,513.5 
			464.5,506.6 498.7,513.6 517.2,518.2 510.3,530.9 476,554.6 		"/>
		<path fill="#FFFFFF" d="M464.9,507.2l33.7,6.9l17.9,4.4l-6.6,12.1l-33.8,23.3l-4-5.5l1-4.3l0.2-1l-1-0.2l-2.2-0.4l1.5-4.7l0.3-1
			l-1-0.3l-4.7-1.4l-5.3-10.4l3.1-11.1l0-0.1l0-0.1L464.9,507.2 M464.1,506l-1,7.3l-3.2,11.5l5.6,11.1l5.1,1.6l-1.8,5.7l3.3,0.6
			l-1.1,4.8l4.8,6.6l34.8-24l7.3-13.5l-19.1-4.7L464.1,506L464.1,506z"/>
	</g>
	<g display="none">
		<polygon display="inline" fill="#F7941E" points="471.6,548.5 472.8,543.4 469.5,542.8 471.2,537.2 465.8,535.5 460.4,524.8 
			463.6,513.5 464.5,506.6 498.7,513.6 517.2,518.2 510.3,530.9 476,554.6 		"/>
		<g display="inline">
			<path fill="#FFFFFF" d="M464.9,507.2l33.7,6.9l17.9,4.4l-6.6,12.1l-33.8,23.3l-4-5.5l1-4.3l0.2-1l-1-0.2l-2.2-0.4l1.5-4.7l0.3-1
				l-1-0.3l-4.7-1.4l-5.3-10.4l3.1-11.1l0-0.1l0-0.1L464.9,507.2 M464.1,506l-1,7.3l-3.2,11.5l5.6,11.1l5.1,1.6l-1.8,5.7l3.3,0.6
				l-1.1,4.8l4.8,6.6l34.8-24l7.3-13.5l-19.1-4.7L464.1,506L464.1,506z"/>
		</g>
	</g>
</g>
<g id="serdika">
	<g>
		<path class="fillable" fill="#2E55A4" d="M394.6,579.4c-0.3,0-0.6,0-0.8,0c-5.3-0.4-10.2-3.9-11.7-5.2l17.9-10l23.3-21.1l35.1-22.4l3.4-4.7l-2.5,9
			l5.8,11.5l4.9,1.5l-1.8,5.8l3.4,0.6l-1,4.5l4.6,6.3l-42.7,18.4C431,573.8,402.5,579.4,394.6,579.4
			C394.6,579.4,394.6,579.4,394.6,579.4z"/>
		<path fill="#FFFFFF" d="M460.6,518.5l-1.7,6.1l-0.1,0.4l0.2,0.3l5.6,11.1l0.2,0.4l0.4,0.1l4.2,1.3l-1.5,4.8l-0.3,1.1l1.1,0.2
			l2.3,0.4l-0.8,3.8l-0.1,0.4l0.3,0.4l4.1,5.6L432.2,573c-1.9,0.4-29.9,5.9-37.6,5.9c-0.3,0-0.5,0-0.8,0c-4.6-0.3-8.9-3.1-10.9-4.6
			l17.2-9.6l0.1-0.1l0.1-0.1l23.2-21l35-22.4l0.2-0.1l0.1-0.2L460.6,518.5 M463.1,513.3l-5,6.9L423,542.7l-23.3,21.1l-18.5,10.3
			c0,0,5.9,5.3,12.6,5.7c0.2,0,0.5,0,0.8,0c8.1,0,37.9-5.9,37.9-5.9l43.3-18.7l0.1-0.1l-4.8-6.6l1.1-4.8l-3.3-0.6l1.8-5.7l-5.1-1.6
			l-5.6-11.1L463.1,513.3L463.1,513.3z"/>
	</g>
	<g display="none">
		<path display="inline" fill="#F7941E" d="M394.6,579.4c-0.3,0-0.6,0-0.8,0c-5.3-0.4-10.2-3.9-11.7-5.2l17.9-10l23.3-21.1
			l35.1-22.4l3.4-4.7l-2.5,9l5.8,11.5l4.9,1.5l-1.8,5.8l3.4,0.6l-1,4.5l4.6,6.3l-42.7,18.4C431,573.8,402.5,579.4,394.6,579.4
			C394.6,579.4,394.6,579.4,394.6,579.4z"/>
		<path display="inline" fill="#FFFFFF" d="M460.6,518.5l-1.7,6.1l-0.1,0.4l0.2,0.3l5.6,11.1l0.2,0.4l0.4,0.1l4.2,1.3l-1.5,4.8
			l-0.3,1.1l1.1,0.2l2.3,0.4l-0.8,3.8l-0.1,0.4l0.3,0.4l4.1,5.6L432.2,573c-1.9,0.4-29.9,5.9-37.6,5.9c-0.3,0-0.5,0-0.8,0
			c-4.6-0.3-8.9-3.1-10.9-4.6l17.2-9.6l0.1-0.1l0.1-0.1l23.2-21l35-22.4l0.2-0.1l0.1-0.2L460.6,518.5 M463.1,513.3l-5,6.9L423,542.7
			l-23.3,21.1l-18.5,10.3c0,0,5.9,5.3,12.6,5.7c0.2,0,0.5,0,0.8,0c8.1,0,37.9-5.9,37.9-5.9l43.3-18.7l0.1-0.1l-4.8-6.6l1.1-4.8
			l-3.3-0.6l1.8-5.7l-5.1-1.6l-5.6-11.1L463.1,513.3L463.1,513.3z"/>
	</g>
</g>
</svg>
    </div>
    <div id="footer">
        <select name="price_selector" id="price_selector_id">
            <option value="Център">Център</option>
            <option value="Оборище">Оборище</option>
            <option value="Хладилника">Хладилника</option>
            <option value="Лозенец">Лозенец</option>
            <option value="Сердика">Сердика</option>
            <option value="Зона Б18">Зона Б18</option>
            <option value="Зона Б19">Зона Б19</option>
            <option value="Зона Б5">Зона Б5</option>
            <option value="Яворов">Яворов</option>
            <option value="Иван Вазов">Иван Вазов</option>
        </select>
        <br />
        <br />
        <div id="prices_container"></div>
        <br />
        <br />
        <div id="copyright">Приложението е направено от Отбор "БЛО" за Хакатон за София 2018</div>
    </div>
</div>
</body>
</html>
