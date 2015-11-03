 jQuery(function() {
    jQuery.validator.addMethod("time", "Please enter a valid time.");
	
    jQuery("#content1").validate({
    
        // Specify the validation rules

        rules: {
            event_title: "required",
            start_date: "required",
            to_date:"required",
            venue: "required",
            desc:"required", 
             traditional: "required"
        },
		
        
        // Specify the validation error messages
        messages: {
            event_title: "Please enter Event title",
            start_date:"Please enter Start Date ",
            to_date:"Please enter End Date",
            venue: "Please enter Event venue",
            traditional:"Please enter guest",
            desc:"Please enter Event Description"
        },
       errorPlacement: function(error, element) {
        error.appendTo(element.parent('.input-text'));
    },

        
        submitHandler: function(form) {
            form.submit();
        }
    });
  });
