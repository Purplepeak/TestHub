function readURL(input, img) {
	if (input.files && input.files[0]) {
		
		if(uploadImageConfig['maxImageSize'] && input.files[0].size > uploadImageConfig['maxImageSize']) {
			$('.s-js-error').text('Размер изображения не должен превышать ' + formatBytes(uploadImageConfig['maxImageSize']));
			return false;
		}
		if(input.files[0].type !== 'image/jpeg' && input.files[0].type !== 'image/png' && input.files[0].type !== 'image/gif') {
			$('.s-js-error').text('Убедитесь, что вы выбради изображения из числа допустимых форматов (jpeg, gif, png).');
			return false;
		}
		
		var reader = new FileReader();
		
		reader.onload = function(e) {
			
			img.onload = function() {
				var imageWidth = img.width;
				var imageHeight = img.height;
				
				var imageRatio = imageWidth / imageHeight;
				
				if(imageWidth > uploadImageConfig['maxImageWidth'] || imageHeight > uploadImageConfig['maxImageHeight'] || imageWidth < uploadImageConfig['minImageWidth'] || imageHeight < uploadImageConfig['minImageHeight'] || uploadImageConfig['minImageRatio'] > imageRatio > uploadImageConfig['maxImageRatio']) {
					$('.s-js-error').text('Размеры сторон изображения должны лежать в пределах от ' + uploadImageConfig['minImageWidth'] + ' до ' + uploadImageConfig['maxImageWidth'] + ' пикселей. Так же нельзя использовать изображение, размеры одной из сторон которого значительно превышают другую.');
					document.getElementById('avatar-uploader').reset();
					
					return false;
				}
				
				$(img).appendTo('.s-src-avatar');
				
				var imgStyle = {
						'display': 'block',
			            'max-width':previewMaxWidth + 'px',
			            'max-height':previewMaxHeight + 'px',
			            'width': 'auto',
			            'height': 'auto',
				}
				
				$(img).css(imgStyle);
				
				var imagePreviewWidth = $('.s-src-avatar').width();
				var imagePreviewHeight = $('.s-src-avatar').height();
				
				var multiplierX;
				var multiplierY;

				multiplierX = getMultiplier(imageWidth, imagePreviewWidth);
			    multiplierY = getMultiplier(imageHeight, imagePreviewHeight);
				
				$(img).imgAreaSelect({
					handles : true,
					parent : '.s-src-avatar',
					aspectRatio : imgAreaSelectConfig['aspectRatio'],
					maxHeight : imgAreaSelectConfig['maxHeight'],
					maxWidth : imgAreaSelectConfig['maxWidth'],
					minHeight : imgAreaSelectConfig['minHeight'],
					minWidth : imgAreaSelectConfig['minWidth'],
					x1 : imgAreaSelectConfig['x1'],
					y1 : imgAreaSelectConfig['y1'],
					x2 : imgAreaSelectConfig['x2'],
					y2 : imgAreaSelectConfig['y2'],
					onInit : function(img, selection) {
						populateForm(selection.x1, selection.y1, selection.width, selection.height, multiplierX, multiplierY);
					},
					onSelectChange : function(img, selection) {
						if(!selection.width || !selection.height) {
							return;
						}
						populateForm(selection.x1, selection.y1, selection.width, selection.height, multiplierX, multiplierY);
					}
				});
			}
			
			img.src = e.target.result;
		}

		reader.readAsDataURL(input.files[0]);
	}
}
$(document).ready(function() {

	var element = {
		    srcAvatarDiv : $("<div>", {class: "s-src-avatar"}),
		    errorDiv : $("<div>", {class: "s-js-error"})
	};
	
	element.errorDiv.appendTo('.js-avatar-wrapper');
	element.srcAvatarDiv.appendTo('.js-avatar-wrapper');
	
	$(".s-avatar-input").on('change', function(){
		
		if($('.s-src-avatar').length) {
			$('.s-src-avatar').empty();
		}
		
		var img = new Image();
		
		$(img).attr('class', '' + "src-avatar-img");
		
		$('.s-js-error').empty();
		
		readURL(this, img);
    });
	
});

function populateForm(x, y, width, height, multiplierX, multiplierY) {
	$('.image-x').val(x * multiplierX);
	$('.image-y').val(y * multiplierY);
	$('.crop-width').val(width * multiplierX);
	$('.crop-height').val(height * multiplierY);
	//console.log(x * multiplierX, y * multiplierY, width * multiplierX, height * multiplierY, 'SRC', x, y, width, height, 'MP', multiplierX, multiplierY);
}

function getMultiplier(numerator, denominator) {
	if(numerator > denominator) {
		return (numerator / denominator).toFixed(5);
	} else {
		return '1';
	}
}

function formatBytes(bytes) {
	   if(bytes == 0) return '0 Байт';
	   var k = 1024;
	   var sizes = ['Байт', 'КБ', 'МБ'];
	   var i = Math.floor(Math.log(bytes) / Math.log(k));
	   return parseFloat((bytes / Math.pow(k, i)).toPrecision(3)) + sizes[i];
	}