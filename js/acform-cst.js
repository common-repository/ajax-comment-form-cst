/*
 * Ajax comment form validation script
 * Jquery plugin for email, text and comment field validation.
*/
 jQuery.extend(jQuery.fn, {
   /*
    * check if field value lenth more than 2 symbols for name and comment ) 
    */
   validate: function () {
    if(jQuery(this).val().length < 2) {
              jQuery(this).addClass('error');
              return false;
          }else{
              jQuery(this).removeClass('error');
              return true;
          }
   },
      
   /*
    * Vlidate checkbox field added in comment form
    *
    */
   validateCheckbox: function(){
      if(jQuery(this).prop("checked") == true){
          jQuery(this).removeClass('error');
          jQuery(this).closest('p').removeClass('error');
          return true;
      }else{
          jQuery(this).addClass('error');
          jQuery(this).closest('p').addClass('error');
          return false;
      }
   },
   
   scroller:function(afterScroll = false){
      // scroll to display error or success message.
      //console.log('firescroll'+afterScroll);
      jQuery('html, body').animate({scrollTop:jQuery(this).offset().top - 40}, 'slow','swing', function(){
         if(afterScroll){
             setTimeout(function(){
              jQuery('html, body').animate({scrollTop:jQuery('#'+afterScroll).offset().top - 40}, 'slow');
              }, 1000);
             
         }
      });
   },
   /*
    * check if email is correct
    * It will add .error class for incorrect / error creating field.
   */
      
   validateEmail: function () {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
        emailToValidate = jQuery(this).val();
    if (!emailReg.test( emailToValidate ) || emailToValidate == "") {
     jQuery(this).addClass('error');return false;
    } else {
     jQuery(this).removeClass('error');return true;
    }
   }     
});
 
jQuery(function($){
 
 $(document).on("click", "#cancel-comment-reply-link", function(){
    console.log("It works!");
});
 
	/*
	 * On comment form submit
	 */
	jQuery( '#commentform' ).on('submit',function(){
         
        // define some vars
        var button = jQuery('#submit'), // submit button
        commentform = jQuery(this),
        errorMessageText = acformcst.errorMessage,
        errorCaught = false,
        respond = jQuery('#respond'), // comment form container
        cancelreplylink = jQuery('#cancel-comment-reply-link'), 
        commentlist = jQuery('.comment-list'); // comment list container
		    
        if( commentform.find('#comment-status').length == 0 )
        commentform.prepend('<div id="comment-status" ></div>');

        var statusdiv=$('#comment-status');
        

        // if user is logged in, do not validate author and email fields        
        if( jQuery( '#author' ).length ){
            if(!jQuery( '#author' ).validate()){
                  errorCaught = true;                
            }
        } 
        if( jQuery( '#email' ).length ){
            if(!jQuery( '#email' ).validateEmail()){
                  errorCaught = true;                 
            }
        }
 
        // validate comment in any case
        if(!jQuery( '#comment' ).validate()){
            errorCaught = true;            
        }

        // validate checkbox / GDPR
        if(acformcst.captcha == 'true'){
         if(!jQuery( '#security_code' ).validate()){
             errorCaught = true;            
         }
        }
        
        // validate checkbox / GDPR
        if(acformcst.gdpr == 'true'){
         if(!jQuery( '#gdpr' ).validateCheckbox()){
             errorCaught = true;            
         }
        }
        
        if(errorCaught){
            statusdiv.html('<p class="ajax-error" >'+ errorMessageText+'</p>');
            commentform.scroller();
        }
 
        // if comment form isn't in process, submit it
        if ( !button.hasClass( 'loadingform' ) && !$( '#author' ).hasClass( 'error' ) && !$( '#email' ).hasClass( 'error' ) && !$( '#comment' ).hasClass( 'error' ) && !$( '#gdpr' ).hasClass( 'error' ) && !$( '#security_code' ).hasClass( 'error' )){
       
        // ajax request
        jQuery.ajax({
         type : 'POST',
         url : acformcst.ajaxurl, // admin-ajax.php URL
                     dataType: "json",
         data: jQuery(this).serialize() + '&action=acform_cst_post_comment', // send form data + action parameter
         beforeSend: function(xhr){
                  // what to do just after the form has been submitted                 
                  button.attr('disabled',true);
                  //Add a status message
                  statusdiv.html('<p class="ajax-placeholder">Processing...</p>');
         },
         error: function (request, status) {
          if( status == 500 ){
           alert( 'Error while adding comment' );
          } else if( status == 'timeout' ){
           alert('Error: Server doesn\'t respond.');
          } else {
           // process WordPress errors
           var wpErrorHtml = request.responseText.split("<p>"),
            wpErrorStr = wpErrorHtml[1].split("</p>");
      
           alert( wpErrorStr[0] );
          }
         },
         success: function ( jsonresponse ) {     
          if(jsonresponse.error){
               statusdiv.html('<p class="ajax-error" >'+jsonresponse.message+'</p>');
               // scroll to display error.
               commentform.scroller();             
               
          }else{
                addedCommentHTML = jsonresponse.html;
                scrollto = jQuery('#'+jsonresponse.moveto);
                // if this post already has comments
               if( commentlist.length > 0 ){           
                // if in reply to another comment
                //console.log(respond.parent());
                if( respond.parent().hasClass( 'comment' ) ){
           
                 // if the other replies exist
                 if( respond.parent().children( '.children' ).length ){	
                  respond.parent().children( '.children' ).append( addedCommentHTML );
                 } else {
                  // if no replies, add <ol class="children">
                  addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
                  respond.parent().append( addedCommentHTML );
                 }
                 // close respond form
                 cancelreplylink.trigger("click");
                } else {
                 // simple comment
                 commentlist.append( addedCommentHTML );
                }
               }else{
                // if no comments yet
                addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
                respond.before( $(addedCommentHTML) );
               }
               
               jQuery('#comment').val(''); // clear textarea field
               jQuery('input[type="text"], input[type="email"]').val('');
               statusdiv.html('<p class="ajax-success" >'+acformcst.successMessage+'</p>');
               if(acformcst.mscroll)
               commentform.scroller(jsonresponse.moveto);
               else
               commentform.scroller(); 
               // scroll to comment area.
               
          }      
        },
        complete: function(){
         // what to do after a comment has been added            
            button.removeAttr('disabled'); // enable submit button
        }
       });                    
  }
   
		return false;
	});
});
