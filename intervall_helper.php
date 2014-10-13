<?php

$equal_left = 1;
$equal_right = 2;

$lt = 3;
$gt = 4;

$included = 5;

function testPointVsINtervall($point, $x1, $x2)
{
  $res = 0;

  global $equal_left;
  global $equal_right;
  global $lt;
  global $gt;
  global $included;

  if ($point < $x1) { $res = $lt; }
  elseif ($point > $x2) { $res = $gt; }
  elseif ($point == $x1) { $res = $equal_left; }
  elseif ($point == $x2) { $res = $equal_right; }
  else { $res = $included; }
    
  return $res;
}



/*

x1, x2  -> intervall in list
y1, y2  -> intervall to insert

isOpenX1, isOpenX2, isOpenY1, isOpenY2 -> intervall border open vs closed

returns 
 -> insert, if y1, y2 is left of x1, x2
 -> next if y1, y2 is right of x1, x2
 -> merge + new intervall
 -> right_expand + new intervall
 
*/
$error = 0;

$insert = 1;
$next = 2;
$merge = 3;
$expand_right = 4;

function calculateUnion($x1, $x2, $isOpenX1, $isOpenX2, $y1, $y2, $isOpenY1, $isOpenY2) {

// globals
  global $equal_left;
  global $equal_right;
  global $lt;
  global $gt;
  global $included;

  global $error;
  
  global $insert;
  global $next;
  global $merge;
  global $expand_right;

// return values
  $result = $error;
  $z1 = 0;
  $z2 = 0;
  $isOpenZ1 = false;
  $isOpenZ2 = false;

  switch ( testPointVsINtervall($y1,$x1,$x2)) {
    case $equal_left:
      echo "here: " . $equal_left;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " + " . $equal_left . " -> merge";
	
	  $result = $merge;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1 && $isOpenY2;
	  $isOpenZ2 = $isOpenX2;
	
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> expand_right";
	
	  $result = $expand_right;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;

	  break;
	case $lt:
	  echo " error " . $lt; 
	  break;
	case $gt:
	  echo " + " . $gt. " -> expand_right";
	
	  $result = $expand_right;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenY2;	

	  break;
	case $included:
	  echo " + " . $included. " -> merge";
	
	  $result = $merge;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $equal_right:
      echo "here " . $equal_right;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " error " . $equal_left;
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> expand_right";
	  
	  $result = $expand_right;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY1 && $isOpenY2;
	  
	  break;
	case $lt:
	  echo " error " . $lt;
	  break;
	case $gt:
	  echo " + " . $gt;
	  
	  if ($isOpenX2 && $isOpenY1) {
	    echo " -> next";
	    
	    $result = $next;
	  } else {
	    echo " -> expand_right";
	    
	    $result = $expand_right;
	    $z1 = $x1;
	    $z2 = $y2;
	    $isOpenZ1 = $isOpenX1;
	    $isOpenZ2 = $isOpenY2;
	  }
	  break;
	case $included:
	  echo " error " . $included;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $lt:
      echo "here " . $lt;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " + " . $equal_left;
	  
	  if ($isOpenX1 && $isOpenY2) {
	    echo " -> insert";
	    
	    $result = $insert;
	  } else {
	    echo " -> merge";
	    
	    $result = $merge;
	    $z1 = $y1;
	    $z2 = $x2;
	    $isOpenZ1 = $isOpenY1;
	    $isOpenZ2 = $isOpenX2;
	  }
	  
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> expand_right";
	  
	  $result = $expand_right;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case $lt:
	  echo " + " . $lt . " -> insert";
	  
	  $result = $insert;
	  break;
	case $gt:
	  echo " + " . $gt . " -> expand_right";
	  
	  $result = $expand_right;
	  $z1 = $y1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenY2;
	  
	  break;
	case $included:
	  echo " + " . $included . " -> merge";
	  
	  $result = $merge;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $gt:
      echo "here " . $gt;
    
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $gt:    
	  echo " + " . $gt . " -> next";
	  $result = $next;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case $included:
      echo "here " . $included;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " error " . $equal_left;
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> expand_right";
	  
	  $result = $expand_right;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case $lt:
	  echo " error " . $lt;
	  break;
	case $gt:
	  echo " + " . $gt . " -> expand_right";
	  
	  $result = $expand_right;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenY2;
	  break;
	case $included:
	  echo " + " . $included . " -> merge";
	  
	  $result = $merge;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    // fallthrough
    default:
      echo "error";
  }
  
  echo " ";
  
  if ($result == $error || $result == $insert || $result == $next) {
    return ["result" => $result];
  } else {
    return ["result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2];
  }
}

/*








*/

$doIntersectStop = 1;
$doIntersectContiniue = 2;
$doNotIntersectContiniue = 3;
$doNotIntersectStop = 4;

function calculateIntersection($x1, $x2, $isOpenX1, $isOpenX2, $y1, $y2, $isOpenY1, $isOpenY2) {

// globals
  global $equal_left;
  global $equal_right;
  global $lt;
  global $gt;
  global $included;

  global $error;
  
  global $doIntersectContiniue;
  global $doIntersectStop;
  global $doNotIntersectContiniue;
  global $doNotIntersectStop;

// return values
  $result = $error;
  $z1 = 0;
  $z2 = 0;
  $isOpenZ1 = false;
  $isOpenZ2 = false;

  switch ( testPointVsINtervall($y1,$x1,$x2)) {
    case $equal_left:
      echo "here: " . $equal_left;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  if ($isOpenX1 || $isOpenY1 || $isOpenY2) {
	    echo " + " . $equal_left . " -> doNotIntersectStop";
	    
	    $result = $doNotIntersectStop;
	  } else {
	    echo " + " . $equal_left . " -> doIntersectStop";
	    
	    $result = $doIntersectStop;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> doIntersectContiniue";
	
	  $result = $doIntersectContiniue;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;

	  break;
	case $lt:
	  echo " error " . $lt; 
	  break;
	case $gt:
	  echo " + " . $gt. " -> doIntersectContiniue";
	
	  $result = $doIntersectContiniue;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
	case $included:
	  echo " + " . $included. " -> doIntersectStop";
	
	  $result = $doIntersectStop;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenY2;	

	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $equal_right:
      echo "here " . $equal_right;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " error " . $equal_left;
	  break;
	case $equal_right:
	  if ($isOpenX2 || $isOpenY1 || $isOpenY2) {
	    echo " + " . $equal_left . " -> doNotIntersectStop";
	    
	    $result = $doNotIntersectContiniue;
	  } else {
	    echo " + " . $equal_left . " -> doIntersectContiniue";
	    
	    $result = $doIntersectContiniue;
	    $z1 = $x2;
	    $z2 = $x2;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }	  
	  break;
	case $lt:
	  echo " error " . $lt;
	  break;
	case $gt:
	  echo " + " . $gt;
	  
	  if ($isOpenX2 || $isOpenY1) {
	    echo " -> doNotIntersectContiniue";
	    
	    $result = $doNotIntersectContiniue;
	  } else {
	    echo " -> doIntersectContiniue";
	    
	    $result = $doIntersectContiniue;
	    $z1 = $x2;
	    $z2 = $x2;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  break;
	case $included:
	  echo " error " . $included;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $lt:
      echo "here " . $lt;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " + " . $equal_left;
	  
	  if ($isOpenX1 || $isOpenY2) {
	    echo " -> doNotIntersectStop";
	    
	    $result = $doNotIntersectStop;
	  } else {
	    echo " -> doIntersectStop";
	    
	    $result = $doIntersectStop;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> doIntersectContiniue";
	  
	  $result = $doIntersectContiniue;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;
	  break;
	case $lt:
	  echo " + " . $lt . " -> doNotIntersectStop";
	  
	  $result = $doNotIntersectStop;
	  break;
	case $gt:
	  echo " + " . $gt . " -> doIntersectContiniue";
	  
	  $result = $doIntersectContiniue;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2;
	  
	  break;
	case $included:
	  echo " + " . $included . " -> doIntersectStop";
	  
	  $result = $doIntersectStop;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenY2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case $gt:
      echo "here " . $gt;
    
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $gt:    
	  echo " + " . $gt . " -> doNotIntersectContiniue";
	  $result = $doNotIntersectContiniue;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case $included:
      echo "here " . $included;
 
      switch ( testPointVsINtervall($y2,$x1,$x2)) {
	case $equal_left:
	  echo " error " . $equal_left;
	  break;
	case $equal_right:
	  echo " + " . $equal_right . " -> doIntersectContiniue";
	  
	  $result = $doIntersectContiniue;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;
	  break;
	case $lt:
	  echo " error " . $lt;
	  break;
	case $gt:
	  echo " + " . $gt . " -> doIntersectContiniue";
	  
	  $result = $doIntersectContiniue;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2;
	  break;
	case $included:
	  echo " + " . $included . " -> doIntersectStop";
	  
	  $result = $doIntersectStop;
	  $z1 = $y1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenY2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    // fallthrough
    default:
      echo "error";
  }
  
  echo " ";
  
  if ($result == $error || $result == $doNotIntersectStop || $result == $doNotIntersectContiniue) {
    return ["result" => $result];
  } else {
    return ["result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2];
  }
}

?>