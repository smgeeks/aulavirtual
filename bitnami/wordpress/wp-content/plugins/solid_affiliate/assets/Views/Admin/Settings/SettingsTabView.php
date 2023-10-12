<?php

namespace SolidAffiliate\Views\Admin\Settings;

use SolidAffiliate\Lib\FormBuilder\FormBuilder;
use SolidAffiliate\Lib\VO\Schema;
use SolidAffiliate\Lib\Settings;
use SolidAffiliate\Models\Affiliate;

class SettingsTabView
{

  /**
   * Renders a Tab on Admin Settings page.
   *
   * @param Schema $settings_schema
   * @param Object $current_settings
   * 
   * @return string
   */
  public static function render($settings_schema, $current_settings)
  {
    // check if the sudo param is set in the url
    $sudo_mode = isset($_GET['sudo']); 
    ob_start();
?>
    <?php
    // $setting_groups = [
    //   'Referral' => Settings::schema_for_settings_group('Referral'),
    //   'Integrations' => Settings::schema_for_settings_group('Integrations')
    // ];

    $setting_groups = Settings::schema_grouped_by_settings_groups($settings_schema);

    if ($sudo_mode) {
      // change every schema_entry->show_on_edit_form to true
      foreach ($setting_groups as $_group_name => $schema) {
        foreach ($schema->entries as $entry) {
          $entry->show_on_edit_form = true;
        }
      }
    }

    foreach ($setting_groups as $group_name => $schema) {
      /////////////////////////////////////////////////
      // Check if there are no entries to display
      $entries = $schema->entries;
      $entries_to_display = array_filter($entries, function ($entry) {
        return $entry->show_on_edit_form != FALSE;
      });
      if (count($entries_to_display) == 0) {
        continue;
      }
      /////////////////////////////////////////////////

      $group_name_formatted = str_replace(' ', '_', $group_name);
      # code...
    ?>

      <?php if ($group_name != 'HIDDEN') { ?>
        <div class="sld-card setting setting-group-<?php echo $group_name_formatted ?>">
        <?php }; ?>

        <?php if ($group_name != 'HIDDEN') { ?>
          <div class="sld_setting-heading">
            <?php do_action('solid_affiliate/settings/group_heading/before', $group_name); ?>
            <h2><?php echo (__($group_name, 'solid-affiliate')) ?></h2>
            <?php do_action('solid_affiliate/settings/group_heading/after', $group_name); ?>
          </div>
        <?php }; ?>
        <table class="form-table">
          <tbody>
            <?php echo FormBuilder::build_form($schema, 'edit', $current_settings) ?>
          </tbody>
        </table>
        <?php if ($group_name != 'HIDDEN') { ?>
        </div>
      <?php }; ?>

    <?php
    }
    ?>
    <!-- Settings Group -->

<?php

    $res = ob_get_clean();
    if ($res) {
      return $res;
    } else {
      return __("Error rendering settings.", 'solid-affiliate');
    }
  }
}
