<?php
/**
  * COVERSE Lens search
  *
  * @package           coverse-lens-search
  * @author            Rado Faletič
  * @copyright         2025 Rado Faletič
  * @license           GPL-2.0-or-later
  *
  * @wordpress-plugin
  * Plugin Name:       COVERSE Lens search
  * Description:       Wordpress shortcode to add a custom Lens search feature on the COVERSE website.
  * Version:           1.0.5
  * Requires at least: 6.0
  * Requires PHP:      7.0
  * Author:            Rado Faletič
  * Author URI:        https://RadoFaletic.com
  * Text Domain:       COVERSE Lens search
  * License:           GPL v2 or later
  * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
  * Update URI:        https://RadoFaletic.com/plugins/info.json
  */

/*
  {Plugin Name} is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
  
  {Plugin Name} is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License along with {Plugin Name}. If not, see {URI to Plugin License}.
  */

if (!function_exists('RadoFaletic_com_check_for_updates')) {
  function RadoFaletic_com_check_for_updates($update, $plugin_data, $plugin_file) {
    static $response = false;
    if (empty($plugin_data['UpdateURI']) || !empty($update)) {
      return $update;
    }
    if ($response === false) {
      $response = wp_remote_get($plugin_data['UpdateURI']);
    }
    if (empty($response['body'])) {
      return $update;
    }
    $custom_plugins_data = json_decode($response['body'], true);
    if (!empty($custom_plugins_data[$plugin_file])) {
      return $custom_plugins_data[$plugin_file];
    } else {
      return $update;
    }
  }
  add_filter('update_plugins_RadoFaletic.com', 'RadoFaletic_com_check_for_updates', 10, 3);
}

function coverse_lens_search() {
  
  wp_enqueue_style('coverse-lens-search-style', plugins_url('/css/lens.embed.css', __FILE__), null, false);
  
  global $wp_filesystem;
  require_once(ABSPATH . '/wp-admin/includes/file.php');
  WP_Filesystem();
  $htmlTemplateFile = plugin_dir_path( __FILE__ ) . 'coverse-lens-search.html';
  
  $content = '';
  if ($wp_filesystem->exists($htmlTemplateFile)) {
      $content = $wp_filesystem->get_contents($htmlTemplateFile);
  }
  
  return $content;
}
add_shortcode('coverse-lens-search', 'coverse_lens_search');
