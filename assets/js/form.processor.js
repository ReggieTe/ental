$(function () {    
        
    
    //Process Forms  
    processForm($("#process-form"),$("#submit-form"));
    //Process Forms  
    processForm($("#process-form-1"),$("#submit-form-1"));
    //Process Forms  
    processForm($("#process-form-0"),$("#submit-form-0"));
    //process-form-upload
    uploadFile($("#upload-photo"));
    //process-form-upload
    uploadFile($("#upload-photo-1"));
    //process-form-upload
    uploadFile($("#upload-document"));
    //delete links
    deleteLink($("#delete"));
    //preview Images
    imagePreview($("#file"),$('.profile-dp'));
    ////preview Images
    imagePreview($("#galleryfile"),$('.gallery-photo'));
    //Other pages
    imagePreview($("#listfile"),$('.list-image'));  
    
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
    function deleteLink(link)
    {
        link.click(function(e){  
            e.preventDefault();  
            var deleteLink = $(this).attr("href")   
            swal({
                title: "Are you sure?",
                text: "This action can not be undone",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#4611a7",
                confirmButtonText: "Yes, delete it!"
            },
            function(){            
                window.location.href= deleteLink; 
            });
        });
    }    

    function processForm(submittedForm,formButton,log=false) {
        //enable log=true for debugging   
        formButton.click(function () { 
            var dataBundle = submittedForm.serialize()+"&&mode=app";  
            $(".status-process").removeClass("hide");
            $.ajax({
                url : submittedForm.attr("action"),
                type: "POST",
                data : dataBundle,
                dataType: "json",
                success: function(data, textStatus, jqXHR)
                {
                    $(".status-process").addClass("hide");              
                    if (log) { console.log(data);/*logging reply*/ }                    
                    if (!data.error)
                        {              
                            swal("Success", data.message, "success");
                            if (data.reset){ submittedForm[0].reset();}
                            if(data.redirect){ window.location.href=data.redirect;}
                        } else
                        {
                            swal("Oops",data.message, "error");
                        }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {   
                    formButton.removeAttr("disabled");  
                    console.log(textStatus);
                    console.log(errorThrown);     
                    console.log(jqXHR);     
                    swal("Oops","Error processing form.please try again", "error");
                }
            });

        });
    }
    
    function uploadFile(submittedForm)
    {       
        submittedForm.on('submit',(function(e) {
            $("div.statusprocess").toggleClass("hide");
            e.preventDefault();
            $.ajax({
                url : submittedForm.attr("action"),
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                    cache: false,
                processData:false,
                beforeSend : function() { },
                success: function(data)
                    {
                        $("div.statusprocess").toggleClass("hide");
                        if (!data.error)
                        {              
                            swal("Success", data.message, "success");
                            if(data.redirect){ window.location.href=data.redirect;}
                        } else
                        {
                            swal("Oops",data.message, "error");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {   console.log(textStatus);
                        console.log(errorThrown);     
                        console.log(jqXHR);     
                        swal("Oops","Error processing form.please try again", "error");
                    }        
              });
           }));
    }

});


function deleteImage(action,image,list,page){ 
            var dataBundle = "mode=app&&image="+image+"&&list="+list+"&&page="+page;  
            $.ajax({
                url : action,
                type: "POST",
                data : dataBundle,
                dataType: "json",
                success: function(data, textStatus, jqXHR)
                {                            
                    if (!data.error)
                    {              
                         swal("Success", data.message, "success");
                        if(data.redirect){ window.location.href=data.redirect;}
                    } else
                    {
                        swal("Oops",data.message, "error");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {        
                    swal("Oops","Error processing form.please try again", "error");
                }
            });   

}

