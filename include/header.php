<?php
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
$fontsize = $detect->isMobile() ? "7pt" : "10pt";

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
<html lang=\"ru\"><head><title>$title</title>
<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html;charset=UTF-8\" />
<meta name=\"viewport\" content=\"width=device-width\" />
<style type=\"text/css\">
";
?>
body	{
	background-color : #FFFFFF;
	}
footer {
	font-family : Ubuntu, Verdana, sans-serif; font-size : 7pt; color: #696969; background-color : #FFFFFF; text-align: right;
	}
span.c1 {
	color : red;
	}
span.c2 {
	color : green;
	}
ul.qr1 {
	font-family : Ubuntu, Verdana, sans-serif; font-size : 9pt; background-color : #FFFFFF;
	}
span.m1 {
	font-family : Ubuntu, Verdana, sans-serif; font-size : 80%;
	}
span.m1_red {
	font-family : Ubuntu, Verdana, sans-serif; font-size : 80%; color: #FF0000;
	}
div.d1 {
	text-align:center;
	font-family : Ubuntu, Verdana, sans-serif; font-size : 9pt; background-color : #FFFFFF;
	}
form {
	margin: 0; 
	}
span.c4 {
	font-family : Ubuntu, Verdana, sans-serif; font-size : 9pt;
	border-bottom: 1px dashed red; /* Пунктирное подчеркивание текста */
    }
span.mono1 {
	font-family : \"Courier New\", Courier, monospace; font-size: 110%;
        font-weight: bold;
        }
select  {
<?php
echo "	font-family : Ubuntu, Verdana, sans-serif; font-size : $fontsize; }";
?>
input  {
<?php
echo "	font-family : Ubuntu, Verdana, sans-serif; font-size : $fontsize; }";
?>
button  {
<?php
echo "	font-family : Ubuntu, Verdana, sans-serif; font-size : $fontsize; }";
?>
.features-table
{
  font-family : Ubuntu, Verdana, sans-serif;
  font-size:<?php echo $fontsize; ?>;
  width: 100%;
  margin: 0 auto;
  border-collapse: separate;
  border-spacing: 0;
  border: 0;
  text-shadow: 0 1px 0 #fff;
  color: #2a2a2a;
  background: #fafafa;
  background-image: -moz-linear-gradient(top, #fff, #eaeaea, #fff); /* Firefox 3.6 */
  background-image: -webkit-gradient(linear,center bottom,center top,from(#fff),color-stop(0.5, #eaeaea),to(#fff));
  margin-top:0px;
  margin-bottom:3px;
}
 
.features-table td
{
  font-family : Ubuntu, Verdana, sans-serif;
  font-size:<?php echo $fontsize; ?>;
  height: 25px;
  padding: 0 5px;
  border-bottom: 1px solid #cdcdcd;
  box-shadow: 0 1px 0 white;
  -moz-box-shadow: 0 1px 0 white;
  -webkit-box-shadow: 0 1px 0 white;
  text-align: center;
  vertical-align: middle;
  display: table-cell;
}
.features-table td.client
{
  font-family : Ubuntu, Verdana, sans-serif;
  font-size: 80%;
  height: 15px;
  padding: 0 0px;
  border-bottom: 1px solid #cdcdcd;
  box-shadow: 0 1px 0 white;
  -moz-box-shadow: 0 1px 0 white;
  -webkit-box-shadow: 0 1px 0 white;
  text-align: left;
  vertical-align: top;
  display: table-cell;
} 
.features-table td.work
{
  font-family : Ubuntu, Verdana, sans-serif;
  font-size: 90%;
  padding: 0 0px;
  border-bottom: 1px solid #cdcdcd;
  box-shadow: 0 1px 0 white;
  -moz-box-shadow: 0 1px 0 white;
  -webkit-box-shadow: 0 1px 0 white;
  text-align: left;
  vertical-align: center;
  display: table-cell;
} 
.features-table td.client_h
{
  font-family : Ubuntu, Verdana, sans-serif;
  font-size:<?php echo $fontsize; ?>;
  padding: 0 5px;
  border-bottom: 1px solid #cdcdcd;
  box-shadow: 0 1px 0 white;
  -moz-box-shadow: 0 1px 0 white;
  -webkit-box-shadow: 0 1px 0 white;
  text-align: left;
  vertical-align: top;
  display: table-cell;
} 
.features-table tbody td
{
  text-align: center;
}

.features-table td.left
{
  text-align: left;
} 
 
.features-table td.grey
{
  background: #efefef;
  background: rgba(144,144,144,0.15);
  border-right: 1px solid white;
}

.features-table td.grey_left
{
  background: #efefef;
  background: rgba(144,144,144,0.15);
  border-right: 1px solid white;
  text-align: left;
}
 
.features-table td.green
{
  background: #e7f3d4;
  background: rgba(184,243,85,0.3);
  border-right: 1px solid white;
}

.features-table td.green2
{
  background: #90c738;
  background: rgba(144, 199, 56,0.3);
  border-right: 1px solid white;
}

.features-table td.green_left
{
  background: #e7f3d4;
  background: rgba(184,243,85,0.3);
  border-right: 1px solid white;
  text-align: left;
}

.features-table td.red
{
  background: #FF9999;
  background: rgba(255,153,153,0.6);
  border-right: 1px solid white;
}

.features-table td.red2
{
  background: #ff99dd;
  background: rgba(255,153,221,0.6);
  border-right: 1px solid white;
}

.features-table td.blue
{
  background: #66ccff;
  background: rgba(102, 204, 255,0.3);
  border-right: 1px solid white;
}
.features-table td.blue_left
{
  background: #66ccff;
  background: rgba(102, 204, 255,0.3);
  border-right: 1px solid white;
  text-align: left;
}
.features-table td.blue2
{
  background: #89bcfa;
  background: rgba(137, 188, 250,0.3);
  border-right: 1px solid white;
}
.features-table td.blue2_left
{
  background: #89bcfa;
  background: rgba(137, 188, 250,0.3);
  border-right: 1px solid white;
  text-align: left;
}

.features-table td:nowrap
{
  white-space: nowrap;
}
 
.features-table thead td
{
  font-size: 105%;
  font-weight: bold;
  -moz-border-radius-topright: 10px;
  -moz-border-radius-topleft: 10px;
  border-top-right-radius: 10px;
  border-top-left-radius: 10px;
  border-top: 1px solid #eaeaea;
}
 
.features-table tfoot td
{
  -moz-border-radius-bottomright: 10px;
  -moz-border-radius-bottomleft: 10px;
  border-bottom-right-radius: 10px;
  border-bottom-left-radius: 10px;
  border-bottom: 1px solid #dadada;
}
.block1 { 
    width: 100%; 
    background: #fff;
    border: solid 1px black; 
    float: left;
    font-size : 85%;
}
</style>
</head><body onUnload='closeAllWin()'>
<script src="/include/jquery.min.js" type="text/javascript"></script>
<script src="/include/js.cookie.min.js" type="text/javascript"></script>
<script src="/include/functions.js" type="text/javascript"></script>
<script type='text/javascript'>
function closeAllWin(){
    NewWin1.close();
    NewWin2.close();
    NewWin3.close();
    NewWin4.close();
    NewWin5.close();
    NewWin6.close();
    NewWin7.close();
    NewWin8.close();
    r1.close();
}
function set_lang(par1){
    Cookies.set('bil_s_lang', par1, { expires: 365 });
    //$.cookie('bil_s_lang', par1,{ path : '/'});
    location.reload();
    return false;
}
</script>