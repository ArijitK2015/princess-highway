// vendor 
var Discount = Class.create();
Discount.prototype = {
      initialize: function(form, saveUrl){
          this.form = form;
          if ($(this.form)) {
              $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
          }
          this.saveUrl = saveUrl;
          this.validator = new Validation(this.form);
          this.onSave = this.nextStep.bindAsEventListener(this);
          this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
      },

      validate: function() {
          if(!this.validator.validate()) {
              return false;
          }
          return true;
      },

      save: function(){

          if (checkout.loadWaiting!=false) return;
          if (this.validate()) {
              checkout.setLoadWaiting('discount');
              var request = new Ajax.Request(
                  this.saveUrl,
                  {
                      method:'post',
                      onComplete: this.onComplete,
                      onSuccess: this.onSave,
                      onFailure: checkout.ajaxFailure.bind(checkout),
                      parameters: Form.serialize(this.form)
                  }
              );
          }
      },

      resetLoadWaiting: function(transport){
          checkout.setLoadWaiting(false);
      },

      nextStep: function(transport){
    	  if (transport && transport.responseText){
              try{
                  response = eval('(' + transport.responseText + ')');
              }
              catch (e) {
                  response = {};
              }
          }
          if (response.error){
              if ((typeof response.message) == 'string') {
                  alert(response.message);
              } else {
                  if (window.shippingRegionUpdater) {
                      shippingRegionUpdater.update();
                  }
                  response.message.join("\n");
              }

              return false;
          }

          checkout.setStepResponse(response);
      }
}

function removeCoupon(url, gc)
{
    if (jQuery('.remove-'+gc).length) {
        jQuery('.remove-'+gc).removeClass('fa-times');
        jQuery('.remove-'+gc).addClass('fa-spinner');
        jQuery('.remove-'+gc).addClass('fa-spin');
    }
    new Ajax.Request(url, {
        method: 'post',
        onSuccess: function(transport) {
            $('discount-progress-opcheckout').update(transport.responseText);
        }
    });
}
