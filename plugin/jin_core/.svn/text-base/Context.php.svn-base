<?php

class Context {

	public static $services_map;
	public static $hooks_map;
	
	/**
	 *
	 * @var \lisa_core_api\IView
	 */
	private static $view;

	public static function init() {
		self::$services_map = array();
		self::$hooks_map = array();
		self::$view = null;
	}

	public static function registerService($service, $path, $class_name) {
		self::$services_map[$service] = array($path, $class_name);
	}
	
	public static function getService($service) {
		list($path, $class_name) = self::$services_map[$service];

		spl_autoload_call($path . '/' . $class_name);
		$service_instance = &$class_name::getInstance();

		return $service_instance;
	}

	public static function registerHook($hook) {
		self::$hooks_map[$hook] = array();
	}

	public static function registerHookHandler($hook, $service, $handler) {
		self::$hooks_map[$hook][] = array($service, $handler);
	}
	
	public static function setView(\lisa_core_api\IView $view) {
		if (self::$view) {
			$view->setContent(self::$view->getContent() . $view->getContent());
		}
		
		self::$view = $view;
	}

	/**
	 *
	 * @return \lisa_core_api\IView 
	 */
	public static function getView() {
		return self::$view;
	}
	
	public static function callHook($hook, &$context, $grab_out = FALSE) {
		if (!array_key_exists($hook, self::$hooks_map))
			return;

		$result = "";

		foreach (self::$hooks_map[$hook] as $hook_element) {
			list($service, $handler) = $hook_element;
			$service_instance = self::getService($service);

			if ($grab_out)
				ob_start();

			$service_instance->{$handler}($context);

			if ($grab_out) {
				$contents = ob_get_contents();
				ob_end_clean();
				$result .= $contents;
			}
		}

		return $result;
	}

}
