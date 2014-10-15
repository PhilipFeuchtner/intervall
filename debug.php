<?php

include "interval.php";

function testTestPointVsInterval() {
  foreach (array(1, 2, 3, 4, 5) as $value) {
    echo( $value . " -> " . testPointVsInterval($value, 2,4) . "\n"); 
  }
}

echo "*** test1: testTestPointVsInterval\n";
// testTestPointVsInterval();


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
// testCalulateUnion();

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
// testCalulateIntersection();

$x1 = [10, 40, 42, 60];
$x2 = [20, 50, 45, 70];
$isOpenX1 = [false, false, false, false];
$isOpenX2 = [false, false, false, false];

function testTraverseUnion() {
  global $x1;
  global $x2;
  global $isOpenX1;
  global $isOpenX2;

  var_dump(traverseUnion($x1, $x2, $isOpenX1, $isOpenX2));
}

function testTraverseIntersect($x1, $x2, $isOpenX1, $isOpenX2) {
}


echo "*** test4: testTraverseUnion\n";
testTraverseUnion();

echo "*** test5: testTraverseIntersect\n";
// testTraverseIntersect($x1, $x2, $isOpenX1, $isOpenX2);

echo "*** test6: testParseString\n";
var_dump(parseString("(1,4] U [3,6)"));

echo "*** test7: toString\n";
echo toString($x1, $x2, $isOpenX1, $isOpenX2) ."\n";

echo "*** test8: canonicInterval\n";
echo canonicInterval( "(1,4] U [3,6)" ) . "\n";


?>