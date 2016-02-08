<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://bugcatches.com/
 * @since      1.0.0
 *
 * @package    Bugcatches
 * @subpackage Bugcatches/admin/partials
 */
?>
<h2 class="nav-tab-wrapper"><?php _e('BugCatches', $this->plugin_name);?></h2>

<form method="post" name="bugcatches_options" action="options.php">

    <?php
    //Grab all options
    $options = get_option($this->plugin_name);

    // Cleanup
    $bugcatches_active = $options['bugcatches_active'];
    $feedback = $options['feedback'];
    $bugcatches_key = $options['bugcatches_key'];
    ?>

    <?php
    settings_fields($this->plugin_name);
    do_settings_sections($this->plugin_name);
    ?>

    <!-- active -->
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php _e('Send information to bugcatches.com', $this->plugin_name);?></span>
        </legend>
        <label for="<?php echo $this->plugin_name; ?>-bugcatches_active">
            <input type="checkbox" id="<?php echo $this->plugin_name; ?>-bugcatches_active" name="<?php echo $this->plugin_name; ?>[bugcatches_active]" value="1" <?php checked($bugcatches_active, 1); ?> />
            <span><?php esc_attr_e('Active (will send notifications to bugcatches.com)', $this->plugin_name); ?></span>
        </label>
    </fieldset>

    <!-- feedback -->
    <fieldset>
        <legend class="screen-reader-text"><span><?php _e('Add feedback button in the site', $this->plugin_name);?></span></legend>
        <label for="<?php echo $this->plugin_name; ?>-feedback">
            <input type="checkbox" id="<?php echo $this->plugin_name; ?>-feedback" name="<?php echo $this->plugin_name; ?>[feedback]" value="1" <?php checked($feedback, 1); ?> />
            <span><?php esc_attr_e('Add feedback button in the site', $this->plugin_name); ?></span>
        </label>
    </fieldset>


    <!-- key -->
    <fieldset>
        <p>BugCatches Key</p>
        <legend class="screen-reader-text"><span><?php _e('BugCatches Key', $this->plugin_name); ?></span></legend>
        <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-bugcatches_key" name="<?php echo $this->plugin_name; ?>[bugcatches_key]" value="<?php if(!empty($bugcatches_key)) echo $bugcatches_key; ?>"/>

    </fieldset>

    <?php submit_button(__('Save all changes', 'primary','submit', TRUE)); ?>

