<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$siteSettings = new WOOCF_SiteSettings();
$woocf_settings = $siteSettings->settings;
$credentials = $siteSettings->areCredentialsSet();

// The plugin settings. Add new sections/fields accordingly.
$settings = array(
	__('Cloudflare Credentials', 'wooflare') => array(
        'fields' => array(
            array(
                'name' => 'cf_auth_type',
                'type' => 'radio',
                'title' => __('Authentication Method', 'wooflare'),
                'description' => '',
                'options' => array(
                    'api_token'  => __('API Token (Recommended)', 'wooflare'),
                    'global_key' => __('Global API Key (Legacy)', 'wooflare'),
                ),
                'value' => isset($woocf_settings['cf_auth_type']) ? $woocf_settings['cf_auth_type'] : ($credentials ? 'global_key' : 'api_token')
            ),
            array(
                'name' => 'cf_email',
                'type' => 'text',
                'title' => __('Email Address', 'wooflare'),
                'description' => __('Your Cloudflare account email address.', 'wooflare'),
                'value' => ($credentials && array_key_exists('cf_email', $credentials))? $credentials['cf_email'] : '',
                'auth_group' => 'global_key'
            ),
            array(
                'name' => 'cf_key',
                'type' => 'text',
                'title' => __('Global API Key', 'wooflare'),
                'description' => __('Your <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">Global API Key</a>.<br />Found under My Profile &rarr; API Tokens &rarr; Global API Key &rarr; View.', 'wooflare'),
                'value' => ($credentials && array_key_exists('cf_key', $credentials)) ? $credentials['cf_key'] : '',
                'auth_group' => 'global_key'
            ),
            array(
                'name' => 'cf_token',
                'type' => 'text',
                'title' => __('API Token', 'wooflare'),
                'description' => __('A <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">scoped API token</a>.<br />Create one under My Profile &rarr; API Tokens &rarr; Create Token.<br />Required permissions: <strong>Zone &rarr; Zone &rarr; Read</strong>, <strong>Zone &rarr; Cache Purge &rarr; Purge</strong>.', 'wooflare'),
                'value' => isset($woocf_settings['cf_token']) ? $woocf_settings['cf_token'] : '',
                'auth_group' => 'api_token'
            ),
        )
	),
    __('Clear Cache', 'wooflare') => array(
        'fields' => array(
            array(
                'name' => 'after_scheduled_sale',
                'type' => 'checkbox',
                'title' => __('After Scheduled Sale?', 'wooflare'),
                'description' => __('Set scheduled sales on product edit pages by clicking \'Schedule\' next to the \'Sale price\' field.', 'wooflare')
            ),
            array(
                'name' => 'when_product_out_of_stock',
                'type' => 'checkbox',
                'title' => __('When Product Goes Out of Stock?', 'wooflare'),
                'description' => __('Triggered when a product sells out or when the stock status is toggled to \'Out of Stock\' by an admin.', 'wooflare')
            ),
            array(
                'name' => 'when_store_notice_updated',
                'type' => 'checkbox',
                'title' => __('When Store Notice is Updated?', 'wooflare'),
                'description' => sprintf(__('Found in <a href="%s" target="_blank">Customize &rarr; WooCommerce &rarr; Store Notice</a>.', 'wooflare'), site_url('/wp-admin/customize.php?autofocus[section]=woocommerce_store_notice&return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dwoocf-menu'))
            ),
            array(
                'name' => 'enable_logging',
                'type' => 'checkbox',
                'title' => __('Enable Logging?', 'wooflare'),
                'description' => ''
            ),
            array(
                'name' => 'delete_data_on_uninstall',
                'type' => 'checkbox',
                'title' => __('Remove all plugin data when deleted', 'wooflare'),
                'description' => __('Check this box if you want all plugin settings and data to be removed when the plugin is deleted.', 'wooflare')
            )
        )
    ),
);

/**
 * Render a single field row.
 */
function woocf_render_field($data, $woocf_settings) {
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
                        class="cf-card__footer_message"><?php echo wp_kses_post( $data['description'] ); ?></span>
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
                        class="cf-card__footer_message"><?php echo wp_kses_post( $data['description'] ); ?></span>
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
                        class="cf-card__footer_message"><?php echo wp_kses_post( $data['description'] ); ?></span>
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
                        class="cf-card__footer_message"><?php echo wp_kses_post( $data['description'] ); ?></span>
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

?>
<ul class="tabs clearfix" data-tabgroup="first-tab-group">
    <li><a href="#tab1" class="active"><?php esc_html_e('Settings', 'wooflare'); ?></a></li>
    <li><a href="#tab2" class="log_tab"><?php esc_html_e('Log', 'wooflare'); ?></a></li>
</ul>
<section id="first-tab-group" class="tabgroup">
    <div class="tab-panel" id="tab1">
        <form name="woocf_settings" method="post" action="">
            <table>
                <tbody>
                    <?php
                    $cf_credentials_label = __('Cloudflare Credentials', 'wooflare');
                    foreach( $settings as $section=>$fields ){

                        if ($section === $cf_credentials_label) {
                            // Determine which connection state we're in
                            $is_cf_plugin_source = ($credentials && isset($credentials['source']) && $credentials['source'] === 'cf_plugin');
                            $is_manual_connected = ($credentials && isset($credentials['source']) && $credentials['source'] === 'manual');

                            if ($is_cf_plugin_source) {
                                // State A: Credentials from official Cloudflare plugin
                                $cloudflare_admin_url = admin_url('options-general.php?page=cloudflare');
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="woocf-connection-status woocf-connection-status--cf-plugin">
                                            <span class="dashicons dashicons-yes-alt woocf-status-icon"></span>
                                            <span class="woocf-status-text">
                                                <?php printf(

                                                    wp_kses_post( __('Connected via %sCloudflare plugin%s.', 'wooflare') ),
                                                    '<a href="' . esc_url($cloudflare_admin_url) . '">',
                                                    '</a>'
                                                ); ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            } elseif ($is_manual_connected) {
                                // State B: Manual credentials connected — build masked preview
                                $auth_type = isset($woocf_settings['cf_auth_type']) ? $woocf_settings['cf_auth_type'] : 'global_key';
                                if ($auth_type === 'api_token') {
                                    $token = isset($woocf_settings['cf_token']) ? $woocf_settings['cf_token'] : '';
                                    $masked = substr($token, 0, 3) . '...' . substr($token, -4);
                                    $auth_label = __('API Token', 'wooflare');
                                    $masked_display = '<code>' . esc_html($masked) . '</code>';
                                } else {
                                    $email = isset($woocf_settings['cf_email']) ? $woocf_settings['cf_email'] : '';
                                    $key = isset($woocf_settings['cf_key']) ? $woocf_settings['cf_key'] : '';
                                    $masked_key = substr($key, 0, 3) . '...' . substr($key, -4);
                                    $auth_label = __('Global API Key (Legacy)', 'wooflare');
                                    $masked_display = esc_html($email) . ' / <code>' . esc_html($masked_key) . '</code>';
                                }
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="woocf-connection-status woocf-connection-status--connected">
                                            <span class="dashicons dashicons-yes-alt woocf-status-icon"></span>
                                            <span class="woocf-status-text">
                                                <?php printf(

                                                    wp_kses_post( __('Connected via %s', 'wooflare') ),
                                                    '<strong>' . esc_html($auth_label) . '</strong>'
                                                ); ?>
                                                &mdash; <?php echo esc_html( $masked_display ); ?>
                                            </span>
                                            <a href="#" class="woocf-toggle-credentials"><?php esc_html_e('Change Credentials', 'wooflare'); ?></a>
                                            <span class="woocf-status-separator">|</span>
                                            <a href="#" class="woocf-disconnect"><?php esc_html_e('Disconnect', 'wooflare'); ?></a>
                                        </div>
                                        <div class="woocf-credentials-form" style="display: none;">
                                            <table><tbody>
                                                <?php
                                                foreach ($fields['fields'] as $data) {
                                                    woocf_render_field($data, $woocf_settings);
                                                }
                                                ?>
                                            </tbody></table>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            } else {
                                // State C: Not connected — show prompt + credential form
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="woocf-connect-prompt">
                                            <?php echo wp_kses_post( __('Please set your Cloudflare credentials below or install and configure the <a href="https://wordpress.org/plugins/cloudflare/" target="_blank">official Cloudflare plugin</a> to continue.', 'wooflare') ); ?>
                                        </div>
                                        <table><tbody>
                                            <?php
                                            foreach ($fields['fields'] as $data) {
                                                woocf_render_field($data, $woocf_settings);
                                            }
                                            ?>
                                        </tbody></table>
                                    </td>
                                </tr>
                                <?php
                            }
                            continue;
                        }

                        // Plugin-specific sections — only render if credentials are set
                        if (!$credentials) {
                            continue;
                        }

                        ?>
                        <tr class="tr-section-title">
                            <td><h2 class="cf-heading cf-heading--2"><?php echo esc_html($section); ?></h2></td>
                        </tr>
                        <?php

                        foreach ($fields['fields'] as $data) {
                            woocf_render_field($data, $woocf_settings);
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php wp_nonce_field('woocf_settings_nonce', 'woocf_settings_nonce'); ?>
            <?php submit_button(esc_html__('Save Changes', 'wooflare'), 'cf-btn cf-btn--primary'); ?>
        </form>
    </div>
    <div class="tab-panel" id="tab2">
        <h1 class="cf-heading cf-heading--1"><span><?php esc_html_e('Log', 'wooflare'); ?></span></h1>
        <?php
        if(!$siteSettings->isLoggingEnabled()) {
            ?>
        <span class="cf-card__footer_message"><?php esc_html_e('Logging is currently disabled. To start logging, enable checkbox on settings tab.', 'wooflare'); ?></span>
            <?php
        }
            ?>
        <div id="log_screen">
            <pre></pre>
        </div>
        <button id="clear_log_button" class="button button-primary cf-btn cf-btn--primary"><?php esc_html_e('Clear Log', 'wooflare'); ?></button>
        <div id="woocf_ajax_res_clearlog"></div>
    </div>
</section>
<?php
