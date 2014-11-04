<?php

include "interval_helper.php";

// supress warnings
// $allowedmacros = array();
include "mathphp2.php";
include "macros.php";

define("EMPTY_SET", "DNE");

function parseFloat($input) {

  if (preg_match("/-\s*oo/i",$input)) {
    return array(-INF, "-oo");
  } elseif (preg_match("/\+?oo/i", $input)) {
    return array(INF, "oo");
  }

  $result = eval("return (".mathphp($input, null).");");
  $error = ($result === false) || is_string($result);

  return array($result, $input, $error);

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

      $iol = preg_match("/^\s*\[/", $part) > 0;
      $ior = preg_match("/\]\s*$/", $part) > 0;

      // list($a,$b) = preg_split("/\s*,\s*/",$part);
      preg_match("/^\s*[\(\[]\s*(?P<left>.+)\s*,\s*(?P<right>.+)\s*[\)\]]\s*$/", $part, $match);

      // missing colon
      if (!isset($match["left"]) || !isset($match["right"]))
        return array("has-error" => true);

      list($bl, $v1, $e1) = parseFloat($match["left"]);
      list($br, $v2, $e2) = parseFloat($match["right"]);
    
      $index["$bl"] = $v1;
      $index["$br"] = $v2;

      if ($e1 || $e2)
        return array("has-error" => true);
    
      // $iol = preg_match("/^\s*\[/", $a) > 0;
      // $ior = preg_match("/\]\s*$/", $b) > 0;
    
      // swap borders if necessary
      if ($bl < $br) {
	$borderLeft[] = $bl;
	$borderRight[] = $br;
      } else {
	$borderLeft[] = $br;
	$borderRight[] = $bl;    
      }
    
      $isOpenLeft[] = ! $iol;
      $isOpenRight[] = ! $ior;
    }
  }

    return array("has-error" => false,
	    "left-border" => $borderLeft,
	    "right-border" => $borderRight,
	    "is-open-left" => $isOpenLeft,
	    "is-open-right" => $isOpenRight,
	    "index" => $index);

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

function cannonicalIntersection($input) {
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

function intersection($input) {
    $values = array();
    $index = array();

    foreach($input as $item) {
        $el = parseString($item);

        if ($el["has-error"]) {
            return "input error";
        } else {
            $values[] = $el;
            foreach($el["index"] as $k => $v) {
                $index[$k] = $v;
            }
        }
    }

    $result = calculateIntersectionSet($values);

    return toString($result["left-border"],
    		    $result["right-border"],
    		    $result["is-open-left"],
    		    $result["is-open-right"],
    		    $index);
}
?>