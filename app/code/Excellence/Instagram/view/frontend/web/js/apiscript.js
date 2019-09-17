
require(
	[
	'jquery',
	'prototype'
	], function(jQuery) {
		jQuery(".searchImg").on('click', function() {
			var textVal = jQuery("#searchBox").val();
			hiddenHtml = "<input type='hidden' name='instaTag' value=" + textVal + ">";
			jQuery(".hiddenImg").html(hiddenHtml);
			var contentHtml = "";
			jQuery.each(json, function(key, value) {
				var str = value.tag;
				if (str != null) {
					if (str.match(textVal)) {
						contentHtml += "<img class='imageSelect' src=" + value.thmbimg + ">" + "<input class='checkboxImage' ame='instaImgUrl[]' type='checkbox' value=" + value.thmbimg + ">";
					}
				}
				if(textVal){
					jQuery(".imagesShow").html(contentHtml);
				}                
			});
		});
	});
