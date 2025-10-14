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
 * Version:           1.2.0
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
add_filter('pre_set_site_transient_update_plugins', 'coverse_lens_search_check_for_plugin_update');

function coverse_lens_search() {
	/* Based off:
	 https://support.lens.org/knowledge-base/attribution-badge/
	 https://about.lens.org/for-developers/
	*/
	
	//wp_enqueue_style('coverse-lens-normalize-style', plugins_url('/css/lens.normalize.css', __FILE__), null, false);
	//wp_enqueue_style('coverse-lens-attribution-style', plugins_url('/css/lens.attribution.css', __FILE__), null, false);
	//wp_enqueue_style('coverse-lens-search-style', plugins_url('/css/lens.embed.css', __FILE__), null, false);
	
	//global $wp_filesystem;
	//require_once(ABSPATH . '/wp-admin/includes/file.php');
	//WP_Filesystem();
	//$htmlTemplateFile = plugin_dir_path( __FILE__ ) . 'coverse-lens-search.html';
	
	$content = '';
	//if ($wp_filesystem->exists($htmlTemplateFile)) {
	//	$content = $wp_filesystem->get_contents($htmlTemplateFile);
	//}
	
	$content .= '<p>There is an ever-expanding body of published science addressing serious side-effects of the COVID-19 vaccines.</p>';
	$content .= '<p><a href="https://coverse.org.au"><strong>CO</strong>VERSE</a> has made this research public via a curated collected at <a href="https://www.lens.org/lens/search/scholar/list?collectionId=232079">The Lens</a>, an online platform that makes access to article details possible via collaborations with the major open access scholarly and open data initiatives, including the global public resource of PubMed.</p>';
	$content .= '<p>Use the search feature below to explore this collection, which currently numbers over 4,300 papers.</p>';
	//$content .= '<div class="lens-ui-widget" style="margin-bottom:var(--wp--preset--spacing--70)">';
	$content .= '<iframe src="' . plugins_url('/lens.html', __FILE__) . '" height="100px" width="100%" frameborder=0 style="margin-bottom:var(--wp--preset--spacing--70)"></iframe>';
	//$content .= '</div>';
	$content .= '<p>If you know of relevant scientific articles that do not appear in this collection please <a href="mailto:science@coverse.org.au">let us know via email</a>.</p>';
	$content .= '<p>Thank you to our international network of volunteers for making this work possible and accessible.</p>';
	$content .= '<h2 class="wp-block-heading">Search instructions</h2>';
	$content .= '<p>Search via The Lens utilises standard search syntax. Some of the basic operators are:</p>';
	$content .= '<ul class="wp-block-list">';
	$content .= '<li>Boolean operators such as <code>AND</code>, <code>OR</code> and <code>NOT</code>, e.g. <code>AstraZeneca AND myocarditis</code></li>';
	$content .= '<li>Parenthesis <code>( )</code> to group search terms, e.g. <code>(myocarditis AND pericarditis) NOT mRNA</code></li>';
	$content .= '<li>Quotes <code>" "</code> to search an exact phrase, e.g. <code>"Post-Acute COVID-19 Vaccination Syndrome"</code></li>';
	$content .= '<li>Addition <code>+</code> and subtraction <code>-</code> to <em>must include</em> or <em>must not include</em> respectively, e.g. <code>myopericarditis +Pfizer -Moderna -AstraZeneca -Novavax</code></li>';
	$content .= '<li>Specific attributes such as <code>title</code>, <code>abstract</code>, <code>keyword</code>, e.g. <code>title:pharmacovigilance</code></li>';
	$content .= '</ul>';
	$content .= '<p>Further details can be found on <a href="https://support.lens.org/knowledge-base/search-syntax/" target="_blank">The Lens support pages</a> and the <a href="https://support.lens.org/knowledge-base/scholar-field-definition/" target="_blank">Scholar Field Definitions</a>.</p>';
	$content .= '<iframe src="https://lens.org/lens/embed/attribution" scrolling="no" height="30px" width="100%"></iframe>';
	
	return $content;
}
add_shortcode('coverse-lens-search', 'coverse_lens_search');
