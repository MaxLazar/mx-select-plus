(function($) {
	var onDisplay = function(cell){

		var $select = $('select', cell.dom.$td),
			cell_id =  cell.field.id;
			cell_id =  cell_id.replace(/\[/g, "_");
			cell_id =  cell_id.replace(/\]/g, "_");

			id = cell_id +'_'+cell.row.id+'_'+cell.col.id+'_'+Math.floor(Math.random()*100000000);

			$select.attr('id', id);
			add_new_options = $select.data('no');
			allow_deselect = $select.data('deselect');
			$("#" +  id).chosen({add_new_options: add_new_options , add_new: cell.field.id, cell_obj: cell, allow_single_deselect: allow_deselect, group_class: "."+ cell.field.id +"_" +cell.col.id, callback: function() {}});
			$('div.publish_field.publish_matrix').css({'overflow' : 'visible'});


	};

	Matrix.bind('mx_select_plus', 'display', onDisplay);

})(jQuery);
