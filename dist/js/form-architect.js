
function form_architect_init()
{
	var descriptionOnChange = false;

	$('.icp-auto').iconpicker({
		placement: 'bottomLeft',
		hideOnSelect: true
	});

	$('.icp-auto').on('iconpickerSelected', function(event){
		$(this).change();
	});


	$('textarea.wysiwyg').summernote({
		disableDragAndDrop: true,
		dialogsFade: true,
		toolbar: [
			['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
			['para', ['style', 'ul', 'ol', 'paragraph']],
			['insert', ['link', 'table']],
			['misc', ['fullscreen', 'codeview', 'help']],
		],
		callbacks: {
			onChange: function (contents, $editable) {
				descriptionOnChange = true;
			},
			onBlur: function () {
				if (!descriptionOnChange) {
					return;
				}

				$(this).text($(this).summernote('code'));
				$(this).trigger("change");
				descriptionOnChange = false;
			}
		}
	});

	$('[data-autosave]').off('change').on('change', function(e) {
		return $(this).closest('form')
			.find("[name=autosave]")
			.trigger("click");
	});
}

$(function () {

	form_architect_init();

	$.nette.ext('snippets').after(function() {
		form_architect_init();
	});

	$.nette.ext('fa-spinner', {
		start: function (xhr, settings) {
			this.spinner = setTimeout(function () {
				$('.fa-overlay').removeClass('invisible');
			}, 50)
		},
		complete: function (xhr, status, settings) {
			clearTimeout(this.spinner);
			$('.fa-overlay').addClass('invisible');
		}
	});

});
