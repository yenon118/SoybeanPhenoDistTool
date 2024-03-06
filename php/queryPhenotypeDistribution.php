<?php

include '../../config.php';
include 'pdoResultFilter.php';


$dataset = trim($_GET['Dataset']);
$chromosome = trim($_GET['Chromosome']);
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
$phenotype_distribution_table = "pDist_" . $dataset . "_" . $chromosome . "";
$gff_table = "pDist_Soybean_Wm82a2v1_GFF";


$result_arr = array();

if (isset($phenotype_array)) {
	if (!empty($phenotype_array)) {
		if (is_array($phenotype_array)) {
			if (count($phenotype_array) > 0) {
				$phenotype_array_chunk = array_chunk($phenotype_array, 2);

				for ($i = 0; $i < count($phenotype_array_chunk); $i++) {

					if (count($phenotype_array_chunk[$i]) > 0) {

						// Generate SQL string
						$query_str = "SELECT PHENO2.Chromosome, PHENO2.Position, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Allele ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Allele, ";
						$query_str = $query_str . "PHENO2.Phenotype, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Phenotype_Data_Type ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Phenotype_Data_Type, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Test_Method ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Test_Method, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Phenotype_Category ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Phenotype_Category, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Shapiro_Test_Statistics ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Shapiro_Test_Statistics, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Shapiro_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Shapiro_Test_P_Value, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Mann_Whitney_U_Rank_Test_Statistics ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Mann_Whitney_U_Rank_Test_Statistics, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Mann_Whitney_U_Rank_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Mann_Whitney_U_Rank_Test_P_Value, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.T_Test_Statistics ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS T_Test_Statistics, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.T_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS T_Test_P_Value, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Chi_Square_Test_Statistics ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Chi_Square_Test_Statistics, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Chi_Square_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Chi_Square_Test_P_Value, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Fisher_Exact_Test_Statistics ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Fisher_Exact_Test_Statistics, ";
						$query_str = $query_str . "GROUP_CONCAT(DISTINCT PHENO2.Fisher_Exact_Test_P_Value ORDER BY PHENO2.Position ASC SEPARATOR '; ') AS Fisher_Exact_Test_P_Value ";
						$query_str = $query_str . "FROM ( ";
						$query_str = $query_str . "	SELECT PHENO.Chromosome, PHENO.Position, ";
						$query_str = $query_str . "	CONCAT_WS(' vs ', PHENO.Allele_1, PHENO.Allele_2) AS Allele, ";
						$query_str = $query_str . "	PHENO.Phenotype, PHENO.Phenotype_Data_Type, ";
						$query_str = $query_str . "	PHENO.Test_Method, CONCAT_WS(' vs ', PHENO.Phenotype_Category_1, PHENO.Phenotype_Category_2) AS Phenotype_Category, ";
						$query_str = $query_str . "	PHENO.Shapiro_Test_Statistics, PHENO.Shapiro_Test_P_Value, ";
						$query_str = $query_str . "	PHENO.Mann_Whitney_U_Rank_Test_Statistics, PHENO.Mann_Whitney_U_Rank_Test_P_Value, ";
						$query_str = $query_str . "	PHENO.T_Test_Statistics, PHENO.T_Test_P_Value, ";
						$query_str = $query_str . "	PHENO.Chi_Square_Test_Statistics, PHENO.Chi_Square_Test_P_Value, ";
						$query_str = $query_str . "	PHENO.Fisher_Exact_Test_Statistics, PHENO.Fisher_Exact_Test_P_Value ";
						$query_str = $query_str . "	FROM " . $db . "." . $phenotype_distribution_table . " AS PHENO ";

						$query_str = $query_str . "	WHERE (PHENO.Phenotype IN ('";
						for ($j = 0; $j < count($phenotype_array_chunk[$i]); $j++) {
							if($j < (count($phenotype_array_chunk[$i])-1)){
								$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]) . "', '";
							} elseif ($j == (count($phenotype_array_chunk[$i])-1)) {
								$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]);
							}
						}
						$query_str = $query_str . "')) ";

						// $query_str = $query_str . "	WHERE (PHENO.Phenotype = '";
						// for ($j = 0; $j < count($phenotype_array_chunk[$i]); $j++) {
						// 	if($j < (count($phenotype_array_chunk[$i])-1)){
						// 		$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]) . "') OR (PHENO.Phenotype = '";
						// 	} elseif ($j == (count($phenotype_array_chunk[$i])-1)) {
						// 		$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]);
						// 	}
						// }
						// $query_str = $query_str . "') ";

						$query_str = $query_str . ") AS PHENO2 ";
						$query_str = $query_str . "GROUP BY PHENO2.Chromosome, PHENO2.Position, PHENO2.Phenotype ";
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
}


echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>