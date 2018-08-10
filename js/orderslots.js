
function zoneChange(itemRow)
{
	var str = $("select#itemZone"+itemRow).val()
	
	
	var postdata = "zone=" + str;
	//alert(postdata);
	var data;
	jQuery.ajax({
		type: "POST",
		url: "getorderslots.php",
		data: postdata,
		success: function(data) {
			$("select#item_binloc"+itemRow).html(data);
			$("select#item_binloc2"+itemRow).html("<option value=\"\">(not used)</option>" + data);
			$("select#item_binloc3"+itemRow).html("<option value=\"\">(not used)</option>" + data);
			return true;
      		}
		
     	});

}

