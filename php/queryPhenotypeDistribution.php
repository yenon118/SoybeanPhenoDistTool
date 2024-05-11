<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = trim($_GET['Dataset']);
$gene = trim($_GET['Gene']);
$phenotypes = $_GET['Phenotypes'];


if (is_string($phenotypes)) {
    $phenotypes = trim($phenotypes);
    $temp_phenotype_array = preg_split("/[;, \n]+/", $phenotypes);
    $phenotype_array = array();
    for ($i = 0; $i < count($temp_phenotype_array); $i++) {
        if (!empty(trim($temp_phenotype_array[$i]))) {
            array_push($phenotype_array, trim($temp_phenotype_array[$i]));
        }
    }
} elseif (is_array($phenotypes)) {
    $temp_phenotype_array = $phenotypes;
    $phenotype_array = array();
    for ($i = 0; $i < count($temp_phenotype_array); $i++) {
        if (!empty(trim($temp_phenotype_array[$i]))) {
            array_push($phenotype_array, trim($temp_phenotype_array[$i]));
        }
    }
}


$db = "soykb";
$gff_table = "pDist_Soybean_Wm82a2v1_GFF";


// Generate SQL string
$query_str = "";
if ($query_str == "") {
    if (isset($gene)) {
        if (!empty($gene)) {
            $query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
            $query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
            $query_str = $query_str . "WHERE (Name = '" . $gene . "');";
        }
    }
}

if ($query_str == "") {
    exit();
}

// Execute SQL string
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);


// Use the chromosome to make phenotype_distribution_table
$phenotype_distribution_table = "";
if (isset($gene_result_arr)) {
    if (!empty($gene_result_arr)) {
        if (is_array($gene_result_arr)) {
            $phenotype_distribution_table = "pDist_" . $dataset . "_" . $gene_result_arr[0]["Chromosome"] . "";
        }
    }
}

if ($phenotype_distribution_table == "") {
    exit();
}


$result_arr = array();

if (isset($phenotype_array)) {
    if (!empty($phenotype_array)) {
        if (is_array($phenotype_array)) {
            if (count($phenotype_array) > 0) {

                for ($i = 0; $i < count($phenotype_array); $i++) {

                        // Generate SQL string
                        $query_str = "SELECT PHENO2.Chromosome, PHENO2.Position, PHENO2.Gene, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Allele ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Allele, ";
                        $query_str = $query_str . "PHENO2.Phenotype, ";
                        $query_str = $query_str . "PHENO2.Phenotype_Data_Type, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Test_Method ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Test_Method, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Phenotype_Category ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Phenotype_Category, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Accession_Count ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Accession_Count, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Normality_Statistic ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Normality_Statistic, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Normality_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Normality_P_Value, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Test_Statistic ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Test_Statistic, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Test_P_Value, ";
                        $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Negative_Log2_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Negative_Log2_Test_P_Value ";
                        $query_str = $query_str . "FROM ( ";
                        $query_str = $query_str . "	SELECT PHENO.Chromosome, ";
                        $query_str = $query_str . "	PHENO.Position, ";
                        $query_str = $query_str . "	PHENO.Gene, ";
                        $query_str = $query_str . "	CONCAT_WS(' vs ', PHENO.Allele_1, PHENO.Allele_2) AS Allele, ";
                        $query_str = $query_str . "	PHENO.Phenotype, ";
                        $query_str = $query_str . "	PHENO.Phenotype_Data_Type, ";
                        $query_str = $query_str . "	PHENO.Test_Method, ";
                        $query_str = $query_str . "	CONCAT_WS(' vs ', PHENO.Phenotype_Category_1, PHENO.Phenotype_Category_2) AS Phenotype_Category, ";
                        $query_str = $query_str . "	PHENO.Accession_Count, ";
                        $query_str = $query_str . "	PHENO.Normality_Statistic, ";
                        $query_str = $query_str . "	PHENO.Normality_P_Value, ";
                        $query_str = $query_str . "	PHENO.Test_Statistic, ";
                        $query_str = $query_str . "	PHENO.Test_P_Value, ";
                        $query_str = $query_str . "	PHENO.Negative_Log2_Test_P_Value ";

                        $query_str = $query_str . "	FROM " . $db . "." . $phenotype_distribution_table . " AS PHENO ";

                        $query_str = $query_str . "	WHERE (PHENO.Phenotype IN ('" . $phenotype_array[$i] . "')) ";
                        $query_str = $query_str . "	AND (PHENO.Gene = '" . $gene . "') ";

                        $query_str = $query_str . ") AS PHENO2 ";
                        $query_str = $query_str . "GROUP BY PHENO2.Chromosome, PHENO2.Position, PHENO2.Gene, PHENO2.Phenotype, PHENO2.Phenotype_Data_Type ";
                        $query_str = $query_str . "ORDER BY PHENO2.Phenotype, PHENO2.Chromosome, PHENO2.Position; ";

                        try {
                            // Execute SQL string
                            $stmt = $PDO->prepare($query_str);
                            $stmt->execute();
                            $result = $stmt->fetchAll();

                            // Populate results
                            $temp_result_arr = pdoResultFilter($result);

                            for ($j = 0; $j < count($temp_result_arr); $j++) {
                                array_push($result_arr, $temp_result_arr[$j]);
                            }

                        } catch (Exception $e) {
                        }

                }
            }
        }
    }
}


echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>