<?php
/*
Plugin Name: d3 simpleCharts
Plugin URI: http://wordpress.org/extend/plugins/d3-simpleCharts/
Description: d3 simpleCharts gives you easy and direct access to all powerfull d3.js library's state-of-art vector based charts (SVG, vector graphics). You can use four basic graph types and customize their appearence & layout just the way you prefer by applying CSS attributes & elements of HTML5.
Version: 1.2.16
Author: Jouni Santara
Organisation: TERE-tech ltd
Author URI: http://www.linkedin.com/in/santara
License: GPL2
*/
/* 
	d3 - Charts
	-----------

	This WP-plugin is meant to be a clear foundation to bridge W3C's consortium long hard work (on the areas of CSS, SVG, and DOM) and active d3.js framework community's efforts to the WordPress developers.

	Our goal & approach is to offer a simple server's and client's open source codes that are highly modular so that you can easily tailor it just to your specific needs.

	Here are 4 example charts of D3 society but the same approach can be used for any of those d3's impressive other gallery charts: just add more JavaScript functions for each new chart type you want to generate (on d3-simpleCharts.js).

	Our example should inspire you to add more fancy charts into your visualisation purposes easily and fast and finally build up some nice GUI on the posting panel of WordPress to manage it all for the benefits of all of us.

	Welcome to the journey of professional SVG charts !	


 	simpleBarsDev
	-------------
	- Generating new simple chart from values + their labels
*/
function simpleBarsDev($data) {

$args2js["debug"] = ''; 

// External CSS style file name
$cssfile = testDef("d3chart.css",$data['cssfile']);
if ($cssfile)
	echo '<link rel="stylesheet" type="text/css" href="wp-content/plugins/d3-simpleCharts/'. $cssfile .'" />';

// Unique ID name for each new chart +
// generate all custom tailored CSS to independent graph
$uniq = styleBars($data['css']);
// $chartid = "chart" . $uniq;

// Testing ALL user's given arguments from php side + setting defauls

// Data values & labels from arrays
$values = testDef('',$data['values']);
$labels = testDef('',$data['labels']);
// Convert to php arrays
$values = getArr($values);
$labels = getArr($labels);

// Convert input into pairs of JSON to use for later JS input
$points2 = array();
if ($values[0] != '')
foreach(array_keys($labels) as $i) {
	// $points .= '{ "label" : "' . $labels[$i] . '", "value" : "' . $values[$i] . '" },';
	array_push( $points2, json_decode('{ "label" : "' . $labels[$i] . '", "value" : "' . $values[$i] . '" }') );
}
// $points = '[' . $points . ' ]'; // array JSON
// echo json_encode($points2);
// var_dump(json_decode($points));

// All other - optional - arguments from php shortcode call to php args array
$args2js = array();
$args2js["uniq"] = $uniq; // Unique ID of this new chart
$args2js["data"] = $points2; // Data set: labels & values in JSON array

// All these X labels inside $data['X'] are open and available from php shortnote for user

$args2js["chart"] = strtolower(testDef("columns",$data['chart'])); // Asked basic chart type or its default: Columns
$args2js["xtitle"] = testDef("X-values",$data['xtitle']); // Minor title
$args2js["ytitle"] = testDef("Y-values",$data['ytitle']); // Minor title

$args2js["datafile"] = testDef("",$data['datafile']); // Source of external file for data set
$args2js['row'] = testDef('1',$data['row']); // Row of chosen data from multidimension input file
$args2js['column'] = testDef('',$data['column']); // Column of chosen data from multidimension input file

$args2js['sort'] = strtolower(testDef('',$data['sort'])); // Sorting of data values (123/321)

$args2js["format"] = testDef("+00.02",$data['format']); // How to format & show numeric axis (except: line chart)
$args2js["width"] = testDef(640,$data['width']); // Width of final chart on post or page (default: VGA)
$args2js["height"] = testDef(480,$data['height']); // Height of final chart
$args2js["margin"] = testDef(json_decode('{"top": 20, "right": 20, "bottom": 30, "left": 70}'),json_decode($data['margin'])); // How much space around chart for the axis titles & values
$args2js["ticks"] = testDef(10,$data['ticks']); // If there is horizontal or vertical ticks inside columns or bars

$args2js["minrange"] = testDef(0,$data['minrange']); // Starting value for linear axis of values
$args2js["maxrange"] = testDef(0,$data['maxrange']); // Ending value

$args2js['title'] = testDef('',$data['mtitle']); // MAJOR TITLE

// Coloring of chart objects, linear ramp, if any defined
$args2js['startbar'] = testDef('',$data['startbar']); // Starting color 1st bar/slice of chart
$args2js['endbar'] = testDef('',$data['endbar']); // Ending color of smooth gradient

$main = testDef("",$data['mtitle']); // Major title
$mstyle = testDef("",$data['mstyle']); // Title's position & style <TD>
$logo = testDef("",$data['logo']); // Possible url of logo (eq company symbol, etc)

if (strlen($logo))
	$logo = ' <img src="' . $logo . '"> ';
$logopos = testDef("bottom",$data['logopos']); // Logo's layout position (bottom/top)
if ($logopos == "top") {
	$logo_top = $logo;
	$logo = '';
}

$moredata = testDef(" More Data ",$data['moredata']); // Title's position & style <TD>
$backstyle = testDef('',$data['backstyle']); // Chart's border & background style 
$url = testDef('',$data['url']); // Url to further info on net

if ($url)  // URL to external page linked to chart
	$url = ' href="' . $url . '" ';

$title = testDef('',$data['title']); // Longer pop-up description for user when cursor mover over chart
if ($title)
	$title = ' title="' . $title . '" ';
/*
TODO/test: User defines one's own container id => chart could be anywhere on page/post where shortcode is called, clumsy
if ($data['id'])
	$args2js['id'] = $data['id'];
*/

// Some config flags about buttons on layout: visible or not (def: yes)
$switcher = testDef(0,$data['noswitcher']); // No chart type switcher buttons
$series = testDef(0,$data['noseries']); // No more data button (2x2 series)
$export = testDef(0,$data['noexport']); // No data export buttons
	$exportsvg = testDef(0,$data['exportsvg']); // Chart's SVG HTML visible for export button, def: no
$embed = testDef(0,$data['noembed']); // No embed link visible
$embedtitle = testDef('Embed',$data['embedtitle']); // Custom title for embed

$jquery = testDef(0,$data['jquery']); // If jQuery should be loaded (eq not existing on blog before, default:existing)
if ($jquery)
	echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>';

// Including minimized version of d3.js: from CDN or local copy of server
if ($cdn)
	echo '<script src="http://d3js.org/d3.v3.min.js"></script>';
else
	echo '<script src="wp-content/plugins/d3-simpleCharts/d3.v3.min.js"></script>';

// Including our core JavaScript lib
/*
<link rel="stylesheet" type="text/css" href="wp-content/plugins/d3-simpleCharts/nvd3/nv.d3.css" />
<script src="wp-content/plugins/d3-simpleCharts/nvd3/nv.d3.min.js"></script>
*/
// if ($data['chart'] == 'line') {
	echo '<link rel="stylesheet" type="text/css" href="wp-content/plugins/d3-simpleCharts/rickshaw/rickshaw.min.css" />';
	echo '<script src="wp-content/plugins/d3-simpleCharts/rickshaw/rickshaw.min.js"></script>';
// }

// <link rel="stylesheet" type="text/css" href="wp-content/plugins/d3-simpleCharts/d3chart.css" />
?>
<script src="wp-content/plugins/d3-simpleCharts/d3-simpleCharts.js"></script>

<script>

// First things at first: generate the HTML -container for its new chart
var url = '<? echo $url ?>';
var chartid = 'chart<? echo $uniq ?>';
var tableid = 'table<? echo $uniq ?>';
var title = '<? echo $title ?>';
var url = '<? echo $url ?>';
id = '<? echo $args2js['id'] ?>';

// Moving to client's side JS processing now ...

// A magical glue: dumping server's php JSON for browser's JS variable, one line
var args2js = <?php echo json_encode($args2js) ?>;
// console.info(args2js);
// Writing data set into global array (debug and look this on FireBug/Chrome console: "d3charts")
if (typeof d3charts == 'undefined') 
	d3charts = new Array();
// d3charts[args2js.title] = args2js;
d3charts.push(args2js);

var rootp = 'wp-content/plugins/d3-simpleCharts/icons/';

// All existing chart types & their names
var ctype = ["'columns'","'bars'","'area'","'line'","'pie'"];
var cicons = ["columns.png","bars.png","area.png","line.png","pie.png"];
// Referring to just now added one for creating its buttons
var last_chart = d3charts.length-1;
var fontx = ' style="font-size:xx-small; cursor:pointer;" ';
var butts = '<span style="background-color:darkgray; float:right;">';
butts += ' <button '+fontx+'onclick="drawChart(d3charts['+last_chart+'],'+ctype[0]+')"> <img src="'+rootp+cicons[0]+'"></button>';
butts += ' <button '+fontx+'onclick="drawChart(d3charts['+last_chart+'],'+ctype[1]+')"> <img src="'+rootp+cicons[1]+'"></button>';
butts += ' <button '+fontx+'onclick="drawChart(d3charts['+last_chart+'],'+ctype[2]+')"> <img src="'+rootp+cicons[2]+'"></button>';
butts += ' <button '+fontx+'onclick="drawChart(d3charts['+last_chart+'],'+ctype[3]+')"> <img src="'+rootp+cicons[3]+'"> </button>';
butts += ' <button '+fontx+'onclick="drawChart(d3charts['+last_chart+'],'+ctype[4]+')"> <img src="'+rootp+cicons[4]+'"> </button></span>';

var otherbutt = ' <button '+fontx+' onclick="extendData()" title="Extend to other data sets."><?php echo $moredata ?></button>';

if (<?php echo $switcher ?>==1) {  // No buttons: chart switcher 
		butts = '';
}
if (<?php echo $series ?>==1) {  // No buttons: more data
		otherbutt = '';
}

// Embed link element
var cid = 'chart<? echo $uniq ?>';
var url2 = 'wp-content/plugins/d3-simpleCharts/embed.php';  // encodeURIComponent(el.innerText)
// var cid2 = "'"+cid+"'";
var cid2 = "'<? echo $uniq ?>'"; 

// embed link, TODO
// var elink = '<a href="'+url2+'?chartid='+showembed(cid2)+'" target="_blank"><?php echo $embedtitle ?></a>';
// var elink = '<a onclick="showembed('+cid2+')" target="_blank"><?php echo $embedtitle ?></a>';
elink = '';
// newwin = '';

// new window popup's opening
var logofile = '<?php echo testDef("",$data["logo"]) ?>';
	logofile = "'"+logofile+"'";
// style file name for popup charts - too
var cssfile = "'<?php echo $cssfile ?>'";

// var newwin = ' <a onclick="svgWin('+cid2+','+logofile+','+cssfile+',args2js)">new window</a> ';
var rootp = 'wp-content/plugins/d3-simpleCharts/';
// console.info(logofile);
var newwin = ' <button style="cursor:pointer" onclick="svgWin('+cid2+','+logofile+','+cssfile+',args2js)"><img src="'+rootp+'icons/newindow.jpg"></button> ';

var embed = '<tr><td></td><td style="text-align:right"><sub>'+elink+newwin+'</sub></td><tr>'; // TODO
var sortbutt = '<select '+fontx+' id="xsort" onchange="sort()"><option value="">Sort</option><option value="abc">1-2-3</option><option value="cba">3-2-1</option></select>';

// Our chart container in HTML is <table> element with custom styles
var html = '<br /><br /><table id= "'+ tableid +'" class="svgtable" style="<?php echo $backstyle ?>" width="'+(100+parseInt(args2js.width))+'">';
// if ('<? echo $embed ?>')
	html = html+embed;
html = html + '<tr><td class="titletext" style="<?php echo $mstyle ?>">'+butts+'<br /> <h3><?php echo $main ?></h3><?php echo $logo_top ?></td></tr>'; // Main title & logo (+ its CSS style)
html = html + '<tr><td id="extras" style="float:right">'+otherbutt+'</td><td>'+sortbutt+'</td></tr>';
var chartX = '<div style="" id="'+ chartid + '"></div>';
if (url) // Here is row where D3 draws its chart - finally
	html = html + '<tr><td><a id="'+ chartid + '" ' + title + ' ' + url + '></a></td></tr>';
else
	// html = html + '<tr><td id="'+ chartid + '" ' + title + '></td></tr>';
	html = html + '<tr><td ' + title + '>'+chartX+'</td></tr>';

var id = "'"+chartid+"'";
var odform = "'table'";
html = html + '<tr><td id="'+ id + '" title="Data values"></td></tr>'; // Container of big data

var cc = '<tr><td style="font-size:x-small; float:left">Run by <b>W3C</b> open technology </td><td><?php echo $logo ?></td></tr>';

var odataButt = '';
var odataButt2 = '';

if (<?php echo $export ?>==0) {

// Data export buttons
var odataButt = ' <button '+fontx+' onclick="openData(d3charts['+last_chart+'], '+id+')" title="Open chart\'s data for Copy & Paste to big data applications."> BIG DATA </button>';
var odataButt2 = ' <button '+fontx+' onclick="openData(d3charts['+last_chart+'], '+id+', '+odform+')" title="Open chart\'s data for Copy & Paste to Excel here."> Excel data </button>';
var odataButt3 = '';
if (<?php echo $exportsvg ?>==1) {
	odform="'svg'";
	odataButt3 = ' <button '+fontx+' onclick="openData(d3charts['+last_chart+'], '+id+', '+odform+', cid)" title="Open chart\'s html for Copy & Paste to web page here."> Chart </button>';
}
}

html = html + '<tr><td id="'+ chartid + 'odata" ' + title + ' style=" float:right;">'+odataButt3+odataButt+odataButt2+'</td></tr>'+cc; 
html = html + '</table>';

document.write(html); // This prints out chart (now at top of each WP page/post)

// Printing all data for input next - debug 
// var datas = <?php echo $points ?>;

if (args2js.data.length == 0) {

	// External file
	var datafile = "<?php echo $args2js["datafile"] ?>";
	if (!datafile) { // Input must exists
		console.error('Not found data input for chart from file or php direct call !');
	}
	// This is how d3.js wants to read external files: AJAX calls + GETs, not pretty but works
	if (datafile.indexOf('.tsv') > 0)
	d3.tsv(datafile, function(error, data) {
		args2js.data = data;
		drawChart(args2js);
	});
	if (datafile.indexOf('.csv') > 0)
	d3.csv(datafile, function(error, data) { 
		args2js.data = data;
		drawChart(args2js);
	});
	if (datafile.indexOf('.json') > 0)
	d3.json(datafile, function(error, data) {
		args2js.data = data;
		drawChart(args2js);
	});
	if (datafile.indexOf('.xml') > 0)
	d3.xml(datafile, function(error, data) {
		// console.info(data);
		// d3doc = data;
		return;  // NOT working yet: TODO NEXT ...
		args2js.data = data;
		drawChart(args2js);
	});
}
else // data is coming via php shortcode directly here
	drawChart(args2js);


function showembed(chartid) {

	var node = $('#'+chartid).html();
	console.info(node);
	// console.info(encodeURIComponent(node));
	// return encodeURIComponent(node);
	// '<a href="'+url2+'?chartid='+showembed(cid2)+'" target="_blank"><?php echo $embedtitle ?></a>'
}
</script>
<?php
};
add_shortcode("simpleCharts", "simpleBarsDev");
add_shortcode("SimpleCharts", "simpleBarsDev");
add_shortcode("simplecharts", "simpleBarsDev");
add_shortcode("Simplecharts", "simpleBarsDev");

add_shortcode("simpleChart", "simpleBarsDev");
add_shortcode("SimpleChart", "simpleBarsDev");
add_shortcode("simplechart", "simpleBarsDev");
add_shortcode("Simplechart", "simpleBarsDev");

add_shortcode("drawColumns", "simpleBarsDev");
add_shortcode("simpleChartsNew", "simpleBarsDev");

// All minor PHP functions

// Helps for setting of default arguments
function testDef($setupV, $userV) {
	if ($userV)
		return $userV;
	return $setupV;
}

/*
 	styleBars
	---------
	Generating CSS elements automatically from user's provided JSON data
 	+ printing this into its own style section on WP pages before actual new chart.
	
	Returnin unique id number for each new chart & its data set.

	Abit tricky function - sorry.
*/
function styleBars($cssdata) {

$uniq = rand();

// Parsing css data from json object => string
$cssdata = (array) json_decode($cssdata);
// var_dump($cssdata);
// echo '<br />';

$css = '';
if ($cssdata)
/*
	an input json from php's input array:
		{ ".bar" : { "fill" : "navy" } }
	& the target output: 
		.bar { "fill": "navy"; }
*/
foreach (array_keys($cssdata) as $gobject) {
	//	typical objects of chart: '.bar', '.axis path', etc
	$css .= '.g' . $uniq . ' ' . $gobject . ' { ';
	$tmp = (array) $cssdata[$gobject];
	// var_dump($tmp);
	foreach (array_keys($tmp) as $attr)
		// typical attributes: 'fill', 'display', etc
		$css .= $attr . ': ' . $tmp[$attr] . '; ';
	$css .= ' } ';
}
 echo '<style>' . $css . '</style>';
return $uniq;
}
/*
	getArr
	------
	Parsing user's str arrays (eq data's values & labels) -> real php array object
		an input format: "(a,b,c)"
		the output: array("a","b","c")
*/
function getArr($array) {

	$array = str_replace('(', '',$array);
	$array = str_replace(')', '',$array);
	$array = str_replace(', ', ',',$array);
	return explode(',',$array);  // cells must be separated by ',' letter
}

?>
