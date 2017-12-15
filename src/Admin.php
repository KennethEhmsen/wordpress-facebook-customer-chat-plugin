<?php
/**
 * Created by PhpStorm.
 * User: alpipego
 * Date: 11.12.2017
 * Time: 09:44
 */

namespace FbCustomerChat;

class Admin
{
    const PAGE_ID = 'fb_chat_page';
    const SECTION_ID = 'fb_chat_settings';
    const SITE_FIELD_ID = 'fb_site_id';
    private $pluginVersion;
    private $mainFile;

    public function __construct(string $version, string $mainFile)
    {
        $this->pluginVersion = $version;
        $this->mainFile      = $mainFile;
    }

    public function run()
    {
        add_action('admin_init', [$this, 'registerSetting']);
        add_action('admin_menu', [$this, 'addSettingsSection'], 11);
        add_action('admin_menu', [$this, 'addSettingsField'], 12);
        add_action('admin_menu', [$this, 'addOptionsPage']);
        add_filter('plugin_action_links', [$this, 'pluginActionLinks'], 10, 2);

        add_action('plugins_loaded', [$this, 'loadTextdomain']);
    }

    public function loadTextdomain()
    {
        load_plugin_textdomain('fb-customer-chat', false, dirname(plugin_basename($this->mainFile)) . '/languages/');
    }

    public function pluginActionLinks(array $links, string $file)
    {
        if ($file === plugin_basename(dirname($this->mainFile) . '/' . basename($this->mainFile))) {
            $links[] = sprintf('<a href="options-general.php?page=%s">%s</a>', self::PAGE_ID, __('Settings', 'fb-customer-chat'));
        }

        return $links;
    }

    public function addOptionsPage()
    {
        add_options_page(
            __('Facebook Messenger Platform', 'fb-customer-chat'),
            __('Facebook Messenger Platform', 'fb-customer-chat'),
            'manage_options',
            self::PAGE_ID,
            [$this, 'renderPage']
        );
    }

    public function renderPage()
    {
        $docLink = sprintf(wp_kses(__('Please visit the <a target="_blank" rel="noopener" href="%s">Facebook Customer Chat WordPress Plugin</a> documentation page for usage instructions.', 'fb-customer-chat'), [
            'a' => [
                'href'   => [],
                'target' => [],
                'rel'    => [],
            ],
        ]), esc_url('https://megamaker.co/facebook-customer-chat-wordpress-plugin/'));
        ?>
        <div class="wrap">
            <h2>Facebook Customer Chat WordPress Plugin - v<?php echo $this->pluginVersion; ?></h2>
            <div class="update-nag"><?= $docLink; ?></div>
            <form action="<?= admin_url('options.php'); ?>" method="post">
                <?php
                settings_fields(self::SECTION_ID);
                do_settings_sections(self::PAGE_ID);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function addSettingsSection()
    {
        add_settings_section(
            self::SECTION_ID,
            __('General Settings', 'fb-customer-chat'),
            [$this, 'fb_chat_settings_section_callback'],
            self::PAGE_ID
        );
    }

    public function fb_chat_settings_section_callback()
    {
        echo __('This section description', 'fbchat');
    }

    public function registerSetting()
    {
        register_setting(self::SECTION_ID, self::SITE_FIELD_ID, [
            'type'              => 'int',
            'sanitize_callback' => [$this, 'sanitizeSiteId'],
        ]);
    }

    public function sanitizeSiteId($value) : int
    {
        return (int)$value;
    }

    public function addSettingsField()
    {
        add_settings_field(
            self::SITE_FIELD_ID,
            __('Tracking ID', 'fb-customer-chat'),
            [$this, 'renderIdField'],
            self::PAGE_ID,
            self::SECTION_ID
        );
    }

    public function renderIdField()
    {
        ?>
        <input type="text" name="<?= self::SITE_FIELD_ID; ?>" value="<?= $this->getSiteId() ?: ''; ?>">
        <p class="description">
            <?php printf(__('Enter your Facebook Page ID.', 'fb-customer-chat')); ?>
        </p>
        <?php
    }

    public function getSiteId()
    {
        return get_option(self::SITE_FIELD_ID, true);
    }
}
