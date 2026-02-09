<?php
$siteSettings = new WOOCF_SiteSettings();
$woocf_settings = $siteSettings->settings;
$credentials = $siteSettings->areCredentialsSet();

// The plugin settings. Add new sections/fields accordingly.
$settings = array(
	'Cloudflare Credentials' => array(
        'fields' => array(
            array(
                'name' => 'cf_auth_type',
                'type' => 'radio',
                'title' => 'Authentication Method',
                'description' => '',
                'options' => array(
                    'global_key' => 'Global API Key',
                    'api_token'  => 'API Token (Recommended)',
                ),
                'value' => isset($woocf_settings['cf_auth_type']) ? $woocf_settings['cf_auth_type'] : 'global_key'
            ),
            array(
                'name' => 'cf_email',
                'type' => 'text',
                'title' => 'Email Address',
                'description' => 'Your Cloudflare account email address.',
                'value' => ($credentials && array_key_exists('cf_email', $credentials))? $credentials['cf_email'] : '',
                'auth_group' => 'global_key'
            ),
            array(
                'name' => 'cf_key',
                'type' => 'text',
                'title' => 'Global API Key',
                'description' => 'Your <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">Global API Key</a>.<br />Found under My Profile &rarr; API Tokens &rarr; Global API Key &rarr; View.',
                'value' => ($credentials && array_key_exists('cf_key', $credentials)) ? $credentials['cf_key'] : '',
                'auth_group' => 'global_key'
            ),
            array(
                'name' => 'cf_token',
                'type' => 'text',
                'title' => 'API Token',
                'description' => 'A <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">scoped API token</a>.<br />Create one under My Profile &rarr; API Tokens &rarr; Create Token.',
                'value' => isset($woocf_settings['cf_token']) ? $woocf_settings['cf_token'] : '',
                'auth_group' => 'api_token'
            ),
        )
	),
    'Clear Cache' => array(
        'fields' => array(
            array(
                'name' => 'after_scheduled_sale',
                'type' => 'checkbox',
                'title' => 'After Scheduled Sale?',
                'description' => 'Set scheduled sales on product edit pages by clicking \'Schedule\' next to the \'Sale price\' field.'
            ),
            array(
                'name' => 'when_product_out_of_stock',
                'type' => 'checkbox',
                'title' => 'When Product Goes Out of Stock?',
                'description' => 'Triggered when a product sells out or when the stock status is toggled to \'Out of Stock\' by an admin.'
            ),
            array(
                'name' => 'when_store_notice_updated',
                'type' => 'checkbox',
                'title' => 'When Store Notice is Updated?',
                'description' => 'Found in <a href="'.site_url('/wp-admin/customize.php?autofocus[section]=woocommerce_store_notice&return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwoocf-menu').'" target="_blank">Customize &rarr; WooCommerce &rarr; Store Notice</a>.'
            ),
            array(
                'name' => 'enable_logging',
                'type' => 'checkbox',
                'title' => 'Enable Logging?',
                'description' => ''
            )
        )
    ),
);

?>
<ul class="tabs clearfix" data-tabgroup="first-tab-group">
    <li><a href="#tab1" class="active">Settings</a></li>
    <li><a href="#tab2" class="log_tab">Log</a></li>
</ul>
<section id="first-tab-group" class="tabgroup">
    <div class="tab-panel" id="tab1">
        <form name="woocf_settings" method="post" action="">
            <table>
                <tbody>
                    <?php
                    foreach( $settings as $section=>$fields ){
                        /**
                        * Only display full form if credentials are set
                        */
                        if($section=='Cloudflare Credentials' || ($section!=='Cloudflare Credentials' && $credentials)) {
                            ?>
                            <tr class="tr-section-title">
                                <td><h2 class="cf-heading cf-heading--2"><?php echo esc_html($section); ?></h2></td>
                                <?php
                                if($section=='Cloudflare Credentials' && !$credentials){
                                    echo '<span class="cf-card__footer_message">Please manually set credentials below or install and configure the <a href="http://wordpress.org/plugins/cloudflare/" target="_blank">official Cloudflare plugin</a> to continue.</span>';
                                }
                                ?>
                            </tr>

                            <?php
                            // If Cloudflare credentials are filled out from Cloudflare plugin
                            if($section=='Cloudflare Credentials' && $this->isCloudflarePluginActive() && ($credentials && $credentials['source']=='cf_plugin')) {
                                $cloudflare_admin_url = admin_url()."options-general.php?page=cloudflare";
                                ?>
                                <tr>
                                    <td>Credentials imported from <a href="<?php echo esc_url($cloudflare_admin_url); ?>">Cloudflare</a> plugin.</td>
                                </tr>
                                <?php continue;
                            }


                            foreach ($fields['fields'] as $field => $data) {
                                if ($data['type'] == 'textarea') {
                                    ?>
                                    <tr>
                                        <td><h3 class="cf-card__title"><label
                                                    for="<?php echo esc_attr($data['name']); ?>"><?php echo esc_html($data['title']); ?>
                                                </label></h3>
                                            <?php
                                            if (isset($data['description']) && $data['description']) {
                                                ?>
                                                <span
                                                    class="cf-card__footer_message"><?php echo $data['description']; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td><textarea id="<?php echo esc_attr($data['name']); ?>"
                                                      name="<?php echo esc_attr($data['name']); ?>" rows="4"
                                                      cols="50"><?php echo esc_textarea(array_key_exists($data['name'], $woocf_settings) ? $woocf_settings[$data['name']] : ''); ?></textarea>
                                        </td>
                                    </tr>
                                    <?php
                                } else if ($data['type'] == 'checkbox') {
                                    ?>
                                    <tr>
                                        <td><?php if($data['name']=='enable_logging'){echo '<hr />';} ?><h3 class="cf-card__title"><label
                                                    for="<?php echo esc_attr($data['name']); ?>"><?php echo esc_html($data['title']); ?>
                                                </label></h3>
                                            <?php
                                            if (isset($data['description']) && $data['description']) {
                                                ?>
                                                <span
                                                    class="cf-card__footer_message"><?php echo $data['description']; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td><input id="<?php echo esc_attr($data['name']); ?>" type="<?php echo esc_attr($data['type']); ?>"
                                                   name="<?php echo esc_attr($data['name']); ?>" <?php echo(array_key_exists($data['name'], $woocf_settings) && $woocf_settings[$data['name']] == 'on' ? 'checked' : ''); ?>>
                                        </td>
                                    </tr>
                                    <?php
                                } else if ($data['type'] == 'span') {
                                    ?>
                                    <tr>
                                        <td><h3 class="cf-card__title"><label
                                                    for="<?php echo esc_attr($data['name']); ?>"><?php echo esc_html($data['title']); ?>
                                                </label></h3>
                                            <?php
                                            if (isset($data['description']) && $data['description']) {
                                                ?>
                                                <span
                                                    class="cf-card__footer_message"><?php echo $data['description']; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td><span
                                                id="<?php echo esc_attr($data['name']); ?>"><?php echo esc_html(array_key_exists($data['name'], $woocf_settings) && $woocf_settings[$data['name']] ? $woocf_settings[$data['name']] : ($data['value'] ?: '')); ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                } else if ($data['type'] == 'radio') {
                                    ?>
                                    <tr>
                                        <td colspan="2"><h3 class="cf-card__title"><?php echo esc_html($data['title']); ?></h3>
                                            <?php foreach ($data['options'] as $val => $label) { ?>
                                                <label style="margin-right: 20px;">
                                                    <input type="radio"
                                                           name="<?php echo esc_attr($data['name']); ?>"
                                                           value="<?php echo esc_attr($val); ?>"
                                                           <?php checked($data['value'], $val); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </label>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    ?>
                                    <tr<?php if (isset($data['auth_group'])) echo ' class="auth-group-' . esc_attr($data['auth_group']) . '"'; ?>>
                                        <td colspan="2"><h3 class="cf-card__title"><label
                                                    for="<?php echo esc_attr($data['name']); ?>"><?php echo esc_html($data['title']); ?>
                                                </label></h3>
                                            <?php
                                            if (isset($data['description']) && $data['description']) {
                                                ?>
                                                <span
                                                    class="cf-card__footer_message"><?php echo $data['description']; ?></span>
                                                <?php
                                            }
                                            ?>
                                            <br />
                                            <input id="<?php echo esc_attr($data['name']); ?>" type="<?php echo esc_attr($data['type']); ?>"
                                                   name="<?php echo esc_attr($data['name']); ?>"
                                                   value="<?php echo esc_attr(array_key_exists($data['name'], $woocf_settings) && $woocf_settings[$data['name']] ? $woocf_settings[$data['name']] : ($data['value'] ?: '')); ?>"
                                                   style="width: 100%; max-width: 400px;">
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php wp_nonce_field('woocf_settings_nonce', 'woocf_settings_nonce'); ?>
            <?php submit_button('Save Changes', 'cf-btn cf-btn--primary'); ?>
        </form>
    </div>
    <div class="tab-panel" id="tab2">
        <h1 class="cf-heading cf-heading--1"><span>Log</span></h1>
        <?php
        if(!$siteSettings->isLoggingEnabled()) {
            ?>
        <span class="cf-card__footer_message">Logging is currently disabled. To start logging, enable checkbox on settings tab.</span>
            <?php
        }
            ?>
        <div id="log_screen">
            <pre></pre>
        </div>
        <button id="clear_log_button" class="button button-primary cf-btn cf-btn--primary">Clear Log</button>
        <div id="woocf_ajax_res_clearlog"></div>
    </div>
</section>
<?php
