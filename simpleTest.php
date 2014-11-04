<?php

include "interval_ext.php";

echo "*** test8: canonicInterval\n";
echo canonicInterval( "(1,4] U [3,6)" ) . "\n";
echo "--> " . canonicInterval( "(1,2] U [3,4) u (5,6)" ) . "\n";
echo "--> " . canonicInterval( "(1,2] U [3,5) u (4,5) u (6,6+1)" ) . "\n";
echo "--> " . canonicInterval( "(1,2] U [3,6) u (4,5) u (2*3,7)" ) . "\n";
echo "--> " . canonicInterval( "(x+,2] U [3+,6) u ((4*3,5) u (a+b,7)" ) . "\n";
echo "--> " . canonicInterval( "(cheese(2),2] U [3,6) u (4,5) u (2*3,7)" ) . "\n";

echo "*** test14: intersection\n";
//echo intersection( ["(1,3] u (5,7)", "(2,4) u [6,7)"] ) . "\n";
echo intersection( array("(1,3] u (5,7)", "(2,4) u [6,7)") ) . "\n";

echo "*** test15: mathphp2\n";
echo mathphp("exp(20)", "") . "\n";
echo mathphp("1+1", "") . "\n";

echo "*** test16: macros\n";
echo (is_numeric("sin(20)") ? "numeric" : "nonnumeric") . "\n";
echo evalbasic("sin(20)") . "\n";
echo evalbasic("20 +7 *3") . "\n";

function calc($equation)
{
    // Remove whitespaces
    $equation = preg_replace('/\s+/', '', $equation);
    echo "$equation\n";

    $number = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|π)'; // What is a number

    $functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg
|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
    $operators = '[\/*\^\+-,]'; // Allowed math operators
    $regexp = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

    if (preg_match($regexp, $equation))
    {
        $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
        echo "$equation\n";
        eval('$result = '.$equation.';');
    }
    else
    {
        $result = false;
    }
      return $result;
}

echo "*** test17: calc\n";
echo calc("exp(20)") . "\n";
echo calc("20 +7 *3") . "\n";

echo "*** test18: mathphp\n";
echo eval("return (".mathphp("20 + 7 *3", null).");") . "\n";

echo "*** test 18: regexp\n";
preg_match("/^\s*[\(\[]\s*(?P<left>.+)\s*,\s*(?P<right>.+)\s*[\)\]]\s*$/", "[ 3+5, 5)", $match);
var_dump($match);
?>