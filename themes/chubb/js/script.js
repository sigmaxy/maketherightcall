/**
 * @file
 * This file is to add any custom js for the drupal8 w3css subtheme.
 */
jQuery(document).ready(function($) {
	var datatable = $('table.table_list_data');

	/**
	 * Example of drupal form table attribute configuration for column sorting and filtering:
	 *
	 '#attributes' => [
		 'class' => ['wholesale_payment_list','table_list_data'],
		 'col_sort_index' => 0,
		 'col_sort_type' => 'desc',
		 'filter_columns' => json_encode([2]),
     'filter_numeric_columns' => json_encode([0]),
		 'filter_select_columns' => json_encode(
			 [5 => ['Option 1', 'Option 2']]
		 ),
	 ],
	 */

		// Initialize datatables
	var datatableObj = datatable.DataTable({
		"order": datatable.attr('col_sort_index') ? [ datatable.attr('col_sort_index'), datatable.attr('col_sort_type')] : [],
		"pageLength": datatable.attr('default_page_length') ? datatable.attr('default_page_length') : 10,
		"orderCellsTop": true,
		"processing": true,
			initComplete: function () {

			var filterColumns = datatable.attr('filter_columns');
      var filterNumericColumns = datatable.attr('filter_numeric_columns');
			var filterSelectColumns = datatable.attr('filter_select_columns');

			// Add column search filters
			if ((filterColumns !== undefined && filterColumns !== '') ||
				(filterSelectColumns !== undefined && filterSelectColumns !== '') ||
        (filterNumericColumns !== undefined && filterNumericColumns !== '')) {

				var filterColumnArray = [];
        var filterNumericColumnArray = [];
				var filterSelectColumnObj = [];
				if (filterColumns !== undefined && filterColumns !== '') {
					filterColumnArray = JSON.parse(filterColumns);
				}
        if (filterNumericColumns !== undefined && filterNumericColumns !== '') {
          filterNumericColumnArray = JSON.parse(filterNumericColumns);
        }
				if (filterSelectColumns !== undefined && filterSelectColumns !== '') {
					filterSelectColumnObj = JSON.parse(filterSelectColumns);
				}
				// $('thead', datatable).append($('thead tr', datatable).clone());
				var tr = $('<tr>');
				tr.append($.map($('thead tr th', datatable), function() {
					return $('<th></th>');
				}));
				$('thead', datatable).append(tr);
				$('thead tr:eq(1) th', datatable).each(function (index) {
					var inputMaxWidth = Math.max($(this).width(), 70);
					if (filterColumnArray.includes(index)) {
						// var title = $(this).text();
						$(this).html('<input type="text" style="max-width: '+inputMaxWidth+'px;" class="fuzzy-column-search" />');
					} else if (filterNumericColumnArray.includes(index)) {
            $(this).html('<input type="text" style="max-width: '+inputMaxWidth+'px;" class="column-search" />');
          } else if (filterSelectColumnObj.hasOwnProperty(index)) {
						var s = $('<select style="max-width: '+inputMaxWidth+'px;" class="column-search"/>');
						s.append($('<option/>', {text: '', value: ''}));
						s.append($.map(filterSelectColumnObj[index], function(val) {
							return $('<option/>', {text: val, value: val});
						}));
						$(this).html(s);
					}
				});

				// Search on column filter changes
				$('thead', datatable).on('keyup change', ".fuzzy-column-search", function () {
					datatableObj
						.column($(this).parent().index())
						.search(this.value)
						.draw();
				});
        $('thead', datatable).on('keyup change', ".column-search", function () {
          datatableObj
            .column($(this).parent().index())
            .search(this.value == '' ? '' : '^'+$.trim(this.value)+'$', true, false)
            .draw();
        });
			}
		}
	});

	$('select:not(".noselect2")').select2();

});
