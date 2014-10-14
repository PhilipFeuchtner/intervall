<?php

define ('EQUAL_LEFT', 1);
define ('EQUAL_RIGHT', 2);

define ('LT', 3);
define ('GT', 4);

define ('INCLUDED', 5);

function testPointVsInterval($point, $x1, $x2)
{
  $res = 0;

  if ($point < $x1) { $res = LT; }
  elseif ($point > $x2) { $res = GT; }
  elseif ($point == $x1) { $res = EQUAL_LEFT; }
  elseif ($point == $x2) { $res = EQUAL_RIGHT; }
  else { $res = INCLUDED; }
    
  return $res;
}



/*

x1, x2  -> interval in list
y1, y2  -> interval to insert

isOpenX1, isOpenX2, isOpenY1, isOpenY2 -> interval border open vs closed

returns 
 -> insert, if y1, y2 is left of x1, x2
 -> next if y1, y2 is right of x1, x2
 -> merge + new interval
 -> right_expand + new interval
 
*/
define ('ERROR', 0);

define ('INSERT', 1);
define ('NEXT', 2);
define ('MERGE', 3);
define ('EXPAND_RIGHT', 4);

function calculateUnion($x1, $x2, $isOpenX1, $isOpenX2, $y1, $y2, $isOpenY1, $isOpenY2) {

// return values
  $result = ERROR;
  $z1 = 0;
  $z2 = 0;
  $isOpenZ1 = false;
  $isOpenZ2 = false;

  switch ( testPointVsInterval($y1,$x1,$x2)) {
    case EQUAL_LEFT:
      echo "here: " . EQUAL_LEFT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " + " . EQUAL_LEFT . " -> merge";
	
	  $result = MERGE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1 && $isOpenY2;
	  $isOpenZ2 = $isOpenX2;
	
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> expand_right";
	
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;

	  break;
	case LT:
	  echo " error " . LT; 
	  break;
	case GT:
	  echo " + " . GT. " -> expand_right";
	
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenY2;	

	  break;
	case INCLUDED:
	  echo " + " . INCLUDED. " -> merge";
	
	  $result = MERGE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case EQUAL_RIGHT:
      echo "here " . EQUAL_RIGHT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY1 && $isOpenY2;
	  
	  break;
	case LT:
	  echo " error " . LT;
	  break;
	case GT:
	  echo " + " . GT;
	  
	  if ($isOpenX2 && $isOpenY1) {
	    echo " -> next";
	    
	    $result = NEXT;
	  } else {
	    echo " -> expand_right";
	    
	    $result = EXPAND_RIGHT;
	    $z1 = $x1;
	    $z2 = $y2;
	    $isOpenZ1 = $isOpenX1;
	    $isOpenZ2 = $isOpenY2;
	  }
	  break;
	case INCLUDED:
	  echo " error " . INCLUDED;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case LT:
      echo "here " . LT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " + " . EQUAL_LEFT;
	  
	  if ($isOpenX1 && $isOpenY2) {
	    echo " -> insert";
	    
	    $result = INSERT;
	  } else {
	    echo " -> merge";
	    
	    $result = MERGE;
	    $z1 = $y1;
	    $z2 = $x2;
	    $isOpenZ1 = $isOpenY1;
	    $isOpenZ2 = $isOpenX2;
	  }
	  
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case LT:
	  echo " + " . LT . " -> insert";
	  
	  $result = INSERT;
	  break;
	case GT:
	  echo " + " . GT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $y1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenY2;
	  
	  break;
	case INCLUDED:
	  echo " + " . INCLUDED . " -> merge";
	  
	  $result = MERGE;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case GT:
      echo "here " . GT;
    
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case GT:    
	  echo " + " . GT . " -> next";
	  $result = NEXT;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case INCLUDED:
      echo "here " . INCLUDED;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case LT:
	  echo " error " . LT;
	  break;
	case GT:
	  echo " + " . GT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenY2;
	  break;
	case INCLUDED:
	  echo " + " . INCLUDED . " -> merge";
	  
	  $result = MERGE;
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
  
  if ($result == ERROR || $result == INSERT || $result == NEXT) {
    return ["result" => $result];
  } else {
    return ["result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2];
  }
}

/*








*/

define ('DOINTERSECT_STOP', 1);
define ('DOINTERSECT_CONTINIUE', 2);
define ('DONOTINTERSECT_CONTINIUE', 3);
define ('DONOTINTERSECT_STOP', 4);

function calculateIntersection($x1, $x2, $isOpenX1, $isOpenX2, $y1, $y2, $isOpenY1, $isOpenY2) {

// return values
  $result = ERROR;
  $z1 = 0;
  $z2 = 0;
  $isOpenZ1 = false;
  $isOpenZ2 = false;

  switch ( testPointVsInterval($y1,$x1,$x2)) {
    case EQUAL_LEFT:
      echo "here: " . EQUAL_LEFT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  if ($isOpenX1 || $isOpenY1 || $isOpenY2) {
	    echo " + " . EQUAL_LEFT . " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_STOP;
	  } else {
	    echo " + " . EQUAL_LEFT . " -> doIntersectStop";
	    
	    $result = DOINTERSECT_STOP;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;

	  break;
	case LT:
	  echo " error " . LT; 
	  break;
	case GT:
	  echo " + " . GT. " -> doIntersectContiniue";
	
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
	case INCLUDED:
	  echo " + " . INCLUDED. " -> doIntersectStop";
	
	  $result = DOINTERSECT_STOP;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenY2;	

	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case EQUAL_RIGHT:
      echo "here " . EQUAL_RIGHT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  if ($isOpenX2 || $isOpenY1 || $isOpenY2) {
	    echo " + " . EQUAL_LEFT . " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_CONTINIUE;
	  } else {
	    echo " + " . EQUAL_LEFT . " -> doIntersectContiniue";
	    
	    $result = DOINTERSECT_CONTINIUE;
	    $z1 = $x2;
	    $z2 = $x2;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }	  
	  break;
	case LT:
	  echo " error " . LT;
	  break;
	case GT:
	  echo " + " . GT;
	  
	  if ($isOpenX2 || $isOpenY1) {
	    echo " -> doNotIntersectContiniue";
	    
	    $result = DONOTINTERSECT_CONTINIUE;
	  } else {
	    echo " -> doIntersectContiniue";
	    
	    $result = DOINTERSECT_CONTINIUE;
	    $z1 = $x2;
	    $z2 = $x2;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  break;
	case INCLUDED:
	  echo " error " . INCLUDED;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case LT:
      echo "here " . LT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " + " . EQUAL_LEFT;
	  
	  if ($isOpenX1 || $isOpenY2) {
	    echo " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_STOP;
	  } else {
	    echo " -> doIntersectStop";
	    
	    $result = DOINTERSECT_STOP;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;
	  break;
	case LT:
	  echo " + " . LT . " -> doNotIntersectStop";
	  
	  $result = DONOTINTERSECT_STOP;
	  break;
	case GT:
	  echo " + " . GT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2;
	  
	  break;
	case INCLUDED:
	  echo " + " . INCLUDED . " -> doIntersectStop";
	  
	  $result = DOINTERSECT_STOP;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenY2;
	  break;
    
	default:
	  echo "error";
      }
    
      break;
    
    case GT:
      echo "here " . GT;
    
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case GT:    
	  echo " + " . GT . " -> doNotIntersectContiniue";
	  $result = DONOTINTERSECT_CONTINIUE;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case INCLUDED:
      echo "here " . INCLUDED;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;
	  break;
	case LT:
	  echo " error " . LT;
	  break;
	case GT:
	  echo " + " . GT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2;
	  break;
	case INCLUDED:
	  echo " + " . INCLUDED . " -> doIntersectStop";
	  
	  $result = DOINTERSECT_STOP;
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
  
  if ($result == ERROR || $result == DONOTINTERSECT_STOP || $result == DONOTINTERSECT_CONTINIUE) {
    return ["result" => $result];
  } else {
    return ["result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2];
  }
}

?>