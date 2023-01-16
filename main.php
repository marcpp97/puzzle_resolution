<?php
// IMPORT OF THE REQUIRE SCRIPT OF FUNCTIONS
require "functions.php";

// START THE PROCESS THAT WILL START THE ALGORITHM
$resultado = startProcess($argc, $argv[1]);

// ECHO THE RESULT
// echo $resultado . "\n";

// COMPARE THE TWO OUTPUTS (THE SOLUTION GIVEN BY THE ALGORITHM AND THE SOLUTION PREDEFINED) AND CHECKS IF ARE THE SAME RESULT
compareTwoOutputs($argv[1], $resultado);

?>