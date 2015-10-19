 jQuery(function() {
      //global variables
      var form = jQuery("#content1");
      //Values
      var name = jQuery("#eventtitle"); //textbox u are going to validate
      
      
      //Span tags to display error messages
      var nameInfo = jQuery("#titleerror");
      
      //first validation on form submit
      form.submit(function() 
      {
            // validation begin before submit
            if (validateName()) 
            {
                  return true;
            } 
            else 
            { 
                  return false; 
            }
      });
            //declare name validation function
      function validateName() {
      //validation for empty
            if (name.val() == "") {
                  name.addClass("error");
                  nameInfo.text("Names cannot be empty!");
                  nameInfo.addClass("error");
                  return false;
            } else if (name.val().length < 2) {
                  name.addClass("error");
                  nameInfo.text("Names with more than 2 letters!");
                  nameInfo.addClass("error");
                  return false;
            }
            //if it's valid
            else {
                  name.addClass("");
                  nameInfo.text("");
                  nameInfo.addClass("");
                  return true;
            }
            
      }
	
    

  });