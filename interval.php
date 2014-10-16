<?php

include "interval_helper.php";

function parseFloat($input) {
    if (preg_match("/-\s*oo/i",$input)) {
      return -INF;
    } elseif (preg_match("/\+?oo/i", $input)) {
      return INF;
    }
    
    return (float) preg_replace("/[\(\[]/"," ", $input);
}

// ######################################################################

function parseString($input) {
  $hasError = false;

  $borderLeft = [];
  $borderRight = [];
  $isOpenLeft = [];
  $isOpenRight = [];

  $parts = preg_split("/\s*U\s*/i",$input);
  
  // empty input
  $hasError = (count($parts) == 0);
  
  foreach($parts as $part) {
    // echo "-> " . $part . "\n";
    
    if (preg_match("/one/i", $part)) {
      continue;
    }
    
    list($a,$b) = preg_split("/\s*,\s*/",$part);
    
    // missing colon
    if (!isset($a) || !isset($b)) {
	$hasError = true;
	break;
    }
    
    $bl = parseFloat($a);
    $br = parseFloat($b);
    
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
  
  if ($hasError) {
    return ["has-error" => true];
  } else {
    return ["has-error" => false,
	    "border-left" => $borderLeft,
	    "border-right" => $borderRight,
	    "is-open-left" => $isOpenLeft,
	    "is-open-right" => $isOpenRight];
  }
}

function toString($borderLeft, $borderRight, $isOpenLeft, $isOpenRight) {
  if (count($borderLeft) == 0) {
    return "ONE";
  }

  $results = [];
  
  for ($i=0; $i<count($borderLeft); $i++) {
    $a = $isOpenLeft[$i] ? "(" : "[";
    $b = $borderLeft[$i];
    $c = $borderRight[$i];
    $d = $isOpenRight[$i] ? ")" : "]";
    
    $results[] = $a . $b . "," . $c . $d;
  }
  
  return join(" U ", $results);
}

// #############################################################################################

function canonicInterval($input) {
  $values = parseString($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $result = traverseUnion($values["border-left"], $values["border-right"], $values["is-open-left"], $values["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"]);
  }
}

function intersectionList($input) {
  $values = parseString($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $result = traverseIntersection($values["border-left"], $values["border-right"], $values["is-open-left"], $values["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"]);
  }
}

function intersection($input) {
  $values = parseString($input);
  
  if ($values["has-error"]) {
    return "input error";
  } else {
  
    $v1 = traverseIntersection($values["border-left"], $values["border-right"], $values["is-open-left"], $values["is-open-right"]);
    // var_dump($v1);
    $result = traverseUnion($v1["left-border"], $v1["right-border"], $v1["is-open-left"], $v1["is-open-right"]);

    return toString($result["left-border"],
		    $result["right-border"],
		    $result["is-open-left"],
		    $result["is-open-right"]);
  }
}

?>