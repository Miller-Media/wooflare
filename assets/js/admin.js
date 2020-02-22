jQuery(document).ready(function(){

    var $ubSelect = jQuery('select[name="blacklisted_ips"]');
    $ubSelect.after("<button id='woocf_ajax_btn' class='woocf_ajax_btn cf-btn cf-btn--primary' name='woocf_ajax_btn'>Remove From Blacklist</button><div id='woocf_ajax_res_unblacklist'></div>");

    // Clear blacklisted IP AJAX request.
    jQuery("#woocf_ajax_btn").on('click', function(e){

        e.preventDefault();

        var ip_address = $ubSelect.val();
        var $response = jQuery("#woocf_ajax_res_unblacklist");

        $response.text('Removing IP from blacklist...');

        var data = {
            action: 'woocf_unblacklist_ip',
            ip_address: ip_address
        };
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            success: function( data ){
                data = JSON.parse(data);

                // If the request was successful but the API returned an error.
                if( data.errors && data.errors.length > 0 ) {
                    $response.removeClass().addClass('notice notice-error').text("There was an error.");
                    console.log(data);
                    return;
                } else if( data.result == 'success' ) {
                    // Remove IP Address from select dropdown.
                    jQuery('select[name="blacklisted_ips"] option[value="'+ip_address+'"]').remove();

                    // Check if this was the last item removed from the list
                    if(jQuery('select[name="blacklisted_ips"] option').length == 0){
                        that = jQuery('select[name="blacklisted_ips"]').closest('td');
                        jQuery('select[name="blacklisted_ips"]').remove();
                        jQuery('button#woocf_ajax_btn').remove();
                        that.prepend('<span id="blacklisted_ips">No IPs currently blacklisted.</span>');
                        $response.removeClass().addClass('notice notice-success').text("IP address successfully removed from blacklist.");
                    }
                } else {
                    $response.removeClass().addClass('notice notice-warning').text("Response from Cloudflare API unclear. Please refresh page.");
                }

                console.log(data);
            },
            error: function(errorThrown){
                console.log('there was an error');
                console.log(errorThrown);
            }
        });
    });

    // Clear log AJAX request.
    jQuery("#clear_log_button").on('click', function(e){

        e.preventDefault();

        var $response = jQuery("#log_screen").find("pre");
        $response.text('Clearing log...');

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
                    $response.text("There was an error.");
                    return;
                }

                // Clear log on front end.
                $response.text("");

                $response.text("Log cleared.");
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
        $response.text('Loading log...');

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
                    $response.text("There was an error.");
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