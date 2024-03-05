<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = trim($_GET['Dataset']);


$db = "soykb";
$data_panel_selection_table = "pDist_Soybean_Data_Panel_Selection";


// Generate SQL string
$query_str = "SELECT DISTINCT Chromosome ";
$query_str = $query_str . "FROM " . $db . "." . $data_panel_selection_table . " ";
$query_str = $query_str . "WHERE Dataset='" . $dataset . "';";


// Execute SQL string
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);


echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>