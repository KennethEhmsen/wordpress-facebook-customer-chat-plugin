<?php
/*
Plugin Name: Facebook Customer Chat Plugin
Version: 1.0.1
Plugin URI: https://megamaker.co/facebook-customer-chat-wordpress-plugin/
Author: Justin Jackson
Author URI: https://justinjackson.ca
Description: Use the new Facebook Messenger Platform. Easily embed Facebook Messenger chat in your site without redirecting to Facebook.
Text Domain: wordpress-facebook-customer-chat-plugin
Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit;
}

$pluginVersion = '1.0.1';

require_once __DIR__ . '/src/Admin.php';
$admin = new \FbCustomerChat\Admin($pluginVersion, __FILE__);

if (is_admin()) {
    $admin->run();
}

add_action('wp_footer', function () use ($admin) {
    $siteId = $admin->getSiteId();
    if (! empty($siteId)) :
        ?>
        <script>
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '1820043301628783',
                    autoLogAppEvents: true,
                    xfbml: true,
                    version: 'v2.11'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div class="fb-customerchat" page_id="<?= $siteId; ?>"></div>
    <?php
    endif;
});
