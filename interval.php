<?php

include "interval_helper.php";

define("EMPTY_SET", "DNE");

function parseFloat($input) {

  if (preg_match("/-\s*oo/i",$input)) {
    return array(-INF, "-oo");
  } elseif (preg_match("/\+?oo/i", $input)) {
    return array(INF, "oo");
  }
    
  // test: rational
  if (preg_match("/\//", $input)) {
    list($a, $b) = preg_split("/\//", $input);
  
    $a1 = (int) preg_replace("/[\(\[]/"," ",$a);
    $b1 = (int) preg_replace("/[\(\[]/"," ",$b);
    $v = $a1/$b1;
    return array($v, "$a1/$b1");
  } else {
    $v = (float) preg_replace("/[\(\[]/"," ", $input);
    return array($v, "$v");
  }
}

// ######################################################################

function parseString($input) {
  $parts = preg_split("/\s*U\s*/i",$input);
  
  return parseParts($parts);
}

function parseParts($parts) {
  global $emptySet;
  
  $hasError = false;

  $borderLeft = array();
  $borderRight = array();
  $isOpenLeft = array();
  $isOpenRight = array();
  
  $index = array();
  
  // empty input
  $hasError = (count($parts) == 0);
  
  foreach($parts as $part) {
    // echo "-> " . $part . "\n";
    
    if (preg_match("/dne/i", $part)) {
    
      $borderLeft[] = $emptySet["left-border"];
      $borderRight[] = $emptySet["right-border"]; 
      $isOpenLeft[] = $emptySet["is-open-left"];
      $isOpenRight[] = $emptySet["is-open-right"];
  
    } else {
    
      list($a,$b) = preg_split("/\s*,\s*/",$part);
    
      // missing colon
      if (!isset($a) || !isset($b)) {
	$hasError = true;
	break;
      }
    
      list($bl, $v1) = parseFloat($a);
      list($br, $v2) = parseFloat($b);
    
      $index["$bl"] = $v1;
      $index["$br"] = $v2;
    
      $iol = preg_match("/\(/", $a) > 0;
      $ior = preg_match("/\)/", $b) > 0;
    
      // swap borders if necessary
      if ($bl < $br) {
	$borderLeft[] = $bl;
	$borderRight[] = $br;
      } else {
	$borderLeft[] = $br;
	$borderRight[] = $bl;    
      }
    
      $isOpenLeft[] = $iol;
      $isOpenRight[] = $ior;
    }
  }
  
  if ($hasError) {
    return array("has-error" => true);
  } else {
    return array("has-error" => false,
	    "left-border" => $borderLeft,
	    "right-border" => $borderRight,
	    "is-open-left" => $isOpenLeft,
	    "is-open-right" => $isOpenRight,
	    "index" => $index);
  }
}

function toString($borderLeft, $borderRight, $isOpenLeft, $isOpenRight, $index) {
  if (count($borderLeft) == 0) {
    return EMPTY_SET;
  }

  $results = array();
  
  for ($i=0; $i<count($borderLeft); $i++) {
    $v = toStringPart($borderLeft[$i], $borderRight[$i], $isOpenLeft[$i], $isOpenRight[$i], $index);
    
    if ($v != EMPTY_SET) $results[] = $v;
  }
  
  return count($results) == 0 ? EMPTY_SET : join(" U ", $results);
}

function toStringPart($borderLeft, $borderRight, $isOpenLeft, $isOpenRight, $index) {
  global $emptySet;
  
  if ($borderLeft == $emptySet["left-border"] && $borderRight == $emptySet["right-border"] && 
      $isOpenLeft == $emptySet["is-open-left"] && $isOpenRight == $emptySet["is-open-right"]) {
     return EMPTY_SET;
  }

  $a = $isOpenLeft ? "(" : "[";
  $b = $index["$borderLeft"];
  $c = $index["$borderRight"];
  $d = $isOpenRight ? ")" : "]";
  
  return $a . $b . "," . $c . $d;
}




// #############################################################################################

function canonicInterval($input) {
  $values = parseString($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $result = traverseUnion($values["left-border"], $values["right-border"], $values["is-open-left"], $values["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"],
		    $values["index"]);
  }
}

function intersectionList($input) {
  $values = parseString($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $result = traverseIntersection($values["left-border"], $values["right-border"], $values["is-open-left"], $values["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"],
		    $values["index"]);
  }
}

function intersection($input) {
  $values = parseParts($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $v1 = traverseIntersection($values["left-border"], $values["right-border"], $values["is-open-left"], $values["is-open-right"]);
    // var_dump($v1);
    $result = traverseUnion($v1["left-border"], $v1["right-border"], $v1["is-open-left"], $v1["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"],
		    $values["index"]);
  }
}

function mostCommonIntersection($input) {
  $values = parseParts($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $result = calculateMostCommonIntersection($values["left-border"], $values["right-border"], $values["is-open-left"], $values["is-open-right"]);

    return toStringPart($result["left-border"],
			$result["right-border"],
			$result["is-open-left"],
			$result["is-open-right"],
			$values["index"]);
  }
}

?>