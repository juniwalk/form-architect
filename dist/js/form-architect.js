
function form_architect_init()
{
	var descriptionOnChange = false;

	$('textarea.fd-editor').summernote({
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

	$(document).click(function (e) {
		$('.fd-field').removeClass('active');

		var $target = $(e.target).closest('.fd-field');
		var $handle = $(e.target).closest('.fd-toolbar.vertical');

		if (!$target.length || $handle.length) {
			return;
		}

		$target.addClass('active');
	});

	$('[data-autosave]').off('change').on('change', function(e) {
		return $(this).closest('form').submit();
	});
}

$(function () {

	form_architect_init();

	$.nette.ext('snippets').after(function() {
		form_architect_init();
	});

	$.nette.ext('fd-spinner', {
		start: function (xhr, settings) {
			this.spinner = setTimeout(function () {
				$('.fd-overlay').removeClass('invisible');
			}, 50)
		},
		complete: function (xhr, status, settings) {
			clearTimeout(this.spinner);
			$('.fd-overlay').addClass('invisible');
		}
	});

});
