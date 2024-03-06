<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';
include 'getSummarizedDataByChromosomePositionsQueryString.php';
include 'getDataByChromosomePositionsQueryString.php';

$dataset = trim($_GET['Dataset']);
$gene = $_GET['Gene'];
$chromosome = $_GET['Chromosome'];

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];

// Generate query string
$query_str = getDataByChromosomePositionsQueryString(
	$dataset,
	$gene,
	$chromosome,
	$db,
	$gff_table,
	$accession_mapping_table,
	""
);

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

for ($i = 0; $i < count($result_arr); $i++) {
	if (preg_match("/\+/i", $result_arr[$i]["Imputation"])) {
		$result_arr[$i]["Imputation"] = "+";
	} else{
		$result_arr[$i]["Imputation"] = "";
	}
}

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>