var SelectedPrimary = "";

var pageloaded = false;
var PrimaryCatPRE = "";
var SubCat1PRE = "";
var SubCat2PRE = "";
var SubCat3PRE = "";
var SubCat4PRE = "";

$(function(){
	$("#catpanel").hide();
	$("#primary_cat").hide();
	$("#subcat1").hide();
	$("#subcat2").hide();
	$("#subcat3").hide();
	$("#subcat4").hide();

});

$(function(){
	$("#catbtn").click(function(){
		$("#catpanel").show();
		$("#primary_cat").show();
		$("#subcat1").html("");
		$("#subcat1").show();
		$("#subcat2").html("");
		$("#subcat2").show();
		$("#subcat3").html("");
		$("#subcat3").show();
		$("#subcat4").html("");
		$("#subcat4").show();
		PrimaryCatPRE = '';
		SubCat1PRE = '';
		SubCat2PRE='';
		SubCat3PRE = '';
		SubCat4PRE = '';
		$("input#catchanged").val("1");
		catchanged=1;
		
		
	$("select#primary_cat option[value='none']").attr('selected','selected');
	$("select#primary_cat").change();
	$("select#primary_cat").show();
		
    	$("select#subcat1 option[value='none']").attr('selected','selected');
	$("select#subcat1").change();
	$("select#subcat1").show();	
	
	$("select#subcat2 option[value='none']").attr('selected','selected');
	$("select#subcat2").change();
	$("select#subcat2").show();

	$("select#subcat3 option[value='none']").attr('selected','selected');
	$("select#subcat3").change();
	$("select#subcat3").show();

	$("select#subcat4 option[value='none']").attr('selected','selected');
	$("select#subcat4").change();
	$("select#subcat4").show();	
		
	});
});

$(function(){
	$("select#primary_cat").change(function(){
		//add ajax stuff here
		 var str = "";

	    $("select#primary_cat option:selected" ).each(function() {
	      str += $( this ).text() + " ";
    		});
    		
	   	// use str to pull list of slots via ajax
	   	var postdata = "primarycat=" + str;
	   	var data;
	   	var subpre = "";
	   	
	   	if(SubCat1PRE != ''){
	   		subpre = "<option value='" + SubCat1PRE + "' selected>" + SubCat1PRE + "</option>\n<option value=''>(Select Subcategory - Level 1)</option>";
	   	}else{
	   		subpre = "<option value=''>(Select Subcategory - Level 1)</option>";
	   		
	   	}
	   				//	alert('primary changed ' + str);
		jQuery.ajax({
			type: "POST",
			url: "getsubcats1.php",
			data: postdata,
			success: function(data) {
				
				$("select#subcat1").html("" + subpre + data);
				$("select#subcat2").html("");
				$("select#subcat3").html("");
				$("select#subcat4").html("");
      			}
		
     		});
     		
     			PrimaryCatPRE = str;
     			SubCat1PRE = '';
     			SubCat2PRE='';
     			SubCat3PRE='';
     			SubCat4PRE='';
     		

  })
 .change();

});

$(function(){
	$("select#subcat1").change(function(){
	
		 var str = "";
		 var Pristr = "";
	    $("select#primary_cat option:selected" ).each(function() {
	      Pristr += $( this ).text() + " ";
    		});
	    $("select#subcat1 option:selected" ).each(function() {
	      str += $( this ).text() + " ";
    		});

      		$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();	
\
	   	// use str to pull list of slots via ajax
	   	var postdata = "primarycat=" +  Pristr + '&subcat1=' + str;
	   	var data2;
		var subpre2 = "";
	   	
	   	if(SubCat2PRE != ''){
	   		subpre2 = "<option value='" + SubCat2PRE + "' selected>" + SubCat2PRE + "</option>\n<option value=''>(Select Subcategory - Level 2)</option>";
	   	}else{
	   		subpre2 = "<option value=''>(Select Subcategory - Level 2)</option>";
	   	}	   	
		jQuery.ajax({
			type: "POST",
			url: "getsubcats2.php",
			data: postdata,
			success: function(data2) {
		
				$("select#subcat2").html("" + subpre2 + data2);
				$("select#subcat3").html("");
				$("select#subcat4").html("");
      			}
		
     		});
     		if(str != SubCat1PRE){
     			SubCat1PRE = str;
     		}
     		
  })
  .change();

});

$(function(){
	$("select#subcat2").change(function(){

		 var str = "";
		 var Pristr = "";
		 var Sub1str = "";
		 var Sub2str = "";
		 
	    $("select#primary_cat option:selected" ).each(function() {
	      Pristr += $( this ).text() + " ";
    		});
	    $("select#subcat1 option:selected" ).each(function() {
	      Sub1str += $( this ).text() + " ";
    		});
	    $("select#subcat2 option:selected" ).each(function() {
	      Sub2str += $( this ).text() + " ";
    		});

      		$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();	

	   	// use str to pull list of slots via ajax
	   	var postdata = "primarycat=" +  Pristr + '&subcat1=' + Sub1str + '&subcat2=' + Sub2str;
	   	var data3;
	 	var subpre3 = "";
	   	
	   	if(SubCat3PRE != ''){
	   		subpre3 = "<option value='" + SubCat3PRE + "' selected>" + SubCat3PRE + "</option>\n<option value=''>(Select Subcategory - Level 3)</option>";
	   	}else{
	   		subpre3="<option value=''>(Select Subcategory - Level 3)</option>";	  	
	   	}
		jQuery.ajax({
			type: "POST",
			url: "getsubcats3.php",
			data: postdata,
			success: function(data3) {

				$("select#subcat3").html("" + subpre3 + data3);

      			}
		
     		});
     		if(Sub2str != SubCat2PRE){
     			SubCat2PRE = Sub2str;
     		}
     		
  })
  .change();

});

$(function(){
	$("select#subcat3").change(function(){

		 var str = "";
		 var Pristr = "";
		 var Sub1str = "";
		 var Sub2str = "";
		 var Sub3str = "";
		 
	    $("select#primary_cat option:selected" ).each(function() {
	      Pristr += $( this ).text() + " ";
    		});
	    $("select#subcat1 option:selected" ).each(function() {
	      Sub1str += $( this ).text() + " ";
    		});
	    $("select#subcat2 option:selected" ).each(function() {
	      Sub2str += $( this ).text() + " ";
    		});
	    $("select#subcat3 option:selected" ).each(function() {
	      Sub3str += $( this ).text() + " ";
    		});

      		$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();	

	   	// use str to pull list of slots via ajax
	   	var postdata = "primarycat=" +  Pristr + '&subcat1=' + Sub1str + '&subcat2=' + Sub2str+'&subcat3='+Sub3str;
	   	var data4;
	   	var subpre4 = "";
	   	
	   	if(SubCat4PRE != ''){
	   		subpre4 = "<option value='" + SubCat4PRE + "' selected>" + SubCat4PRE + "</option>\n<option value=''>(Select Subcategory - Level 4)</option>";
	   	}else{
	   		subpre4 = "<option value=''>(Select Subcategory - Level 4)</option>";
	   	}
		jQuery.ajax({
			type: "POST",
			url: "getsubcats4.php",
			data: postdata,
			success: function(data4) {
	
				$("select#subcat4").html("" + subpre4 + data4);

      			}
		
     		});
     		if(Sub3str != SubCat3PRE){
     			SubCat3PRE = str;
     		}
     		
  })
  .change();

});


$(function(){
$( "#addbtn" ).click(function(event) {
	$("#addprimaryForm").show();
	$("#adds1Form").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();	
	$("input#newprimary").focus();
	event.preventDefault();

});
});

$(function(){
$("#AddNewPrimaryBtn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	Pristr = $("input#newprimary").val();
	
	var postdata = "newprimary=" +  Pristr;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "addprimarycat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php";
				//return true;
      			}
		
     		});
	$("#addprimaryForm").hide();
	event.preventDefault();

});
});
$(function(){
	$("#CancelAddNewPrimaryBtn" ).click(function( event ) {
		$("#addprimaryForm").hide();
	});
});	

// add subcategory - Level 1
$(function(){
$( "#adds1btn" ).click(function(event) {
	var Pristr = "";
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	
	$("#adds1Form").show();
	$("#addprimaryForm").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();	
	$("input#news1").focus();
	event.preventDefault();

});
});

$(function(){
$("#AddNewS1Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var s1 = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
    	

	s1 = $("input#news1").val();
	
	var postdata = "news1=" +  s1 + '&primarycat=' + Pristr;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "adds1cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+s1;
				//return true;
      			}
		
     		});
	$("#adds1Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelAddNewS1Btn" ).click(function( event ) {
		$("#adds1Form").hide();
	});
});	


// add subcategory - Level 2
$(function(){
$( "#adds2btn" ).click(function(event) {
	var Pristr = "";
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)"){
    		alert("Error: No primary selected...");
    		return false;
    	}

	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subcategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory 1 selected...");
    		return false;
    	}
	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	
	$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds3Form").hide();	
	$("#adds4Form").hide();		
	$("#adds2Form").show();
	$("input#news2").focus();
	event.preventDefault();

});
});

$(function(){
$("#AddNewS2Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
    	

	s2 = $("input#news2").val();
	
	var postdata = "news2=" +  s2 + '&primarycat=' + Pristr + '&subcat1=' +Sub1str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "adds2cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+Sub1str+'&subcat2='+s2;
				//return true;
      			}
		
     		});
	$("#adds2Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelAddNewS2Btn" ).click(function( event ) {
		$("#adds2Form").hide();
	});
});	


// add subcategory - Level 3
$(function(){
$( "#adds3btn" ).click(function(event) {
	var Pristr = "";
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)"){
    		alert("Error: No primary selected...");
    		return false;
    	}

	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subcategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory 1 selected...");
    		return false;
    	}

	var Sub2str = "";
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subcategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory 2 selected...");
    		return false;
    	}
	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	$("span#s2text").html(Sub2str);
	
	$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();	
	$("#adds4Form").hide();	
	$("#adds3Form").show();
	$("input#news3").focus();
	event.preventDefault();

});
});

$(function(){
$("#AddNewS3Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
	var Sub2str = "";
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});
    	

	s3 = $("input#news3").val();
	
	var postdata = "news3=" +  s3 + '&primarycat=' + Pristr + '&subcat1=' +Sub1str + '&subcat2=' +Sub2str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "adds3cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+Sub1str+'&subcat2=' +Sub2str+'&subcat3='+s3;
				//return true;
      			}
		
     		});
	$("#adds3Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelAddNewS3Btn" ).click(function( event ) {
		$("#adds3Form").hide();
	});
});	


// add subcategory - Level 4
$(function(){
$( "#adds4btn" ).click(function(event) {
	var Pristr = "";
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)"){
    		alert("Error: No primary selected...");
    		return false;
    	}

	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Primary Category)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory 1 selected...");
    		return false;
    	}

	var Sub2str = "";
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subcategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory 2 selected...");
    		return false;
    	}

	var Sub3str = "";
	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});

    	if(Sub3str == "(Select Subcategory - Level 3)" || Sub3str == null || Sub3str==''){
    		alert("Error: No subcategory 3 selected...");
    		return false;
    	}
	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	$("span#s2text").html(Sub2str);
	$("span#s3text").html(Sub3str);	
	
	$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();	
	$("#adds3Form").hide();		
	$("#adds4Form").show();
	$("input#news4").focus();
	event.preventDefault();

});
});

$(function(){
$("#AddNewS4Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	var Sub1str = "";
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
	var Sub2str = "";
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});
	var Sub3str = "";
	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});
    	

	s4 = $("input#news4").val();
	
	var postdata = "news4=" +  s4 + '&primarycat=' + Pristr + '&subcat1=' +Sub1str + '&subcat2=' +Sub2str+ '&subcat3=' +Sub3str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "adds4cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+Sub1str+'&subcat2=' +Sub2str+ '&subcat3=' +Sub3str+'&subcat4='+s4;
				//return true;
      			}
		
     		});
	$("#adds4Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelAddNewS4Btn" ).click(function( event ) {
		$("#adds4Form").hide();
	});
});

//=====================================================
// edit functions

// edit Primary
$(function(){
$( "#editbtn" ).click(function(event) {
	var Pristr = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}


	$("#editprimaryForm").show();
	$("input#editprimary").val(Pristr);

	$("#addprimaryForm").hide();
	$("#adds1Form").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#edits1Form").hide();
	$("#edits2Form").hide();
	$("#edits3Form").hide();
	$("input#editprimnary").focus();
	event.preventDefault();

});
});

$(function(){
$("#EditPrimaryBtn" ).click(function( event ) {
	// add processing code here
	var s1 = "";
	var Pristr = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
    	

	s1 = $("input#editprimary").val();
	
	var postdata = "editprimary=" +  s1 + '&primarycat=' + Pristr;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "editprimarycat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+s1;
				//return true;
      			}
		
     		});
	$("#editprimaryForm").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelEditPrimaryBtn" ).click(function( event ) {
		$("#editprimaryForm").hide();
	});
});	

// edit subcategory - Level 1
$(function(){
$( "#edits1btn" ).click(function(event) {
	var Pristr = "";
	var Sub1str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}
	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	
	// insert selected item

	
	$("#edits1Form").show();
	$("input#edits1").val(Sub1str);
		
	$("#addprimaryForm").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#editprimaryForm").hide();
	$("#edits2Form").hide();
	$("#edits3Form").hide();
	$("input#edits1").focus();
	event.preventDefault();

});
});

$(function(){
$("#EditS1Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	var s1 = "";
	
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
    	

	s1 = $("input#edits1").val();
	
	var postdata = "edits1=" +  s1 + '&primarycat=' + Pristr + '&subcat1=' + Sub1str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "edits1cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+s1;
				//return true;
      			}
		
     		});
	$("#edits1Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelEditS1Btn" ).click(function( event ) {
		$("#edits1Form").hide();
	});
});	

// edit subcategory - Level 2
$(function(){
$( "#edits2btn" ).click(function(event) {
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
		
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	
	// insert selected item

	
	$("#edits2Form").show();
	$("input#edits2").val(Sub2str);
		
	$("#addprimaryForm").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#editprimaryForm").hide();
	$("#edits1Form").hide();
	$("#edits3Form").hide();
	$("input#edits2").focus();
	event.preventDefault();

});
});

$(function(){
$("#EditS2Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var s2 = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}

	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
    	
    	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}


	s2 = $("input#edits2").val();
	
	var postdata = "edits2=" +  s2 + '&primarycat=' + Pristr + '&subcat1=' + Sub1str + '&subcat2=' +Sub2str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "edits2cat.php",
			data: postdata,
			success: function(data) {
			window.location.href='index.php?primarycat='+Pristr+'&subcat1='+Sub1str+'&subcat2='+s2;
				//return true;
      			}
		
     		});
	$("#edits2Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelEditS2Btn" ).click(function( event ) {
		$("#edits2Form").hide();
	});
});	


// edit subcategory - Level 3
$(function(){
$( "#edits3btn" ).click(function(event) {
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var Sub3str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});

    	if(Sub3str == "(Select Subcategory - Level 3)" || Sub3str == null || Sub3str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	$("span#s2text").html(Sub2str);	
	
	// insert selected item

	
	$("#edits3Form").show();
	$("input#edits3").val(Sub3str);
		
	$("#addprimaryForm").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#editprimaryForm").hide();
	$("#edits1Form").hide();
	$("#edits2Form").hide();
	$("input#edits3").focus();
	event.preventDefault();

});
});

$(function(){
$("#EditS3Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var Sub3str = "";
	var s3 = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});
	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});
    	

	s3 = $("input#edits3").val();
	

	var postdata = "edits3=" +  s3 + '&primarycat=' + Pristr + '&subcat1=' + Sub1str+ '&subcat2='+Sub2str+'&subcat3='+Sub3str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "edits3cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+Sub1str+'&subcat2='+Sub2str+'&subcat3='+s3;
				//return true;
      			}
		
     		});
	$("#edits3Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelEditS3Btn" ).click(function( event ) {
		$("#edits3Form").hide();
	});
});


// edit subcategory - Level 4
$(function(){
$( "#edits4btn" ).click(function(event) {
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var Sub3str = "";
	var Sub4str = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});

    	if(Pristr == "(Select Primary Category)" || Pristr == null || Pristr==''){
    		alert("Error: No primary selected...");
    		return false;
    	}
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});

    	if(Sub1str == "(Select Subategory - Level 1)" || Sub1str == null || Sub1str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});

    	if(Sub2str == "(Select Subategory - Level 2)" || Sub2str == null || Sub2str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});

    	if(Sub3str == "(Select Subcategory - Level 3)" || Sub3str == null || Sub3str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	$("select#subcat4 option:selected" ).each(function() {
		Sub4str += $( this ).text();
    	});

    	if(Sub4str == "(Select Subcategory - Level 4)" || Sub4str == null || Sub4str==''){
    		alert("Error: No subcategory selected...");
    		return false;
    	}

	//alert("Primary: " + Pristr);
	$("span#primarytext").html(Pristr);
	$("span#s1text").html(Sub1str);
	$("span#s2text").html(Sub2str);	
	$("span#s3text").html(Sub3str);	
	
	// insert selected item

	
	$("#edits4Form").show();
	$("input#edits4").val(Sub4str);
		
	$("#addprimaryForm").hide();
	$("#adds2Form").hide();
	$("#adds3Form").hide();
	$("#adds4Form").hide();
	$("#editprimaryForm").hide();
	$("#edits1Form").hide();
	$("#edits2Form").hide();
	$("#edits3Form").hide();
	$("input#edits4").focus();
	event.preventDefault();

});
});

$(function(){
$("#EditS4Btn" ).click(function( event ) {
	// add processing code here
	var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var Sub3str = "";
	var Sub4str = "";
	var s4 = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});
	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});
	$("select#subcat4 option:selected" ).each(function() {
		Sub4str += $( this ).text();
    	});
    	

	s4 = $("input#edits4").val();
	
	var postdata = "edits4=" +  s4 + '&primarycat=' + Pristr + '&subcat1=' + Sub1str+ '&subcat2='+Sub2str+ '&subcat3='+Sub3str+'&subcat4='+Sub4str;
	   	var data;
		jQuery.ajax({
			type: "POST",
			url: "edits3cat.php",
			data: postdata,
			success: function(data) {
			window.location.href="index.php?primarycat="+Pristr+'&subcat1='+Sub1str+'&subcat2='+Sub2str+'&subcat3='+Sub3str+'&subcat4='+s4;
				//return true;
      			}
		
     		});
	$("#edits4Form").hide();

	event.preventDefault();

});
});

$(function(){
	$("#CancelEditS4Btn" ).click(function( event ) {
		$("#edits4Form").hide();
	});
});

var myt = setTimeout("updateselects",2000);

function updateselects(){
clearTimeout(myt);

var Pristr = "";
	var Sub1str = "";
	var Sub2str = "";
	var Sub3str = "";
	var Sub4str = "";
	var s4 = "";
	
	$("select#primary_cat option:selected" ).each(function() {
		Pristr += $( this ).text();
    	});
	$("select#subcat1 option:selected" ).each(function() {
		Sub1str += $( this ).text();
    	});
	$("select#subcat2 option:selected" ).each(function() {
		Sub2str += $( this ).text();
    	});
	$("select#subcat3 option:selected" ).each(function() {
		Sub3str += $( this ).text();
    	});
	$("select#subcat4 option:selected" ).each(function() {
		Sub4str += $( this ).text();
    	});
//$("#gate option[value='Gateway 2']").attr('selected', 'selected');
    	
	$("select#primary_cat option[value='" + Pristr + "']").attr('selected','selected');
	$("select#primary_cat").change();
	$("select#primary_cat").show();
    
    	$("select#subcat1 option[value='" + Sub1str + "']").attr('selected','selected');
	$("select#subcat1").change();
	$("select#subcat1").show();	
	
	$("select#subcat2 option[value='" + Sub2str + "']").attr('selected','selected');
	$("select#subcat2").change();
	$("select#subcat2").show();

	$("select#subcat3 option[value='" + Sub3str + "']").attr('selected','selected');
	$("select#subcat3").change();
	$("select#subcat3").show();

	$("select#subcat4 option[value='" + Sub4str + "']").attr('selected','selected');
	$("select#subcat4").change();
	$("select#subcat4").show();	

pageloaded = true;
}

$(function(){
//	$("div#cancelbtn").click(function(){
//		var sku = "";
//		sku = $("input#sku").val();
		
//		window.location.href("edititem.php?sku="+sku);
//	});
});

$(function(){
	$("input#savebtn1").click(function(){
		var Pristr = "";
		var saveok = true;
		
		var Sub1str="";
		var Sub2str="";
		  $("select#primary_cat option:selected" ).each(function() {
	      Pristr += $( this ).text();
    		});
		  $("select#subcat1 option:selected" ).each(function() {
	      Sub1str += $( this ).text();
    		});
		  $("select#subcat2 option:selected" ).each(function() {
	      Sub2str += $( this ).text();
    		});
    		
    		if(catchanged == 1){
	    	if(Sub1str == "(Select Subcategory - Level 1)" || Sub1str == null || Sub1str==''){
	    		alert("Error: No subcategory 1 selected...REQUIRED");
    			saveok = false;
    		}
    		if(Sub2str == "(Select Subcategory - Level 2)" || Sub2str == null || Sub1str==''){
	    		if( confirm("Error: No subcategory 2 selected...OPTIONAL\n Add one now?") == false){
	    			saveok = true;
	    		}else{
	    			saveok = false;
	    		}
    			
    		}
    		}
    		if(saveok == true){
    			return window.editfrm.submit();
    		}else{
    			return false;
    		}
	});
});