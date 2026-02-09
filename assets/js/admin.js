jQuery(document).ready(function(){

    // Auth method toggle
    function toggleAuthFields() {
        var authType = jQuery('input[name="cf_auth_type"]:checked').val() || 'global_key';
        jQuery('.auth-group-global_key').toggle(authType === 'global_key');
        jQuery('.auth-group-api_token').toggle(authType === 'api_token');
    }
    jQuery('input[name="cf_auth_type"]').on('change', toggleAuthFields);
    toggleAuthFields();

    // Change Credentials toggle
    jQuery('.woocf-toggle-credentials').on('click', function(e) {
        e.preventDefault();
        var $form = jQuery('.woocf-credentials-form');
        var $link = jQuery(this);
        if ($form.is(':visible')) {
            $form.slideUp(200);
            $link.text(woocf_i18n.change_credentials);
        } else {
            $form.slideDown(200, function() {
                toggleAuthFields();
            });
            $link.text(woocf_i18n.cancel);
        }
    });

    // Disconnect — clear credential fields and submit
    jQuery('.woocf-disconnect').on('click', function(e) {
        e.preventDefault();
        if (!confirm(woocf_i18n.disconnect_confirm)) return;
        jQuery('#cf_email').val('');
        jQuery('#cf_key').val('');
        jQuery('#cf_token').val('');
        jQuery('form[name="woocf_settings"]').find('#submit').click();
    });

    // Clear log AJAX request.
    jQuery("#clear_log_button").on('click', function(e){

        e.preventDefault();

        var $response = jQuery("#log_screen").find("pre");
        $response.text(woocf_i18n.clearing_log);

        var data = {
            action: 'woocf_clearlog',
            clear_log: true
        };
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            success: function( data ){
                data = JSON.parse(data);

                // If the request was successful but the API returned an error.
                if( data.errors && data.errors.length > 0 ) {
                    $response.text(woocf_i18n.error);
                    return;
                }

                // Clear log on front end.
                $response.text("");

                $response.text(woocf_i18n.log_cleared);
                setTimeout(function(){
                    $response.text("");
                }, 1500);
            },
            error: function(errorThrown){
                console.log('There was an error');
                console.log(errorThrown);
            }
        });
    });

    // View log AJAX request.
    jQuery(".log_tab").on('click', function(e){
        e.preventDefault();

        var $response = jQuery("#log_screen").find("pre");
        $response.text(woocf_i18n.loading_log);

        var data = {
            action: 'woocf_loadlog',
            load_log: true
        };
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            success: function( data ){
                data = JSON.parse(data);

                // If the request was successful but the API returned an error.
                if( data.errors && data.errors.length > 0 ) {
                    $response.text(woocf_i18n.error);
                    return;
                }

                // Load log on front end.
                $response.text(data.log);
            },
            error: function(errorThrown){
                console.log('There was an error');
                console.log(errorThrown);
            }
        });
    });

    // Tabs
    jQuery('.tab-panel').fadeIn();
    jQuery('.tabgroup > div').hide();
    jQuery('.tabgroup > div:first-of-type').show();
    jQuery('.tabs a').click(function(e){
        e.preventDefault();
        var $this = jQuery(this),
            tabgroup = '#'+$this.parents('.tabs').data('tabgroup'),
            others = $this.closest('li').siblings().children('a'),
            target = $this.attr('href');
        others.removeClass('active');
        $this.addClass('active');
        jQuery(tabgroup).children('div').hide();
        jQuery(target).show();
    });

});
