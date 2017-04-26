jQuery(window).on('load', function(){
    if (!Mage.Cookies.get('hasPersistentBeenClosed'))
    {
        jQuery('#persistent-cart-window').modal();
    }

    jQuery('.ajaxlogin-login').click(function(event){
        event.preventDefault();
        if (currentPath != "/customer/account/login") {
            jQuery('#ajaxforgotModal').modal('hide');
            jQuery('#ajaxloginModal').modal();
        }
    });
    jQuery('.ajaxlogin-forgot').click(function(event){
        event.preventDefault();
        if (currentPath != "/customer/account/forgotpassword") {
            jQuery('#ajaxloginModal').modal('hide');
            jQuery('#ajaxforgotModal').modal();
        }
    });
    jQuery('.ajaxlogin-logout').click(function(event){
        event.preventDefault();
        jQuery('#ajaxlogoutModal').modal();
    });
    jQuery('.ajaxlogin-account').click(function(event){
        event.preventDefault();
        // close if on main account create page
        if (currentPath == "/customer/account/create") {
            jQuery("#ajaxloginModal").modal('toggle');
        }
        else {
            jQuery('#ajaxloginModal').modal('hide');
            jQuery('#ajaxcreateModal').modal();
        }
    });
    jQuery('#ajaxlogin-login-form') && jQuery('#ajaxlogin-login-form').on('submit', function(e) {
        e.preventDefault();

        if (!ajaxLoginForm.validator.validate()) {
            return false;
        }

        jQuery('#login-please-wait').show();
        jQuery('#send2').attr('disabled', 'disabled');
        jQuery('#ajaxlogin-login-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",0.5, function(){});

        jQuery.ajax({
            type: "POST",
            url: jQuery('#ajaxlogin-login-form').attr('action'),
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            data: jQuery('#ajaxlogin-login-form').serialize(),
            success: function(data, status, xhr) {
                var section = jQuery('#ajaxlogin-login-form');
                if (!section.length) {
                    return;
                }
                var ul = section.find('.messages').first();
                if (ul.length) {
                    ul.remove();
                }
                var response = data.evalJSON();
                if (response.error) {
                    var section = jQuery('#ajaxlogin-login-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-danger').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-danger"><i class="fa fa-lg fa-exclamation-triangle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-danger').first();
                    }
                    if (typeof response.error === "object" && response.error !== null) {
                        for (var key in response.error) {
                            if (response.error.hasOwnProperty(key)) {
                                jQuery(li).find('ul').first().append('<li>' + response.error[key] + '</li>');
                            }
                        }
                    } else {
                        jQuery(li).find('ul').first().append('<li>' + response.error + '</li>');
                    }
                    var captchaEl = jQuery('#user_login');
                    if (captchaEl.length) {
                        captchaEl.captcha.refresh(captchaEl.previous('img.captcha-reload'));
                        // try to focus input element:
                        var inputEl = jQuery('#captcha_' + id);
                        if (inputEl) {
                            inputEl.focus();
                        }
                    }
                } else if (response.message) {
                    var section = jQuery('#ajaxlogin-login-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-success').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-success"><i class="fa fa-lg fa-check-circle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-success').first();
                    }
                    jQuery(li).find('ul').first().append('<li>' + response.message + '</li>');
                }
                if (response.redirect) {
                    window.setTimeout(function(){
                        // Move to a new location or you can do something else
                        window.location.href = response.redirect;
                    }, 5000);
                    return;
                }
                jQuery('#login-please-wait').hide();
                jQuery('#send2').removeAttr('disabled');
                jQuery('#ajaxlogin-login-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",1, function(){});
            }
        });
    });

    jQuery('#ajaxlogin-forgot-password-form') && jQuery('#ajaxlogin-forgot-password-form').on('submit', function(e) {
        e.preventDefault();

        if (!ajaxForgotForm.validator.validate()) {
            return false;
        }

        jQuery('#forgot-please-wait').show();
        jQuery('#btn-forgot').attr('disabled', 'disabled');
        jQuery('#ajaxlogin-forgot-password-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",0.5, function(){});

        jQuery.ajax({
            type: "POST",
            url: jQuery('#ajaxlogin-forgot-password-form').attr('action'),
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            data: jQuery('#ajaxlogin-forgot-password-form').serialize(),
            success: function(data, status, xhr) {
                var section = jQuery('#ajaxlogin-forgot-password-form');
                if (!section.length) {
                    return;
                }
                var ul = section.find('.messages').first();
                if (ul.length) {
                    ul.remove();
                }

                jQuery('#forgot-please-wait').hide();
                jQuery('#btn-forgot').removeAttr('disabled');
                jQuery('#ajaxlogin-forgot-password-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",1, function(){});

                var response = data.evalJSON();

                if (response.error) {
                    var section = jQuery('#ajaxlogin-forgot-password-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-danger').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-danger"><i class="fa fa-lg fa-exclamation-triangle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-danger').first();
                    }
                    if (typeof response.error === "object" && response.error !== null) {
                        for (var key in response.error) {
                            if (response.error.hasOwnProperty(key)) {
                                jQuery(li).find('ul').first().append('<li>' + response.error[key] + '</li>');
                            }
                        }
                    } else {
                        jQuery(li).find('ul').first().append('<li>' + response.error + '</li>');
                    }
                    var captchaEl = jQuery('#user_forgotpassword');
                    if (captchaEl.length) {
                        captchaEl.captcha.refresh(captchaEl.previous('img.captcha-reload'));
                        // try to focus input element:
                        var inputEl = jQuery('#captcha_' + id);
                        if (inputEl) {
                            inputEl.focus();
                        }
                    }
                } else if (response.message) {
                    var section = jQuery('#ajaxlogin-forgot-password-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (ul.length) {
                        ul.remove();
                    }
                    var section = jQuery('#ajaxlogin-login-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-success').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-success"><i class="fa fa-lg fa-check-circle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-success').first();
                    }
                    jQuery(li).find('ul').first().append('<li>' + response.message + '</li>');
                    jQuery('#ajaxforgotModal').modal('hide');
                    jQuery('#ajaxloginModal').modal('show');
                }
            }
        });
    });

    jQuery('#ajaxlogin-logout-form') && jQuery('#ajaxlogin-logout-form').on('submit', function(e) {
        e.preventDefault();

        if (!ajaxLogoutForm.validator.validate()) {
            return false;
        }

        jQuery('#login-please-wait').show();
        jQuery('#send2').attr('disabled', 'disabled');
        jQuery('#ajaxlogin-logout-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",0.5, function(){});

        jQuery.ajax({
            type: "POST",
            url: jQuery('#ajaxlogin-logout-form').attr('action'),
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            data: jQuery('#ajaxlogin-logout-form').serialize(),
            success: function(data, status, xhr) {
                var section = jQuery('#ajaxlogin-logout-form');
                if (!section.length) {
                    return;
                }
                var ul = section.find('.messages').first();
                if (ul.length) {
                    ul.remove();
                }

                var response = data.evalJSON();
                if (response.error) {
                    var section = jQuery('#ajaxlogin-logout-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-danger').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-danger"><i class="fa fa-lg fa-exclamation-triangle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-danger').first();
                    }
                    jQuery(li).find('ul').first().append('<li>' + response.error + '</li>');
                }
                if (response.redirect) {
                    document.location = response.redirect;
                    return;
                }
                jQuery('#login-please-wait').hide();
                jQuery('#send2').removeAttr('disabled');
                jQuery('#ajaxlogin-logout-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",1, function(){});
            }
        });
    });

    jQuery('#ajaxlogin-create-form') && jQuery('#ajaxlogin-create-form').on('submit', function(e) {
        e.preventDefault();

        if (!ajaxCreateForm.validator.validate()) {
            return false;
        }

        jQuery('#create-please-wait').show();
        jQuery('#create').attr('disabled', 'disabled');
        jQuery('#ajaxlogin-create-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",0.5, function(){});

        jQuery.ajax({
            type: "POST",
            url: jQuery('#ajaxlogin-create-form').attr('action'),
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            data: jQuery('#ajaxlogin-create-form').serialize(),
            success: function(data, status, xhr) {
                var section = jQuery('#ajaxlogin-create-form');
                if (!section.length) {
                    return;
                }
                var ul = section.find('.messages').first();
                if (ul.length) {
                    ul.remove();
                }

                var response = data.evalJSON();
                if (response.error) {
                    var section = jQuery('#ajaxlogin-create-form');
                    if (!section.length) {
                        return;
                    }
                    var ul = section.find('.messages').first();
                    if (!ul.length) {
                        section.prepend('<ul class="messages list-unstyled"></ul>');
                        ul = section.find('.messages').first()
                    }
                    var li = jQuery(ul).find('.bg-danger').first();
                    if (!li.length) {
                        jQuery(ul).prepend('<li class="bg-danger"><i class="fa fa-lg fa-exclamation-triangle pull-left"></i><ul class="list-unstyled"></ul></li>');
                        li = jQuery(ul).find('.bg-danger').first();
                    }
                    if (typeof response.error === "object" && response.error !== null) {
                        for (var key in response.error) {
                            if (response.error.hasOwnProperty(key)) {
                                jQuery(li).find('ul').first().append('<li>' + response.error[key] + '</li>');
                            }
                        }
                    } else {
                        jQuery(li).find('ul').first().append('<li>' + response.error + '</li>');
                    }
                }
                if (response.redirect) {
                    document.location = response.redirect;
                    return;
                }
                jQuery('#create-please-wait').hide();
                jQuery('#create').removeAttr('disabled');
                jQuery('#ajaxlogin-create-form .buttons-set').first().toggleClass('disabled').fadeTo("slow",1, function(){});
            }
        });
    });
});