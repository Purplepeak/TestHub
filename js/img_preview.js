/*
$(document).ready(function() {
	var img = new Image();
	$(img).attr('class', '' + "src-avatar-img");
	$(img).attr('src', '' + srcImg);

	// $(".src-avatar-img").css('max-width', "100%");

	$('#avatar-form').submit(function(event) {
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
			onSelectEnd : function(img, selection) {
				$('input[name=x]').val(selection.x1);
				$('input[name=y]').val(selection.y1);
				$('input[name=width]').val(selection.width);
				$('input[name=height]').val(selection.height);
			}
		});

		var imageWidth = $('.src-avatar-img').prop('width');
		var imageHeight = $('.src-avatar-img').prop('height');

		event.preventDefault();

		$(".src-avatar-img").css('max-width', "100%");
		$(".src-avatar-img").css('height', "auto");

		var wrapperStyle;

		if (imageWidth > imageHeight) {
			wrapperStyle = {
				width : "600px",
				height : "380px"
			}
		}

		if (imageHeight > imageWidth) {
			wrapperStyle = {
				width : "400px",
				height : "600px"
			}
		}

		$(".src-avatar").css(wrapperStyle);
	});
});
*/