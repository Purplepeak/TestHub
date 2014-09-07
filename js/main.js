function readURL(input, img) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function(e) {
			
			$(img).attr('src', '' + e.target.result);
			$(img).appendTo('.src-avatar');
			
			$('img.src-avatar-img').imgAreaSelect({
				handles : true,
				aspectRatio : '1:1',
				maxHeight : '400',
				maxWidth : '400',
				minHeight : '190',
				minWidth : '190',
				x1 : '0',
				y1 : '0',
				x2 : '190',
				y2 : '190',
				onInit : function(img, selection) {
					populateForm(selection.x1, selection.y1, selection.width, selection.height);
				},
				onSelectEnd : function(img, selection) {
					populateForm(selection.x1, selection.y1, selection.width, selection.height);
				}
			});
		}

		reader.readAsDataURL(input.files[0]);
	}
}
$(document).ready(function() {
	var img = new Image();
	$(img).attr('class', '' + "src-avatar-img");
	
	$(".avatar-input").on('change', function(){
		readURL(this, img);
    });
});

function populateForm(x, y, width, height) {
	$('.image-x').val(x);
	$('.image-y').val(y);
	$('.crop-width').val(width);
	$('.crop-height').val(height);
}