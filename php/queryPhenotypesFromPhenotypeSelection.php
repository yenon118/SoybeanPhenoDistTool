<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = trim($_GET['Dataset']);


$db = "soykb";
$phenotype_selection_table = "pDist_" . $dataset . "_Phenotype_Selection";


// Generate SQL string
$query_str = "SELECT * ";
$query_str = $query_str . "FROM " . $db . "." . $phenotype_selection_table . ";";


// Execute SQL string
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);


echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>