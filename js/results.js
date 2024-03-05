async function queryPhenotypeDistribution(dataset, chromosome, phenotypes) {
	return new Promise((resolve, reject) => {
		$.ajax({
			url: './php/queryPhenotypeDistribution.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Dataset: dataset,
				Chromosome: chromosome,
				Phenotypes: phenotypes
			},
			success: function (response) {
				var res = JSON.parse(response);
				res = res.data;

				resolve(res);
			}, error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
				reject([]);
			}
		});
	});
}

async function updatePhenotypeDistribution(phenotype_accordion_id, dataset, chromosome, phenotypes) {
	var result_array = await queryPhenotypeDistribution(dataset, chromosome, phenotypes);
	if (result_array) {
		// Check the existance of accordion instance and remove if it exists
		var accordion_instance = $("#" + String(phenotype_accordion_id)).accordion("instance");
		if (accordion_instance != undefined) {
			$("#" + String(phenotype_accordion_id)).accordion("destroy");
			$("#" + String(phenotype_accordion_id)).empty();
		}
		document.getElementById(phenotype_accordion_id).innerHTML = "";

		phenotype_array = [];
		for (let i = 0; i < result_array.length; i++) {
			var header_array = Object.keys(result_array[i]);
			var current_phenotype = result_array[i]['Phenotype'];
			var current_phenotype_data_type = result_array[i]['Phenotype_Data_Type'];

			if (!phenotype_array.includes(current_phenotype)) {
				// Create a h3 tag
				var h3_tag = document.createElement('h3');
				// Set id attribute
				h3_tag.id = "ui-id-h3-" + String(current_phenotype.replace(/ /g, "_"));
				// Set aria-controls attribute
				h3_tag.setAttribute("aria-controls", "ui-id-div-" + String(current_phenotype.replace(/ /g, "_")));
				// Set inner HTML
				h3_tag.innerHTML = current_phenotype;
				// Append h3 tag
				document.getElementById(phenotype_accordion_id).appendChild(h3_tag);

				// Create a div tag
				var div_tag = document.createElement('div');
				// Set id attribute
				div_tag.id = "ui-id-div-" + String(current_phenotype.replace(/ /g, "_"));
				// Set aria-labelledby attribute for the div element
				div_tag.setAttribute("aria-labelledby", "ui-id-h3-" + String(current_phenotype.replace(/ /g, "_")));
				// Append div tag
				document.getElementById(phenotype_accordion_id).appendChild(div_tag);

				// Add table into div tag
				var table_tag = document.createElement('table');
				// Set id attribute
				table_tag.id = "table-" + String(current_phenotype.replace(/ /g, "_"));
				// Append div tag
				div_tag.appendChild(table_tag);

				// Add tr into table tag
				var header_tr_tag = document.createElement("tr");
				// Add headings
				for (let i = 0; i < header_array.length; i++) {
					var header_th_tag = document.createElement("th");
					header_th_tag.setAttribute("style", "border:1px solid black; min-width:100px; height:18.5px;");
					header_th_tag.innerHTML = header_array[i];
					header_tr_tag.appendChild(header_th_tag);
				}
				// Append div tag
				table_tag.appendChild(header_tr_tag);

				phenotype_array.push(current_phenotype);
			}

			// Add content
			var detail_tr_tag = document.createElement("tr");
			detail_tr_tag.style.backgroundColor = ((i % 2) ? "#FFFFFF" : "#DDFFDD");
			for (let j = 0; j < header_array.length; j++) {
				var detail_td_tag = document.createElement("td");
				detail_td_tag.setAttribute("style", "border:1px solid black; min-width:100px; height:18.5px;");
				detail_td_tag.innerHTML = result_array[i][header_array[j]];
				detail_tr_tag.appendChild(detail_td_tag);
			}
			document.getElementById("table-" + String(current_phenotype.replace(/ /g, "_"))).appendChild(detail_tr_tag);

		}

		// Make accordion
		$("#" + String(phenotype_accordion_id)).accordion({
			active: false,
			collapsible: true,
			icons: ""
		});
	}
}
