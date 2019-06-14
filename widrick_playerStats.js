jQuery(document).ready(function() {

var tables = document.getElementsByClassName('widrick_fullPlayerStatsTable');
console.log(tables.length);
for(var i = 0; i < tables.length; i++)
{
	var table = tables[i];
	console.log(table);
	jQuery(table).tablesorter();
}

	jQuery('#data-highlight tbody tr').click(function(e) {
    jQuery('#data-highlight tbody tr').removeClass('highlighted');
    jQuery(this).addClass('highlighted');
})
});