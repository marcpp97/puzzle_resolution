<?php

// LOAD THE FILE REQUESTED AND PREPARES THE DATA
function loadFile($argc, $fileName, &$listaPiezas, &$width, &$height) {
    $listaPiezas = array();
    if (isset($argc)) {
        if($argc == 2) {
            $contador = 0;
            $archivo = fopen($fileName . ".txt", "r") or die("No se ha podido abrir el archivo");
            while(!feof($archivo)) {
                $linea = fgets($archivo);
                if($contador == 0) {
                    $aux = explode(" ", $linea);
                    $width = (int) $aux[0];
                    $height = (int) $aux[1];
                    $contador++;
                }
                else {
                    if(trim($linea) != '') {
                        $linea = str_replace(PHP_EOL,"",$linea);
                        $aux = explode(" ", $linea);
                        for($i = 0; $i < count($aux); $i++) {
                            $listaPiezas[$contador][$i] = $aux[$i];
                        }
                        $contador++;
                    }
                }
            }
            fclose($archivo);
    
        }
        else {
            echo "No se han pasado los argumentos necesarios\n";
        }
    
    }
    else {
        echo "argc and argv están deshabilitados\n";
    }
}

// ROTATES THE GIVEN PIECE BY 90 DEGREES AND RETURNS IT
function rotate ($var) {

    $aux = $var;

    for($i = 0; $i < count($aux); $i++) {
        if($i + 1 >= count(($aux))) {
            $aux[$i] = $var[0];
        }
        else {
            $aux[$i] = $var[$i + 1];
        }
    }
    
    return $aux;

}

// CHECK THE PIECE GIVEN AND RETURNS 2 IF ITS A CORNER, 1 IF ITS A BORDER AND 0 IF ITS EITHER OF BOTHS
function checkTypePiece($piece) {
    $contador = 0;
    foreach($piece as $v) {
        if($v == 0){
            $contador++;
        }
    }
    
    return $contador;
}

// CHECK IF THE PIECES IS IN THE CORRECT ROTATION GIVEN A TWO DIMENSIONAL POINT (X, Y) AND RETURNS TRUE IF IT IS, IF NOT, RETURNS FALSE
function checkIfPieceInCorrectRotation($x, $y, $width, $height, $numCorners, $piece) {
    
    if($numCorners == 3) {
        if($height == 1) {
            if($x == 0 || $x == $width - 1) {
                return true;
            }
        }
        if($width == 1) {
            if($y == 0 || $y == $height - 1) {
                return true;
            }
        }
    }
    else if($numCorners == 2) {
        if($x == 0 && $y == 0){
            if($piece[0] == 0 && $piece[1] == 0){
                return true;
            }
        }
        if($x == $width - 1 && $y == 0){
            if($piece[1] == 0 && $piece[2] == 0){
                return true;
            }
        }
        if($x == 0 && $y == $height - 1){
            if($piece[3] == 0 && $piece[0] == 0){
                return true;
            }
        }
        if($x == $width - 1 && $y == $height - 1){
            if($piece[2] == 0 && $piece[3] == 0){
                return true;
            }
        }
        if($height == 1) {
            if($piece[1] == 0 && $piece[3] == 0) {
                return true;
            }
        }
        if($width == 1) {
            if($piece[0] == 0 && $piece[2] == 0) {
                return true;
            }
        }
    }
    else if($numCorners == 1) {
        if($y != 0 && $y != $height - 1 && $x == 0) {
            if($piece[0] == 0){
                return true;
            }
        }
        if($x != 0 && $x != $width - 1 && $y == 0) {
            if($piece[1] == 0){
                return true;
            }
        }
        if($y != 0 && $y != $height - 1 && $x == $width - 1) {
            if($piece[2] == 0){
                return true;
            }
        }
        if($x != 0 && $x != $width - 1 && $y == $height - 1) {
            if($piece[3] == 0){
                return true;
            }
        }
    }
    else {
        return true;
    }
    return false;
}

// CHECK WHAT TYPE OF PIECE IS NEEDED IN THE COORDINATES GIVEN (TYPES OF PIECE EXPLAINED IN FUNCTION ROTATE)
function checkWhatPieceNeeded($x, $y, $width, $height) {
    if(($height == 1 && ($x == 0 || $x == $width - 1)) || ($width == 1 && ($y == 0 || $y == $height - 1))){
        echo "entra";
        return 3;
    }
    if((($x == $width - 1 || $x == 0) && ($y == 0 || $y == $height - 1))){
        return 2;
    }
    if(($x != 0 && $x != $width - 1 && ($y == 0 || $y == $height - 1)) || ($y != 0 && $y != $height - 1 && ($x == 0 || $x == $width - 1))) {
        return 1;
    }
    return 0;
}

// CHECK IF THE PIECE IS CORRECTLY ROTATED RESPECT THE OTHER PIECES SURROINDING IT
function checkIfCorrectRotationRespectOthers($x, $y, $lista, $numI, $numA, $p) {
    
    if($y != 0) {
        if($lista[$numA][3] != $p[1]) {
            return false;
        }
    }
    if($x != 0) {
        if($lista[$numI][2] != $p[0]) {
            return false;
        }
    }

    return true;
}

// THIS METHOD PREPARES THE DATA NEEDED TO START THE MAIN ALGORITHM
function startProcess($argc, $fileName) {
    // ARRAY OF THE PIECES THAT ARE CONTAIN IN THE .TXT
    $listaPiezas = array();

    // WIDTH OF THE PUZZLE 
    $width = 0;

    // HEIGHT OF THE PUZZLE 
    $height = 0; 

    // LOAD VARIABLE LISTAPIEZAS AND TAMANYO
    loadFile($argc, $fileName, $listaPiezas, $width, $height);

    // ARRAY OF BOOLEANS TO CHECK IF THE PIECE HAS BEEN USED
    $listaProbados = array_fill(1, count($listaPiezas), false);

    // ARRAY THAT WILL CONTAIN THE REARRANGE PIECES THAT WILL BE USED TO SOLVE THE PUZZLE IN ORDER OF THE PUZZLE
    $listaAuxiliar = array();

    // ARRAY THAT WILL CONTAIN THE INDEXES OF THE PIECES THAT WILL BE USED TO SOLVE THE PUZZLE IN ORDER OF THE PUZZLE
    $listaIndices = array();

    $resultado = "";

    // THIS METHOD INITIALIZE WITH THE CORNER BORDER OF THE PUZZLE WITH THE COORDINATES (0,0) X = 0, Y = 0
    // THE PIECE SELECTED IS THE FIRST PIECE FOUND THAT MEETS THE SPECIFICATIONS
    // IT STARTS WITH A PIECE THAT WILL BE STATIC TO AVOID THE APPEAREANCE OF EQUAL SOLUTIONS BUT IN DIFFERENT ROTATION
    // IF THE PUZZLE WIDTH AND HEIGHT ARE NOT EQUAL, ITS A RECTANGULAR PUZZLE. THIS MEANS THAT THE CORNER PIECE STABLISHED TO BE STATIC COULD BE WRONG PLACED
    // TO AVOID THIS PROBLEM, IF THE WIDTH AND HEIGHT ARE NOT EQUAL, THE LOOP THAT CHECKS FOR CORNERS CONTINUES
    for($i = 1; $i <= count($listaPiezas); $i++) {
        $p = $listaPiezas[$i];
        $n = checkTypePiece($p);
        if($n == 2 || $n == 3) {
            while(!checkIfPieceInCorrectRotation(0, 0, $width, $height, $n, $p)) {
                $p = rotate($p);
            }
            array_push($listaAuxiliar, $p);
            array_push($listaIndices, $i);
            $listaProbados[$i] = true;
            $xActual = 1;
            $yActual = 0;
            if($width == 1) {
                $xActual = 0;
                $yActual = 1;
            }
            $resultado = generatePuzzle($listaAuxiliar, $listaProbados, $listaIndices, $listaPiezas, $xActual, $yActual, $width, $height, "");
            if($width == $height) {
                break;
            }
            else {
                if($resultado != "") {
                    break;
                }
                array_pop($listaAuxiliar);
                array_pop($listaIndices);
                $listaProbados[$i] = false;
            }
        }
    }

    // STARTS THE GENERATION OF THE POSSIBLE PUZZLES
    return $resultado;
}

// THIS IS THE MAIN METHOD, SEARCHES RECURSIVELY ALL THE POSSIBLE CONVINATIONS AND RETURNS A STRING WITH ALL THE POSSIBLE ANSWERS
function generatePuzzle($lista, $probados, $indices, $listaPiezas, $x, $y, $width, $height, $resultado) {
    
    if(count($lista) == $width * $height) {
        if(count(array_unique($probados)) === 1) {
            $cont = 0;
            foreach($indices as $i) {
                if($cont % $width == 0) {
                    $resultado .= "\n";
                } 
                $resultado .= $i . " ";
                $cont++;
            }
            $resultado = $resultado . "\n";
            return $resultado;
        }
        else {
            return $resultado;
        }
    }
    else {
        
        $numCornersNeeded = checkWhatPieceNeeded($x, $y, $width, $height);

        for($j = 1; $j <= count($listaPiezas); $j++) {
            $p = $listaPiezas[$j];
            if(checkTypePiece($p) == $numCornersNeeded && !$probados[$j]) {
                
                if($numCornersNeeded == 0) {
                    for($i = 0; $i < 4; $i++) {
                        if(checkIfCorrectRotationRespectOthers($x, $y, $lista, count($lista) - 1, count($lista) - $width, $p)) {
                            array_push($lista, $p);
                            array_push($indices, $j);
                            $probados[$j] = true;
                            $auxx = $x + 1;
                            $auxy = $y;
                            if($auxx == $width) {
                                $auxx = 0;
                                $auxy++;
                            }
                            $resultado = generatePuzzle($lista, $probados, $indices, $listaPiezas, $auxx, $auxy, $width, $height, $resultado);
                            array_pop($lista);
                            array_pop($indices);
                            $probados[$j] = false;
                        }
                        $p = rotate($p);
                    }
                }
                else {
                    while(!checkIfPieceInCorrectRotation($x, $y, $width, $height, $numCornersNeeded, $p)) {
                        $p = rotate($p);
                    }
                    // echo " DESPUES: " . implode($p) . "\n";
                    if(checkIfCorrectRotationRespectOthers($x, $y, $lista, count($lista) - 1, count($lista) - $width, $p)){
                        // echo "ENTRA\n";
                        array_push($lista, $p);
                        array_push($indices, $j);
                        $probados[$j] = true;
                        $auxx = $x + 1;
                        $auxy = $y;
                        if($auxx == $width) {
                            $auxx = 0;
                            $auxy++;
                        }
                        $resultado = generatePuzzle($lista, $probados, $indices, $listaPiezas, $auxx, $auxy, $width, $height, $resultado);
                        // echo "SALE\n";
                        array_pop($lista);
                        array_pop($indices);
                        $probados[$j] = false;
                    }

                }
            }
        }

    }
    
    return $resultado;

}

// COMPARES THE TWO OUTPUTS (ALGORITHM OUTPUT AND SOLUTION OUTPUT)
function compareTwoOutputs($fileName, $resultado) {
    echo "\nRESULTADO EXTRAIDO DEL ALGORITMO PROGRAMADO: \n" . $resultado . "\n";

    $archivo = fopen($fileName . "_solution.txt", "r") or die("No se ha podido abrir el archivo");
    $puzzleResuelto = "\n";
    while(!feof($archivo)) {
        $puzzleResuelto .= fgets($archivo);
    }
    fclose($archivo);
    echo "RESULTADO EXTRAIDO DE LA SOLUCION: \n" . $puzzleResuelto . "\n\n";

    $resultadoFinal = true;
    $resultadoEx = explode(PHP_EOL, $resultado);
    $puzzleResueltoEx = explode(PHP_EOL, $puzzleResuelto);
    for($i = 0; $i < count($resultadoEx); $i++) {
        if(trim($resultadoEx[$i]) != '' && trim($puzzleResueltoEx[$i]) != '') {
            if($resultadoEx[$i] != ($puzzleResueltoEx[$i] . " ")) {
                $resultadoFinal = false;
                break;
            }
        }
    }

    if($resultado == "")
        $resultadoFinal = false;

    echo "COMPARACION DE LOS DOS RESULTADOS: ";
    echo $resultadoFinal ? "EL ALGORITMO ES CORRECTO\n\n" : "EL ALGORITMO NO ES CORRECTO\n\n";
}

?>