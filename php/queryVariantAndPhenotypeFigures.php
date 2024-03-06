<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = $_GET['Dataset'];
$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];
$phenotype = $_GET['Phenotype'];


$db = "soykb";
$genotype_table = "act_" . $dataset . "_genotype_" . $chromosome;
$functional_effect_table = "act_" . $dataset . "_func_eff_" . $chromosome;
$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
$phenotype_table = "act_" . $dataset . "_Phenotype_Data";


// Construct query string
$query_str = "SELECT GENO.Chromosome, GENO.Position, GENO.Accession, ";
$query_str = $query_str . "AM.SoyKB_Accession, AM.GRIN_Accession, AM.Improvement_Status, AM.Classification, ";
$query_str = $query_str . "GENO.Genotype, ";
$query_str = $query_str . "COALESCE( FUNC.Functional_Effect, GENO.Category ) AS Functional_Effect, ";
$query_str = $query_str . "GENO.Imputation, ";
$query_str = $query_str . "PH." . $phenotype . " ";

$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT G.Chromosome, G.Position, G.Accession, G.Genotype, G.Category, G.Imputation ";
$query_str = $query_str . "    FROM " . $db . "." . $genotype_table . " AS G ";
$query_str = $query_str . "    WHERE (G.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (G.Position = " . $position . ") ";

$query_str = $query_str . ") AS GENO ";
$query_str = $query_str . "LEFT JOIN ( ";
$query_str = $query_str . "    SELECT F.Chromosome, F.Position, F.Allele, F.Gene, F.Functional_Effect ";
$query_str = $query_str . "    FROM " . $db . "." . $functional_effect_table . " AS F ";
$query_str = $query_str . "    WHERE (F.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (F.Position = " . $position . ") ";

$query_str = $query_str . "    AND (F.Gene LIKE '%" . $gene . "%') ";
$query_str = $query_str . ") AS FUNC ";
$query_str = $query_str . "ON GENO.Chromosome = FUNC.Chromosome AND GENO.Position = FUNC.Position AND GENO.Genotype = FUNC.Allele ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
$query_str = $query_str . "ON CAST(GENO.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";

$query_str = $query_str . "LEFT JOIN " . $db . "." . $phenotype_table . " AS PH ";
$query_str = $query_str . "ON CAST(AM.GRIN_Accession AS BINARY) = CAST(PH.ACNO AS BINARY) ";

$query_str = $query_str . "ORDER BY GENO.Chromosome, GENO.Position, GENO.Genotype; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);
?>