<?php namespace AgriLife\Admin;

class Message
{
    public $updated;
    /**
      * This method will be used to register
      * our custom settings admin page
      */

    public function init()
    {
        // register page
        add_action('network_admin_menu', array($this, 'setupTabs'));
        // update settings
        add_action('network_admin_menu', array($this, 'update'));
    }

    /**
      * This method will be used to register
      * our custom settings admin page
      */

    public function setupTabs()
    {
        add_submenu_page(
            'settings.php',
            __('Admin Message Settings', 'agrilife-message-domain'),
            __('Admin Message'),
            'manage_options',
            'agrilife-message',
            array($this, 'screen')
        );

        return $this;
    }

    /**
      * This method will parse the contents of
      * our custom settings age
      */

    public function screen()
    {
        ?>

        <div class="wrap">

            <h2><?php _e('Admin Message Settings', 'agrilife-message-domain'); ?></h2>

            <?php if ( $this->updated ) : ?>
                <div class="updated notice is-dismissible">
                    <p><?php _e('Settings updated successfully!', 'agrilife-message-domain'); ?></p>
                </div>
            <?php endif; ?>

            <form method="post">

                <h3>Options</h3>
                <p>
                    <label>
                        <input type="checkbox" name="show_message_everywhere" <?php
                          $checked = esc_attr($this->getSettings('show_message_everywhere'));
                          if($checked){
                            ?> checked="checked"<?php
                          }
                        ?>> <?php _e('Show message on all sites', 'agrilife-message-domain'); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="show_message_nonpublic" <?php
                          $checked = esc_attr($this->getSettings('show_message_nonpublic'));
                          if($checked){
                            ?> checked="checked"<?php
                          }
                        ?>> <?php _e('Show message on private sites', 'agrilife-message-domain'); ?>
                    </label>
                </p>
                <h3>Message</h3>
                <?php
                  $message = stripslashes( $this->getSettings('message') );
                  $message = html_entity_decode( $message );

                  wp_editor( $message, 'message', array(
                    'teeny' => true,
                    'media_buttons' => false,
                    'textarea_name' => 'message'
                  ));
                ?>

                <?php wp_nonce_field('my_plugin_nonce', 'my_plugin_nonce'); ?>
                <?php submit_button(); ?>

            </form>

        </div>

        <?php
    }

    /**
      * Check for POST (form submission)
      * Verifies nonce first then calls
      * updateSettings method to update.
      */

    public function update()
    {
        if ( isset($_POST['submit']) ) {

            // verify authentication (nonce)
            if ( !isset( $_POST['my_plugin_nonce'] ) )
                return;

            // verify authentication (nonce)
            if ( !wp_verify_nonce($_POST['my_plugin_nonce'], 'my_plugin_nonce') )
                return;

            return $this->updateSettings();
        }
    }

    /**
      * Updates settings
      */

    public function updateSettings()
    {
        $settings = array();

        if ( isset($_POST['show_message_everywhere']) ) {
            $settings['show_message_everywhere'] = esc_attr($_POST['show_message_everywhere']);
        }

        if ( isset($_POST['show_message_nonpublic']) ) {
            $settings['show_message_nonpublic'] = esc_attr($_POST['show_message_nonpublic']);
        }

        if ( isset($_POST['message']) ) {
            $settings['message'] = $_POST['message'];
        }

        if ( $settings ) {
            // update new settings
            update_site_option('agrilife_message_settings', $settings);
        } else {
            // empty settings, revert back to default
            delete_site_option('agrilife_message_settings');
        }

        $this->updated = true;
    }

    /**
      * Updates settings
      *
      * @param $setting string optional setting name
      */

    public function getSettings($setting='')
    {
        global $agrilife_message_settings;

        if ( isset($agrilife_message_settings) ) {
            if ( $setting ) {
                return isset($agrilife_message_settings[$setting]) ? $agrilife_message_settings[$setting] : null;
            }
            return $agrilife_message_settings;
        }

        $agrilife_message_settings = wp_parse_args(get_site_option('agrilife_message_settings'), array(
            'message' => null,
            'show_message_everywhere' => null,
            'show_message_nonpublic' => null
        ));

        if ( $setting ) {
            return isset($agrilife_message_settings[$setting]) ? $agrilife_message_settings[$setting] : null;
        }
        return $agrilife_message_settings;
    }
}
