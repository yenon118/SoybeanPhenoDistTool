<?php
$TITLE = "Soybean Phenotype Distribution Tool";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css"></link>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
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
?>


<div id="accordion_2"></div>


<script type="text/javascript" language="javascript" src="./js/gene_summary_data.js"></script>

<script>
    var dataset = <?php if(isset($dataset)) {echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var chromosome = <?php if(isset($chromosome)) {echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var phenotypes = <?php if(isset($phenotypes)) {echo json_encode($phenotypes, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;

    updateGeneRanking('accordion_2', dataset, phenotypes);
</script>


<?php include '../footer.php'; ?>
