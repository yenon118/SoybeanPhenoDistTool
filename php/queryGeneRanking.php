<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = trim($_GET['Dataset']);
$phenotypes = $_GET['Phenotypes'];


$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);


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
$gene_ranking_table = "pDist_" . $dataset . "_summary_data";
$gff_table = "pDist_Soybean_Wm82a2v1_GFF";


$result_arr = array();

if (isset($phenotype_array)) {
    if (!empty($phenotype_array)) {
        if (is_array($phenotype_array)) {
            if (count($phenotype_array) > 0) {

                for ($i = 0; $i < count($phenotype_array); $i++) {

                    $query_str = "SELECT Gene, ";
                    $query_str = $query_str . "Phenotype, ";
                    $query_str = $query_str . "Phenotype_Data_Type, ";
                    $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Test_Method SEPARATOR '; ') AS Test_Method, ";
                    $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Minimum_Test_P_Value SEPARATOR '; ') AS Minimum_Test_P_Value, ";
                    $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Minimum_Negative_Log2_Test_P_Value SEPARATOR '; ') AS Minimum_Negative_Log2_Test_P_Value, ";
                    $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Maximum_Test_P_Value SEPARATOR '; ') AS Maximum_Test_P_Value, ";
                    $query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Maximum_Negative_Log2_Test_P_Value SEPARATOR '; ') AS Maximum_Negative_Log2_Test_P_Value ";
                    $query_str = $query_str . "FROM ( ";
                    $query_str = $query_str . "    SELECT ";
                    $query_str = $query_str . "    Gene, ";
                    $query_str = $query_str . "    Phenotype, ";
                    $query_str = $query_str . "    Phenotype_Data_Type, ";
                    $query_str = $query_str . "    Test_Method, ";
                    $query_str = $query_str . "    Minimum_Test_P_Value, ";
                    $query_str = $query_str . "    Minimum_Negative_Log2_Test_P_Value, ";
                    $query_str = $query_str . "    Maximum_Test_P_Value, ";
                    $query_str = $query_str . "    Maximum_Negative_Log2_Test_P_Value ";
                    $query_str = $query_str . "    FROM " . $db . "." . $gene_ranking_table . " AS PHENO ";
                    $query_str = $query_str . "    WHERE (PHENO.Phenotype IN ('" . $phenotype_array[$i] . "')) ";
                    $query_str = $query_str . "    ORDER BY Minimum_Test_P_Value ";
                    $query_str = $query_str . ") AS PHENO2 ";
                    $query_str = $query_str . "GROUP BY PHENO2.Gene, PHENO2.Phenotype, PHENO2.Phenotype_Data_Type ";
                    $query_str = $query_str . "ORDER BY Minimum_Test_P_Value, Maximum_Test_P_Value ";
                    $query_str = $query_str . "; ";

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
