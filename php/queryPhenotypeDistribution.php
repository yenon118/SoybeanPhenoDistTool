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
						$query_str = "SELECT ";
						$query_str = $query_str . "PHENO.Chromosome, PHENO.Position, PHENO.Allele_1, PHENO.Allele_2, ";
						$query_str = $query_str . "PHENO.Phenotype, PHENO.Phenotype_Data_Type, ";
						$query_str = $query_str . "PHENO.Test_Method, ";
						$query_str = $query_str . "PHENO.Phenotype_Category_1, PHENO.Phenotype_Category_2, ";
						$query_str = $query_str . "PHENO.Shapiro_Test_Statistics, PHENO.Shapiro_Test_P_Value, ";
						$query_str = $query_str . "PHENO.Mann_Whitney_U_Rank_Test_Statistics, PHENO.Mann_Whitney_U_Rank_Test_P_Value, ";
						$query_str = $query_str . "PHENO.T_Test_Statistics, PHENO.T_Test_P_Value, ";
						$query_str = $query_str . "PHENO.Chi_Square_Test_Statistics, PHENO.Chi_Square_Test_P_Value, ";
						$query_str = $query_str . "PHENO.Fisher_Exact_Test_Statistics, PHENO.Fisher_Exact_Test_P_Value ";
						// $query_str = $query_str . "GFF.Name AS Gene ";
						$query_str = $query_str . "FROM " . $db . "." . $phenotype_distribution_table . " AS PHENO ";
						// $query_str = $query_str . "LEFT JOIN " . $db . "." . $gff_table . " AS GFF ";
						// $query_str = $query_str . "ON (PHENO.Chromosome = GFF.Chromosome) AND ((PHENO.Position >= GFF.Start) AND (PHENO.Position <= GFF.End)) ";

						$query_str = $query_str . " WHERE (PHENO.Phenotype IN ('";
						for ($j = 0; $j < count($phenotype_array_chunk[$i]); $j++) {
							if($j < (count($phenotype_array_chunk[$i])-1)){
								$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]) . "', '";
							} elseif ($j == (count($phenotype_array_chunk[$i])-1)) {
								$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]);
							}
						}
						$query_str = $query_str . "'))";

						// $query_str = $query_str . " WHERE (Phenotype = '";
						// for ($j = 0; $j < count($phenotype_array_chunk[$i]); $j++) {
						// 	if($j < (count($phenotype_array_chunk[$i])-1)){
						// 		$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]) . "') OR (Phenotype = '";
						// 	} elseif ($j == (count($phenotype_array_chunk[$i])-1)) {
						// 		$query_str = $query_str . trim($phenotype_array_chunk[$i][$j]);
						// 	}
						// }
						// $query_str = $query_str . "')";

						$query_str = $query_str . "; ";

						
						try {
							// Execute SQL string
							$stmt = $PDO->prepare($query_str);
							$stmt->execute();
							$result = $stmt->fetchAll();

							// Populate results
							if (isset($result)) {
								if (!empty($result)) {
									if (is_array($result)) {
										if (count($result) > 0) {
											for ($m = 0; $m < count($result); $m++) {
												array_push($result_arr, array());
												for ($n = 0; $n < count($result[$m]); $n++) {
													$key = array_keys($result[$m])[$n];
													if (is_string($key)) {
														$result_arr[$m][$key] = $result[$m][$key];
													}
												}
											}
										}
									}
								}
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