<?php

include "interval_helper.php";

function parseString($input) {

  $borderLeft = [];
  $borderRight = [];
  $isOpenLeft = [];
  $isOpenRight = [];

  $parts = preg_split("/\s*U\s*/",$input);
  
  foreach($parts as $part) {
    // echo "-> " . $part . "\n";
    list($a,$b) = preg_split("/\s*,\s*/",$part);
    $bl = (float) preg_replace("/[\(\[]/"," ", $a);
    $br = (float) $b;
    
    $iol = preg_match("/\(/", $a) > 0;
    $ior = preg_match("/\)/", $b) > 0;
    
    $borderLeft[] = $bl;
    $borderRight[] = $br;
    $isOpenLeft[] = $iol;
    $isOpenRight[] = $ior;
  }
  
  return ["border-left" => $borderLeft,
	  "border-right" => $borderRight,
	  "is-open-left" => $isOpenLeft,
	  "is-open-right" => $isOpenRight];
}

function toString($borderLeft, $borderRight, $isOpenLeft, $isOpenRight) {
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
  
  $result = traverseUnion($values["border-left"], $values["border-right"], $values["is-open-left"], $values["is-open-right"]);

  return toString($result["left-border"],
		  $result["right-border"],
		  $result["is-open-left"],
		  $result["is-open-right"]);
}



?>