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
  * Description:       Wordpress shortcode to add a custom Lens search feature based on COVERSE’s custom database of COVID-19 vaccine adverse event research. To add this search feature to your own Wordpress website, add the following shortcode: [coverse-lens-search]
  * Version:           1.1.5
  * Requires at least: 6.0
  * Requires PHP:      7.0
  * Author:            Rado Faletič
  * Author URI:        https://RadoFaletic.com
  * Text Domain:       COVERSE Lens search
  * License:           GPL v2 or later
  * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
  * Update URI:        https://raw.githubusercontent.com/coverseau/coverse-lens-search/refs/heads/main/update-info.json
  */

/*
  {Plugin Name} is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
  
  {Plugin Name} is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License along with {Plugin Name}. If not, see {URI to Plugin License}.
  */

/**
 * Plugin updater handler function.
 * Pings the Github repo that hosts the plugin to check for updates.
 */
if (!function_exists('coverse_lens_search_check_for_plugin_update')) {
  function coverse_lens_search_check_for_plugin_update($transient) {
    // If no update transient or transient is empty, return.
    if (empty($transient->checked)) {
      return $transient;
    }
    
    // Plugin slug, path to the main plugin file, and the URL of the update server
    $plugin_slug = 'coverse-lens-search/coverse-lens-search.php';
    $update_url = 'https://raw.githubusercontent.com/coverseau/coverse-lens-search/refs/heads/main/update-info.json';
    
    // Fetch update information from your server
    $response = wp_remote_get($update_url);
    if (is_wp_error($response)) {
      return $transient;
    }
    
    // Parse the JSON response (update_info.json must return the latest version details)
    $update_info = json_decode(wp_remote_retrieve_body($response));
    
    // If a new version is available, modify the transient to reflect the update
    if (version_compare($transient->checked[$plugin_slug], $update_info->new_version, '<')) {
      $plugin_data = array(
          'slug'        => 'coverse-lens-search',
          'plugin'      => $plugin_slug,
          'new_version' => $update_info->new_version,
          'url'         => $update_info->url,
          'package'     => $update_info->package, // URL of the plugin zip file
        );
      $transient->response[ $plugin_slug ] = (object) $plugin_data;
    }
    
    return $transient;
  }
  add_filter('pre_set_coverse_lens_search_transient_update_plugins', 'coverse_lens_search_check_for_plugin_update');
}

function coverse_lens_search() {
  wp_enqueue_style('coverse-lens-attribution-style', plugins_url('/css/lens.attribution.css', __FILE__), null, false);
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
