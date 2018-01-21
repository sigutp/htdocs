<?php
echo "FUCK";
header("Location: index.php?page=0");
$index = $_GET["index"];
$operationCode = $_GET["operationCode"];

if($operationCode == 0){
    echo "edit";
} else if ($operationCode == 1){
    echo "delete";
}

?>