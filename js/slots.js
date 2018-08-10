$(function(){
	$("select#zone").change(function(){
		
		 var str = "";
	    $("select#zone option:selected" ).each(function() {
	      str += $( this ).text() + " ";
    		});
    		
	   	// use str to pull list of slots via ajax
	   	var postdata = "zone=" + str + "&sku=" + $("hidden#barcode").val();
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "getslots.php",
			data: postdata,
			success: function(data) {
				//alert(data);
				$("select#pallet_loc").html(data);
				$("select#pallet_loc2").html("<option value=\"\">(not used)</option>" + data);
				$("select#pallet_loc3").html("<option value=\"\">(not used)</option>" + data);
				return true;
      			}
		
     		});
  })
  .change();

});
