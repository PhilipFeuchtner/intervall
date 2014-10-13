<?php
include "intervall_helper.php";

function testTestPointVsINtervall() {
  foreach (array(1, 2, 3, 4, 5) as $value) {
    echo( $value . " -> " . testPointVsINtervall($value, 2,4) . "\n"); 
  }
}

echo "*** test1: testTestPointVsINtervall\n";
testTestPointVsINtervall();


function testCalulateUnion() {

  $x1 = 3;
  $x2 = 6; 
  $isOpenX1 = false;
  $isOpenX1 = false;

  $valuesy2 = array(2, 3, 4, 5, 6 ,7, 8);
  $isOpenY1 = false;
  $isOpeny2 = false;

  foreach (array(1, 2, 3, 4, 5, 6, 7) as $y1) {
    foreach ($valuesy2 as $y2) {
      echo "- " . $x1 . " " . $x2 . ", " . $y1 . " " . $y2 . " -> ";
      echo "/ " . var_dump(calculateUnion($x1, $x2, true, false, $y1, $y2, true,false)) . "\n"; 
    }
    
    array_shift($valuesy2);
    echo "\n";
  }
}

echo "*** test2: testCalulateUnion\n";
testCalulateUnion();

function testCalulateIntersection() {

  $x1 = 3;
  $x2 = 6; 
  $isOpenX1 = false;
  $isOpenX1 = false;

  $valuesy2 = array(2, 3, 4, 5, 6 ,7, 8);
  $isOpenY1 = false;
  $isOpeny2 = false;

  foreach (array(1, 2, 3, 4, 5, 6, 7) as $y1) {
    foreach ($valuesy2 as $y2) {
      echo "- " . $x1 . " " . $x2 . ", " . $y1 . " " . $y2 . " -> ";
      echo "/ " . var_dump(calculateIntersection($x1, $x2, true, false, $y1, $y2, true,false)) . "\n"; 
    }
    
    array_shift($valuesy2);
    echo "\n";
  }
}

echo "*** test3: testCalulateIntersection\n";
testCalulateIntersection();
?>