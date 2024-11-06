import '../styles/style.css';
import '../styles/sweetalert.css';
import '../styles/app.css';
import '../styles/frontend.css';


$("#filters").on('click',function(e){
    e.preventDefault();
    $(".filter-section").toggleClass('hide');
});

applyFilter($("#filter-transmission"));
applyFilter($("#filter-fuel"));
applyFilter($("#filter-seats"));
applyFilter($("#filter-doors"));
applyFilter($("#filter-brand"));
applyFilter($("#filter-price"));
applyFilter($("#filter-payment"));                                   

$("#filters-clear").on('click',function(e){
    e.preventDefault();    
    $("#filter-transmission").prop('selectedIndex',0);
    $("#filter-fuel").prop('selectedIndex',0);
    $("#filter-seats").prop('selectedIndex',0);
    $("#filter-doors").prop('selectedIndex',0);
    $("#filter-brand").prop('selectedIndex',0);
    $("#filter-price").prop('selectedIndex',0);
    $("#filter-deposit").prop('selectedIndex',0);
    $("#filter-payment").prop('selectedIndex',0);
     applyingFilter(true);
});	

function applyFilter(filter,clearFilters=false){
    filter.on('change',function(){ 
        applyingFilter(clearFilters)
     });    
}

function applyingFilter(clearFilters){
        $.ajax({
            url : "form/filter/cars",
            type: "POST",
            data :{
                filtertransmission :$("#filter-transmission").val(),
                filterfuel :$("#filter-fuel").val(),
                filterseats:$("#filter-seats").val(),
                filterdoors:$("#filter-doors").val(),
                filterbrand:$("#filter-brand").val(),
                filterprice:$("#filter-price").val(),
                filterdeposit:$("#filter-deposit").val(),
                filterpayment:$("#filter-payment").val(),
                location:$("#address").val(),
                clearFilters:clearFilters
            },
            dataType: "json",
            beforeSend: function() {                
                $(".display-vehicles").hide();
                $(".loading-vehicles").removeClass('hide'); 
            },
            success: function(data, textStatus, jqXHR){
                console.log(data);                                
                $(".loading-vehicles").addClass('hide');
                $(".display-vehicles").show();
                if(data.complete){
                    $(".vehicle-count").html(data.count);
                    $(".display-vehicles").html(data.html);
                }else{
                    $(".loading").hide();
                    swal("Oops",data.message, "error");
                }                    
            },
            error: function (jqXHR, textStatus, errorThrown){  
                console.log(textStatus);
                console.log(errorThrown);     
                console.log(jqXHR); 
                $(".loading").hide();    
                swal("Oops","Error processing form.please try again", "error");
            }
        });
}

$("#address").on('change',function(){
    filterWithLocation($(this).val());
})


function filterWithLocation(address){
    console.log(address);
    $.ajax({
        url : "/form/search/cars/location",
        type: "POST",
        data :{
            location :address
        },
        dataType: "json",
        beforeSend: function() {               
            $(".location-available-car-notification").html(""); 
            $(".spinner-grow").removeClass("hide");
        },
        success: function(data, textStatus, jqXHR){
            console.log(data);                                      
            $(".spinner-grow").addClass("hide");
            if(data.complete){
               var message ="";
                if(data.count!= 0){
                    message = data.count+" cars available" ; 
                }else{
                    message = "No cars within 60km radius of your location! Select a different address";
                }
                $(".location-available-car-notification").html(message);                

            }else{
               // $(".loading").hide();                
            }                    
        },
        error: function (jqXHR, textStatus, errorThrown){  
            console.log(textStatus);
            console.log(errorThrown);     
            console.log(jqXHR); 
            $(".loading").hide();    
            swal("Oops","Error processing form.please try again", "error");
        }
    });
}

function applyingFilterWithLocation(){
    var address = $("#address").val();
    console.log(address);
    $.ajax({
        url : "/form/filter/cars/location",
        type: "POST",
        data :{
            location :address
        },
        dataType: "json",
        beforeSend: function() {                
            $(".display-vehicles").hide();
            $(".loading-vehicles").removeClass('hide'); 
        },
        success: function(data, textStatus, jqXHR){
            console.log(data);                                
            $(".loading-vehicles").addClass('hide');
            $(".display-vehicles").show();
            if(data.complete){
                $(".vehicle-count").html(data.count);
                $(".display-vehicles").html(data.html);
                if(data.count==0){
                    swal("Oops","Sorry no cars nearby your selected address\n"+address, "error"); 
                }
            }else{
                $(".loading").hide();
                swal("Oops",data.message, "error");
            }                    
        },
        error: function (jqXHR, textStatus, errorThrown){  
            console.log(textStatus);
            console.log(errorThrown);     
            console.log(jqXHR); 
            $(".loading").hide();    
            swal("Oops","Error processing form.please try again", "error");
        }
    });
}




   
$('.digit-group').find('input').each(function() {
	$(this).attr('maxlength', 1);
	$(this).on('keyup', function(e) {
		var parent = $($(this).parent());
		
		if(e.keyCode === 8 || e.keyCode === 37) {
			var prev = parent.find('input#' + $(this).data('previous'));
			
			if(prev.length) {
				$(prev).select();
			}
		} else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
			var next = parent.find('input#' + $(this).data('next'));
			
			if(next.length) {
				$(next).select();
			} else {
				if(parent.data('autosubmit')) {
					parent.submit();
				}
			}
		}
	});
});


var address =  $("#address")[0];
var localityOptions = {
    types: ['geocode'], //['(cities)'], geocode to allow postal_code if you only want to allow cities then change this attribute
    componentRestrictions: {country: 'za'}
  };
    if(address){
        var autocomplete = new google.maps.places.Autocomplete(address, localityOptions);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
        });
    }
var dlocation =  $("#dlocation")[0];
    if(dlocation){
        var autocomplete = new google.maps.places.Autocomplete(dlocation, localityOptions);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
        });
    }
var profileAddress =  $("#profile_address")[0];   
    if(profileAddress){
        var autocomplete1 = new google.maps.places.Autocomplete(profileAddress, localityOptions);
        google.maps.event.addListener(autocomplete1, 'place_changed', function() {
            var place = autocomplete1.getPlace();
        });
    }

var city =  $("#profile_location")[0];   
    if(city){
        var options = {
            types: ['(cities)'],
            componentRestrictions: {country: "za"}
           };
        var autocomplete1 = new google.maps.places.Autocomplete(city,options);
        google.maps.event.addListener(autocomplete1, 'place_changed', function() {
            var place = autocomplete1.getPlace();
        });
    }  
    
$('#pickSameAsDrop').on('click',function() {
    if(!$(this).is(':checked')){
            $("#dlocation-form").removeClass('hide');
        }

        if($(this).is(':checked')){
            $("#dlocation-form").addClass('hide');
        }
    });
   

$('.table').DataTable();

//Other pages
imagePreview($("#listfile"),$('.list-image'));  

var sections = ["location","cars","documents","invoice","payments"];	
	var current = $("#current").val();
    //if(current){ 
	var current_fs, next_fs, previous_fs; //fieldsets
	var opacity;
	var done = false;
	//Intialize current section
	if(current==""){
        setCurrentPage(sections[0]);
    current = sections[0];}
	 var currentSection = $("."+current);
	 var pos = sections.indexOf(current);
	 $.each(sections, function (i) {
			var item = sections[i];
			$('#'+item).addClass("active");
			if(i == pos){return false;}
		});
	//Intialize btns
	$.each(sections, function (i) {	
			$('.'+sections[i]+'-btn').on('click',function(e){
				if(sections.indexOf(sections[i])<=pos){			
					$.each(sections, function (i) {
							var item = sections[i];
							$('.'+item).hide();
							if(i == pos){return false;}
						});			
					$("."+sections[i]).show();
				}
			});
		});
	 currentSection.css({
                'display': 'block',
                'position': 'relative'
            });

        $(".next").on('click',function(){    
            current_fs = $(this).parent();
            next_fs = $(this).parent().next(); 
            var goToNext = true;
            if(sections[$("fieldset").index(current_fs)] == "location"){
                    if($("#address").val().length==0 || $("#pickupdate").val().length==0 || $("#returndate").val().length==0){
                        swal("Oops","All field must be completed.Please try again", "error");
                        goToNext = false;                                          
                        setCurrentPage("location");               
                    }
            }
            if(sections[$("fieldset").index(current_fs)] == "cars"){
                if($("#selected-vehicle").val()){
                    if($("#selected-vehicle").val().length==0){
                    $("#error-message").removeClass('hide').html("Select a vehicle.Please try again");
                        goToNext = false;                            
                            setCurrentPage("cars");
                    }
                }else{
                    goToNext = false;                            
                            setCurrentPage("cars");
                }
            }
            if(sections[$("fieldset").index(current_fs)] == "payments"){  
                var paymentType = $('#payment-type').val();
                setCurrentPage("payments");
                goToNext = false;     
                        $.ajax({
                            url : "form/process/payment",
                            type: "POST",
                            data : $("#msform").serialize(),
                            dataType: "json",
                            beforeSend: function() {
                                $(".loading").show(); 
                            },
                            success: function(data, textStatus, jqXHR){
                                console.log(data);
                                if(data.complete){
                                    swal("Success", data.message, "success");
                                    window.location.href = "/dashboard/rentals";
                                }else{
                                    $(".loading").hide();
                                    swal("Oops",data.message, "error");
                                }                    
                            },
                            error: function (jqXHR, textStatus, errorThrown){  
                                console.log(textStatus);
                                console.log(errorThrown);     
                                console.log(jqXHR); 
                                $(".loading").hide();    
                                swal("Oops","Error processing form.please try again", "error");
                            }
                        });
            }
            if(sections[$("fieldset").index(next_fs)] == "invoice"){
                console.log("invoice");
                setCurrentPage("invoice");
                        $.ajax({
                            url : "form/process",
                            type: "POST",
                            data : $("#msform").serialize(),
                            beforeSend: function() {
                                $(".generate-statement").removeClass("hide"); 
                            },                            
                            dataType: "json",
                            success: function(data, textStatus, jqXHR)
                            {
                                if(data.complete){
                                    if(data.id!=''){
                                        $("#id").val(data.id);
                                    }
                                    $(".generate-statement").addClass("hide");
                                    $(".statement").html(data.html);
                                }else{
                                    swal("Oops",data.message, "error");
                                    if(data.type=="location"){                            
                                        setCurrentPage("location");
                                        location.reload();
                                    }
                                    if(data.type=="cars"){                            
                                        setCurrentPage("cars");
                                        location.reload();
                                    }
                                    $(".generate-statement").addClass("hide"); 
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown)
                            {   
                                //formButton.removeAttr("disabled");  
                                console.log(textStatus);
                                console.log(errorThrown);     
                                console.log(jqXHR);
                                $(".loading").hide();     
                                swal("Oops",textStatus, "error");
                            }
                        });
            }
            if(sections[$("fieldset").index(next_fs)] == "cars"){
                console.log("Filtering cars with location select by user");
                applyingFilterWithLocation();                     
            }            
            if(sections[$("fieldset").index(next_fs)] == "payments"){
                console.log("payments");
                setCurrentPage("payments");
                        $.ajax({
                            url : "form/process/totals",
                            type: "POST",
                            data : $("#msform").serialize(),
                            beforeSend: function() {
                                $(".generate-totals").removeClass("hide"); 
                            },                            
                            dataType: "json",
                            success: function(data, textStatus, jqXHR)
                            {
                                if(data.complete){
                                    if(data.id!=''){
                                        $("#id").val(data.id);
                                    }
                                    $(".generate-totals").addClass("hide");
                                    $(".totals").html(data.html);
                                }else{
                                    swal("Oops",data.message, "error");
                                    if(data.type=="location"){                            
                                        setCurrentPage("location");
                                        location.reload();
                                    }
                                    if(data.type=="cars"){                            
                                        setCurrentPage("cars");
                                        location.reload();
                                    }
                                    $(".generate-statement").addClass("hide"); 
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown)
                            {   
                                //formButton.removeAttr("disabled");  
                                console.log(textStatus);
                                console.log(errorThrown);     
                                console.log(jqXHR);
                                $(".loading").hide();     
                                swal("Oops",textStatus, "error");
                            }
                        });
            }
            if(goToNext){
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                next_fs.show(); 
                current_fs.animate({opacity: 0}, {
                    step: function(now) {
                        opacity = 1 - now;
                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        next_fs.css({'opacity': opacity});
                    }, 
                    duration: 600
                });
                setCurrentPage(sections[$("fieldset").index(next_fs)]);
            }
        });

        function setCurrentPage(page){
            $.ajax({
                        url : "form/current/page",
                        type: "POST",
                        data :"current="+page,
                        dataType: "json",
                        success: function(data, textStatus, jqXHR)
                        {          
                            console.log(data);
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {   
                            console.log(textStatus);
                            console.log(errorThrown);     
                            console.log(jqXHR);     
                        }
                    });
        }

        $(".previous").on("click",function(){    
            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev(); 
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active"); 
            previous_fs.show();
            current_fs.animate({opacity: 0}, {
                step: function(now) {
                    opacity = 1 - now;
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({'opacity': opacity});
                }, 
                duration: 600
            });
        });
   // }
    
function imagePreview(fileInput,preview)
    {
        fileInput.change(function () {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    }








