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
          return this.validator.validate();
      },

      save: function(){

          if (checkout.loadWaiting!==false) return;
          var validator = new Validation(this.form);
          if (validator.validate()) {
              checkout.setLoadWaiting('discount');
              new Ajax.Request(
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

      resetLoadWaiting: function(){
          checkout.setLoadWaiting(false);
      },

      nextStep: function(transport){
          var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};

          if (response.error){
              if (Object.isString(response.message)) {
                  alert(response.message.stripTags().toString());
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
};

function removeCoupon(url, gc)
{
    var remove_gc = jQuery('.remove-'+gc);
    if (remove_gc.length) {
        remove_gc.removeClass('fa-times').addClass('fa-spinner fa-spin');
    }
    new Ajax.Request(url, {
        method: 'post',
        onSuccess: function(transport) {
            $('discount-progress-opcheckout').update(transport.responseText);
        }
    });
}
Checkout.prototype.parent_initialize = Checkout.prototype.initialize;
Checkout.prototype.initialize = function(accordion, urls) {
    this.parent_initialize(accordion, urls);
    this.steps = ['login', 'billing', 'shipping', 'discount', 'shipping_method', 'payment', 'review'];
};
