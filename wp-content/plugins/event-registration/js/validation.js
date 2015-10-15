 jQuery(function() {
  
    jQuery.validator.addMethod("time", function(value, element) {  
	return this.optional(element) || /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(value);  
	}, "Please enter a valid time.");
	
    jQuery("#regform").validate({
    
        // Specify the validation rules
		
        rules: {
			traditional: "required",
			start_time: "required time",
            		event_title: "required",
            		venue: "required",
            		desc:"required", 
			start_date: "required",
			to_date:"required",
        },
		
        
        // Specify the validation error messages
        messages: {
            event_title: "Please enter Event title",
            venue: "Please enter Event venue",
            desc:"Please enter Event Description",
			start_date:"Please enter Start Date",
			to_date:"Please enter End Date",
			traditional:"Please enter guest",
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });

  });
 