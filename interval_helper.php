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

$emptySet = array( "left-border" => 0,
		   "right-border" => 0,
		   "is-open-left" => true,
		   "is-open-right" => true );

/*

x1, x2  -> interval in list
y1, y2  -> interval to insert

isOpenX1, isOpenX2, isOpenY1, isOpenY2 -> interval border open vs closed

returns 
 -> insert, if y1, y2 is left of x1, x2
 -> SKIP if y1, y2 is right of x1, x2
 -> merge + new interval
 -> right_expand + new interval
 
*/
define ('ERROR', 0);

define ('INSERT', 1);
define ('SKIP', 2);
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
      // echo "here: " . EQUAL_LEFT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " + " . EQUAL_LEFT . " -> merge";
	
	  $result = MERGE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1 && $isOpenY2;
	  $isOpenZ2 = $isOpenX2;
	
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> expand_right";
	
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;

	  break;
	case LT:
	  // echo " error " . LT; 
	  break;
	case GT:
	  // echo " + " . GT. " -> expand_right";
	
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenY2;	

	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED. " -> merge";
	
	  $result = MERGE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 && $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
    
	default:
	  // echo "error";
      }
    
      break;
    
    case EQUAL_RIGHT:
      // echo "here " . EQUAL_RIGHT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY1 && $isOpenY2;
	  
	  break;
	case LT:
	  // echo " error " . LT;
	  break;
	case GT:
	  // echo " + " . GT;
	  
	  if ($isOpenX2 && $isOpenY1) {
	    // echo " -> SKIP";
	    
	    $result = SKIP;
	  } else {
	    // echo " -> expand_right";
	    
	    $result = EXPAND_RIGHT;
	    $z1 = $x1;
	    $z2 = $y2;
	    $isOpenZ1 = $isOpenX1;
	    $isOpenZ2 = $isOpenY2;
	  }
	  break;
	case INCLUDED:
	  // echo " error " . INCLUDED;
	  break;
    
	default:
	  // echo "error";
      }
    
      break;
    
    case LT:
      // echo "here " . LT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " + " . EQUAL_LEFT;
	  
	  if ($isOpenX1 && $isOpenY2) {
	    // echo " -> insert";
	    
	    $result = INSERT;
	  } else {
	    // echo " -> merge";
	    
	    $result = MERGE;
	    $z1 = $y1;
	    $z2 = $x2;
	    $isOpenZ1 = $isOpenY1;
	    $isOpenZ2 = $isOpenX2;
	  }
	  
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case LT:
	  // echo " + " . LT . " -> insert";
	  
	  $result = INSERT;
	  break;
	case GT:
	  // echo " + " . GT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $y1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenY2;
	  
	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED . " -> merge";
	  
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
      // echo "here " . GT;
    
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case GT:    
	  // echo " + " . GT . " -> SKIP";
	  $result = SKIP;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case INCLUDED:
      // echo "here " . INCLUDED;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 && $isOpenY2;
	  break;
	case LT:
	  // echo " error " . LT;
	  break;
	case GT:
	  // echo " + " . GT . " -> expand_right";
	  
	  $result = EXPAND_RIGHT;
	  $z1 = $x1;
	  $z2 = $y2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenY2;
	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED . " -> merge";
	  
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
  
  // echo " ";
  
  if ($result == ERROR || $result == INSERT || $result == SKIP) {
    return array("result" => $result);
  } else {
    return array("result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2);
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
      // echo "here: " . EQUAL_LEFT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  if ($isOpenX1 || $isOpenY1 || $isOpenY2) {
	    // echo " + " . EQUAL_LEFT . " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_STOP;
	  } else {
	    // echo " + " . EQUAL_LEFT . " -> doIntersectStop";
	    
	    $result = DOINTERSECT_STOP;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	
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
	  // echo " + " . GT. " -> doIntersectContiniue";
	
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1 || $isOpenY1;
	  $isOpenZ2 = $isOpenX2;	

	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED. " -> doIntersectStop";
	
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
      // echo "here " . EQUAL_RIGHT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  if ($isOpenX2 || $isOpenY1 || $isOpenY2) {
	    // echo " + " . EQUAL_LEFT . " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_CONTINIUE;
	  } else {
	    // echo " + " . EQUAL_LEFT . " -> doIntersectContiniue";
	    
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
	  // echo " + " . GT;
	  
	  if ($isOpenX2 || $isOpenY1) {
	    // echo " -> doNotIntersectContiniue";
	    
	    $result = DONOTINTERSECT_CONTINIUE;
	  } else {
	    // echo " -> doIntersectContiniue";
	    
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
      // echo "here " . LT;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  // echo " + " . EQUAL_LEFT;
	  
	  if ($isOpenX1 || $isOpenY2) {
	    // echo " -> doNotIntersectStop";
	    
	    $result = DONOTINTERSECT_STOP;
	  } else {
	    // echo " -> doIntersectStop";
	    
	    $result = DOINTERSECT_STOP;
	    $z1 = $x1;
	    $z2 = $x1;
	    $isOpenZ1 = false;
	    $isOpenZ2 = false;
	  }
	  
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2 || $isOpenY2;
	  break;
	case LT:
	  // echo " + " . LT . " -> doNotIntersectStop";
	  
	  $result = DONOTINTERSECT_STOP;
	  break;
	case GT:
	  // echo " + " . GT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $x1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenX1;
	  $isOpenZ2 = $isOpenX2;
	  
	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED . " -> doIntersectStop";
	  
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
      // echo "here " . GT;
    
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case GT:    
	  // echo " + " . GT . " -> doNotIntersectContiniue";
	  $result = DONOTINTERSECT_CONTINIUE;
	  break;
	default:
	  echo "error";
      }
  
      break;
      
    case INCLUDED:
      // echo "here " . INCLUDED;
 
      switch ( testPointVsInterval($y2,$x1,$x2)) {
	case EQUAL_LEFT:
	  echo " error " . EQUAL_LEFT;
	  break;
	case EQUAL_RIGHT:
	  // echo " + " . EQUAL_RIGHT . " -> doIntersectContiniue";
	  
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
	  // echo " + " . GT . " -> doIntersectContiniue";
	  
	  $result = DOINTERSECT_CONTINIUE;
	  $z1 = $y1;
	  $z2 = $x2;
	  $isOpenZ1 = $isOpenY1;
	  $isOpenZ2 = $isOpenX2;
	  break;
	case INCLUDED:
	  // echo " + " . INCLUDED . " -> doIntersectStop";
	  
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
  
  // echo " ";
  
  if ($result == ERROR || $result == DONOTINTERSECT_STOP || $result == DONOTINTERSECT_CONTINIUE) {
    return array("result" => $result);
  } else {
    return array("result" => $result, 
      "left-border" => $z1, "right-border" => $z2, 
      "is-open-left" => $isOpenZ1, "is-open-right" => $isOpenZ2);
  }
}

// ######################################################################################################################

function traverseUnion($border_left, $border_right, $isOpenLeft, $isOpenRight) {
  $y1 = array();
  
  for($i=0; $i<count($border_left); $i++) {
    $inskip = false;
    $fallthrough = false;
    
    $z1 = array();
    $z2 = array();
    $isOpenZ1 = array();
    $isOpenZ2 = array();
  
    if (count($y1) ==0) {
    
      $y1 = array($border_left[$i]);
      $y2 = array($border_right[$i]);
      $isOpenY1 = array($isOpenLeft[$i]);
      $isOpenY2 = array($isOpenRight[$i]);

    } else {
    
      $item_left =$border_left[$i];
      $item_right = $border_right[$i];
      $isOpenItemLeft = $isOpenLeft[$i];
      $isOpenItemRight = $isOpenRight[$i];

      for($j=0; $j<count($y1); $j++) {
        $inskip = false;
      
	if (!$fallthrough) {
      
	  $result = calculateUnion($y1[$j], $y2[$j], $isOpenY1[$j], $isOpenY2[$j], 
			           $item_left, $item_right, $isOpenItemLeft, $isOpenItemRight);
	
	  switch ($result["result"]) {
	    case ERROR:
	      echo "-> error";
	      break;
	    case INSERT:  
	      $fallthrough = true;
	      
	      $z1[] = $item_left;
      	      $z2[] = $item_right;
	      $isOpenZ1[] = $isOpenItemLeft;
      	      $isOpenZ2[] = $isOpenItemRight;
      	      
   	      $z1[] = $y1[$j];
      	      $z2[] = $y2[$j];
	      $isOpenZ1[] = $isOpenY1[$j];
      	      $isOpenZ2[] = $isOpenY2[$j];   	      
	      
	      break;
	    case SKIP:
	      $inskip = true;
	      
	      $z1[] = $y1[$j];
      	      $z2[] = $y2[$j];
	      $isOpenZ1[] = $isOpenY1[$j];
      	      $isOpenZ2[] = $isOpenY2[$j];

	      break;
	    case MERGE;
	      $fallthrough = true;
	      
	      $z1[] = $result["left-border"];
      	      $z2[] = $result["right-border"];
	      $isOpenZ1[] = $result["is-open-left"];
      	      $isOpenZ2[] = $result["is-open-right"];
	      
	      break;
	    case EXPAND_RIGHT:
	      
	      $z1[] = $result["left-border"];
      	      $z2[] = $result["right-border"];
	      $isOpenZ1[] = $result["is-open-left"];
      	      $isOpenZ2[] = $result["is-open-right"];      
	      
	      $item_left = $result["left-border"];
      	      $item_right = $result["right-border"];
	      $isOpenItemLeft = $result["is-open-left"];
      	      $isOpenItemRight = $result["is-open-right"];
	      
	      break;
	    default:
	      echo "-> error";
	  }
	} else {
	
	  $z1[] = $y1[$j];
      	  $z2[] = $y2[$j];
	  $isOpenZ1[] = $isOpenY1[$j];
      	  $isOpenZ2[] = $isOpenY2[$j];
	  
	} // if ! fallthrough
      } // for j
      
      if ($inskip) {
      	$z1[] = $item_left;
	$z2[] = $item_right;
	$isOpenZ1[] = $isOpenItemLeft;
	$isOpenZ2[] = $isOpenItemRight;
      } 
      
      $y1 = $z1;
      $y2 = $z2;
      $isOpenY1 = $isOpenZ1;
      $isOpenY2 = $isOpenZ2;
      
      // echo var_dump($y1);
      // echo var_dump($y2);
      
    } // if count == 0    
  } // for i
  
  return array( "left-border" => $y1,
	   "right-border" => $y2,
	   "is-open-left" => $isOpenY1,
	   "is-open-right" => $isOpenY2 );
	   
}

function traverseIntersection($border_left, $border_right, $isOpenLeft, $isOpenRight) { 

  $z1 = array();
  $z2 = array();
  $isOpenZ1 = array();
  $isOpenZ2 = array();
    
  for ($i=0; $i<count($border_left); $i++) {
    for ($j=$i+1; $j<count($border_left); $j++) {

      $result = calculateIntersection($border_left[$i], $border_right[$i], $isOpenLeft[$i], $isOpenRight[$i],
				      $border_left[$j], $border_right[$j], $isOpenLeft[$j], $isOpenRight[$j]);
      
      switch ($result["result"]) {
	case DOINTERSECT_STOP:
	case DOINTERSECT_CONTINIUE:
	
	  $z1[] = $result["left-border"];
      	  $z2[] = $result["right-border"];
	  $isOpenZ1[] = $result["is-open-left"];
      	  $isOpenZ2[] = $result["is-open-right"];
      	  
      	  break;
	case DONOTINTERSECT_CONTINIUE:
	case DONOTINTERSECT_STOP:
	default:
	  // do noting
      }
    } // for j
  } // for i
  
  return array( "left-border" => $z1,
	   "right-border" => $z2,
	   "is-open-left" => $isOpenZ1,
	   "is-open-right" => $isOpenZ2 );
}

function calculateMostCommonIntersection($border_left, $border_right, $isOpenLeft, $isOpenRight) { 
  global $emptySet;

  // case empy input
  if (count($border_left) == 0) {
    return $emptySet;
  }
  
  // start condition
  $z1 = $border_left[0];
  $z2 = $border_right[0];
  $isOpenZ1 = $isOpenLeft[0];
  $isOpenZ2 = $isOpenRight[0];
    
  for ($i=1; $i<count($border_left); $i++) {

    $result = calculateIntersection($border_left[$i], $border_right[$i], $isOpenLeft[$i], $isOpenRight[$i],
			            $z1, $z2, $isOpenZ1, $isOpenZ2);
      
      switch ($result["result"]) {
	case DOINTERSECT_STOP:
	case DOINTERSECT_CONTINIUE:
	
	  $z1 = $result["left-border"];
      	  $z2 = $result["right-border"];
	  $isOpenZ1 = $result["is-open-left"];
      	  $isOpenZ2 = $result["is-open-right"];
      	  
      	  break;
	case DONOTINTERSECT_CONTINIUE:
	case DONOTINTERSECT_STOP:
	  return $emptySet;
	  
	  break;
	default:
	  // do noting
      }
  } // for i
  
  return array( "left-border" => $z1,
	   "right-border" => $z2,
	   "is-open-left" => $isOpenZ1,
	   "is-open-right" => $isOpenZ2 );
}

function calculateIntersectionSet($values) {
    global $emptySet;

     // case empy input
     if (count($values) == 0) {
        return $emptySet;
     }

     // start condition
     $item = $values[0];
     // $z1 = $value["left-border"];
     // $z2 = $value["right-border"];
     // $isOpenZ1 = $value["is-open-left"];
     // $isOpenZ2 = $value["is-open-right"];

    for ($i = 1; $i < count($values); $i++) {
        $value = $values[$i];

        $z1 = array();
        $z2 = array();
        $isOpenZ1 = array();
        $isOpenZ2 = array();

        for ($j=0; $j<count($value["left-border"]); $j++) {
            for ($k=0; $k<count($item["left-border"]); $k++) {

//            foreach($item as $y) {

                $result = calculateIntersection($value["left-border"][$j], $value["right-border"][$j],
                                                $value["is-open-left"][$j], $value["is-open-right"][$j],
                                                $item["left-border"][$k], $item["right-border"][$k],
                                                $item["is-open-left"][$k], $item["is-open-right"][$k]);

                switch ($result["result"]) {
            	case DOINTERSECT_STOP:
            	case DOINTERSECT_CONTINIUE:

            	    $z1[] = $result["left-border"];
                  	$z2[] = $result["right-border"];
            	    $isOpenZ1[] = $result["is-open-left"];
                  	$isOpenZ2[] = $result["is-open-right"];

                  	break;
            	case DONOTINTERSECT_CONTINIUE:
            	case DONOTINTERSECT_STOP:
            	default:
            	  // do noting
                }
            }
        } // foreach value

        $item = traverseUnion($z1, $z2, $isOpenZ1, $isOpenZ2 );

    } // for values

    return $item;
}

?>

