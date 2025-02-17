<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<?php
$TITLE = "Soybean Phenotype Distribution Tool";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
</link>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
<style>
    .ui-accordion-header.ui-state-active {
        background-color: green;
    }
</style>
<script>
    $(function() {
        $("#accordion_3").accordion({
            active: false,
            collapsible: true
        });
    });
</script>


<!-- Back button -->
<a href="/SoybeanPhenoDistTool/"><button> &lt; Back </button></a>

<br />
<br />


<?php
$dataset = trim($_GET['Dataset']);
$gene = trim($_GET['Gene']);
$phenotypes = $_GET['Phenotype'];


$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$gene = clean_malicious_input($gene);
$gene = preg_replace('/\s+/', '', $gene);

$phenotypes = clean_malicious_input($phenotypes);


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

?>


<div id="message_div_3"></div>
<div id="accordion_3"></div>


<script type="text/javascript" language="javascript" src="./js/statistical_results.js"></script>

<script>
    var dataset = <?php if (isset($dataset)) {
                        echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);
                    } else {
                        echo "";
                    } ?>;
    var gene = <?php if (isset($gene)) {
                    echo json_encode($gene, JSON_INVALID_UTF8_IGNORE);
                } else {
                    echo "";
                } ?>;
    var phenotypes = <?php if (isset($phenotype_array)) {
                            echo json_encode($phenotype_array, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;

    updatePhenotypeDistribution('accordion_3', 'message_div_3', dataset, gene, phenotypes);
</script>


<?php include '../footer.php'; ?>