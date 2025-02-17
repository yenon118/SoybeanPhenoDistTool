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
        $("#accordion_2").accordion({
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
$dataset = trim($_GET['dataset_1']);
$chromosome = trim($_GET['chromosome_1']);
$phenotypes = $_GET['phenotype'];


$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

if (isset($chromosome)) {
    if (!empty($chromosome)) {
        $chromosome = clean_malicious_input($chromosome);
        $chromosome = preg_replace('/\s+/', '', $chromosome);
    }
}

$phenotypes = clean_malicious_input($phenotypes);


?>


<div id="message_div_2"></div>
<div id="accordion_2"></div>


<script type="text/javascript" language="javascript" src="./js/gene_summary_data.js"></script>

<script>
    var dataset = <?php if (isset($dataset)) {
                        echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);
                    } else {
                        echo "";
                    } ?>;
    var chromosome = <?php if (isset($chromosome)) {
                            echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;
    var phenotypes = <?php if (isset($phenotypes)) {
                            echo json_encode($phenotypes, JSON_INVALID_UTF8_IGNORE);
                        } else {
                            echo "";
                        } ?>;

    updateGeneRanking('accordion_2', 'message_div_2', dataset, phenotypes);
</script>


<?php include '../footer.php'; ?>