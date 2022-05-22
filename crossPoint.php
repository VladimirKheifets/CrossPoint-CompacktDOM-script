<?
/*

CrossPoint

PHP script

Version: 1.0

Author: Vladimir Kheifets (kheifets.vladimir@online.de)

Copyright ©2022 Vladimir Kheifets All Rights Reserved

*/
$req = file_get_contents('php://input');
file_put_contents("crossPointRequest.json", $req);
$pXY = json_decode($req, true);
//------------------------------------------------------
function crossPointK($XY1, $XY2, $X0, $Y0){
  $x1 = $XY1["Xad"];
  $y1 = $XY1["Yad"];
  $x2 = $XY1["Xbd"];
  $y2 = $XY1["Ybd"];

  $x3 = $XY2["Xad"];
  $y3 = $XY2["Yad"];
  $x4 = $XY2["Xbd"];
  $y4 = $XY2["Ybd"];
  //----------------------------------
  $z1 = ($x3-$x1)*($y2-$y1)-($y3-$y1)*($x2-$x1);
  $z2 = ($x4-$x1)*($y2-$y1)-($y4-$y1)*($x2-$x1);
  $z3 = ($x1-$x3)*($y4-$y3)-($y1-$y3)*($x4-$x3);
  $z4 = ($x2-$x3)*($y4-$y3)-($y2-$y3)*($x4-$x3);
  $crossPointCheck = ($z3*$z4>0 || $z1*$z2>0)?false:true;
  //----------------------------------
  if($crossPointCheck)
  {
    $k1 = ($y1 - $y2) / ($x1 - $x2);
    $b1 = $y2 - $k1 * $x2;
    $k2 = ($y3 - $y4) / ($x3 - $x4);
    $b2 = $y4 - $k2 * $x4;
    $Xd = round(($b2 - $b1)/($k1 - $k2));
    $Yd = round($k1*$Xd + $b1);
    $X = $Xd + $X0;
    $Y = $Y0 - $Yd;
    return (object)
    [
      "crossPointCheck" => true,
      "X" => $X,
      "Y" => $Y,
      "Xd" =>$Xd,
      "Yd" => $Yd
    ];
  }
  else
  {
    return (object)
    [
      "crossPointCheck"=>false
    ];
  }
};
//-------------------------------------------------------
$line1 = (array) $pXY[1]["line"];
$line2 = (array) $pXY[2]["line"];
$XY1 = empty($line1)?(array) $pXY[1]:$line1;
$XY2 = empty($line2["Xa"])?(array) $pXY[2]:$line2;
$width = (int) $pXY["width"];
$height = (int) $pXY["height"];
$X0 =  $width/2 - 1;
$Y0 =  $height/2 - 1;
$XYcp = crossPointK($XY1, $XY2, $X0, $Y0);
$rsp = json_encode($XYcp);
// save Request and Response
file_put_contents("crossPointRequest.json", $req);
file_put_contents("crossPointResponse.json", $rsp);

if($_GET['pr']==1)
{
  //Print Request/Response
  echo "<h1>AJAX Request/Response</h1>";
  echo "<pre>";
  echo "<h2>JSON-Request</h2>";
  echo json_encode(json_decode($req), JSON_PRETTY_PRINT);
  echo "<h2>JSON-Response</h2>";
  echo json_encode($XYcp, JSON_PRETTY_PRINT);
}
else if($_GET['pr']==2)
{
  //Print Data structure
  echo "<pre>";
  echo "<h1>Data structure</h1>";
  echo "<h2>object pXY</h2>";
  echo json_encode(json_decode($req), JSON_PRETTY_PRINT);
}
else
{
  //- Send json Response ------------------------------------------
  header("Content-type: application/json; charset=utf-8");
  echo $rsp;
}
//---------------------------------------------------------------
?>