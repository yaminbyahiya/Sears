<?php

namespace WilokeHeroSlider\Controllers\Notification;

use WP_Admin_Bar;

class NotificationController
{
	private $topBarId = "wil-plugins-notification";

	public function __construct()
	{
		add_action("admin_bar_menu", [$this, "addLink"], 99);
		add_action("admin_menu", [$this, "registerMenu"]);
		add_action("admin_enqueue_scripts", [$this, "adminEnqueueScripts"]);
		add_action("wp_ajax_wil_update_total_items", [$this, "updateTotalItems"]);
	}

	public function updateTotalItems()
	{
		if (!current_user_can("administrator")) {
			wp_send_json_error();
		}

		$total = absint($_POST["total"]);
		update_option("wil_plugins_total", $total, false);
		wp_send_json_success();
	}

	public function registerMenu()
	{
		add_submenu_page(
			'plugins.php',
			esc_html__("Wiloke Elements", WILOKE_WILOKEHEROSLIDER_NAMESPACE),
			esc_html__('Wiloke Elements', WILOKE_WILOKEHEROSLIDER_NAMESPACE),
			'manage_options',
			$this->topBarId,
			[$this, "fetchPlugins"]
		);
	}

	public function adminEnqueueScripts()
	{
		wp_enqueue_style(
			$this->topBarId . "-global",
			plugin_dir_url(__FILE__) . "Source/Css/global.css",
			[],
			WILOKE_WILOKEHEROSLIDER_VERSION
		);

		wp_register_style(
			$this->topBarId . "-dashboard",
			plugin_dir_url(__FILE__) . "Source/Css/dashboard.css",
			[],
			WILOKE_WILOKEHEROSLIDER_VERSION
		);

		wp_register_script(
			$this->topBarId . "-global",
			plugin_dir_url(__FILE__) . "Source/Js/global.js",
			["jquery"],
			WILOKE_WILOKEHEROSLIDER_VERSION,
			true
		);

		wp_register_script(
			$this->topBarId . "-dashboard",
			plugin_dir_url(__FILE__) . "Source/Js/dashboard.js",
			["jquery"],
			WILOKE_WILOKEHEROSLIDER_VERSION,
			true
		);

		$total = get_option("wil_plugins_total");
		wp_localize_script(
			$this->topBarId . "-global",
			"WIL_PLUGINS_INFO",
			[
				"total" => empty($total) ? 0 : absint($total)
			]
		);
		wp_enqueue_script($this->topBarId . "-global");
	}

	public function fetchPlugins()
	{
		wp_enqueue_style($this->topBarId . "-dashboard");
		wp_enqueue_script($this->topBarId . "-dashboard");

		?>
        <div id="<?php echo esc_attr($this->topBarId . "-root"); ?>"></div>
		<?php
	}

	public function addLink(WP_Admin_Bar $oAdminBar)
	{
		$oAdminBar->add_menu([
			"id"    => $this->topBarId,
			"title" => esc_html__("Wiloke Elements", WILOKE_WILOKEHEROSLIDER_VERSION),
			"href"  => add_query_arg([
				"page" => $this->topBarId
			], "plugins.php")
		]);
	}
}