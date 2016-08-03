var waxrepeater = {
	refreshAfterFormSave : function (response) {
		$('#edit-' + response.params.repeaterId).modal('hide');
		$('#add-' + response.params.repeaterId).modal('hide');
		$('#' + response.params.repeaterId).waxrepeater('refresh', {
			refreshUrl : false
		});
	},
	createAddModal : function (repeaterId) {
		var modal = $('#add-'+repeaterId).clone();
		var form = $('#add-'+repeaterId).find('*').contents().filter(function(){
		     return this.nodeType === 8;
		});
		form = $(form[0].data);
		$('#add-'+repeaterId).remove();
		$('body').append(modal);
		$('#add-'+repeaterId+' .modal-body').html(form);

		var selector = '#add-'+repeaterId+' .do-submit-add';

		$(selector).unbind('click');

		$(document).off('click', selector).on('click', selector, {} ,function(e){
			e.preventDefault();

			var form = $(this).closest('.modal').find('form');
			
			$(this).closest('.modal').find('.btn-can-load').button('loading');

			form.data('formValidation').validate();
			//form.trigger('submit');
		})
	},
	createEditModal : function (repeaterId) {
		var modal = $('#edit-' + repeaterId).clone();
		$('#edit-' + repeaterId).remove();
		$('body').append(modal);

		var selector = '#edit-'+repeaterId+' .do-submit-edit';

		$(selector).unbind('click');

		$(document).off('click', selector).on('click', selector, {} ,function(e){
			e.preventDefault();

			var form = $(this).closest('.modal').find('form');

			$(this).closest('.modal').find('.btn-can-load').button('loading');
			
			form.data('formValidation').validate();
			//form.trigger('submit');
		})
	}
}