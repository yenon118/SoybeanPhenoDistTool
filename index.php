
<?php
$TITLE = "Soybean Phenotype Distribution Tool";

include '../header.php';
// include '../config.php';
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
		$("#accordion_1").accordion({
			active: false,
			collapsible: true
		});
	});
</script>


<form action="results.php" onsubmit="return validateForm('Gmax', 'error_message_div')" method="get">
	<div id="accordion_1"></div>

	<br/><br/>

	<div style='margin-top:10px;' align='center'>
		<button type="button" onclick="uncheck_all_phenotypes('Gmax')" style="margin-right:20px;">Uncheck All Phenotypes</button>
		<button type="button" onclick="check_all_phenotypes('Gmax')" style="margin-right:20px;">Check All Phenotypes</button>

		<label for="dataset_1" style="margin-left:20px;"><b>Dataset:</b></label>
		<select name="dataset_1" id="dataset_1" onchange="updateChromosomeByDataPanelSelection(event)"></select>

		<label for="chromosome_1" style="margin-left:20px;"><b>Chromosome:</b></label>
		<select name="chromosome_1" id="chromosome_1"></select>
	</div>

	<br/><br/>

	<div id='error_message_div' style='margin-top:10px;' align='center'></div>

	<div style='margin-top:10px;' align='center'>
		<input type="submit" value="Search">
	</div>
</form>


<script type="text/javascript" language="javascript" src="./js/index.js"></script>

<script type="text/javascript" language="javascript">
	updatePhenotypeSelections('accordion_1', 'Soy2939');
	updateDatasetSelections('dataset_1');
	updateChromosomeSelections('chromosome_1', 'Soy2939');
</script>


<?php include '../footer.php'; ?>
