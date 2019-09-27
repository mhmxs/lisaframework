<?php

require_once "lib/jin-plugin/PluginManager.class.php";
require_once "lib/jin-plugin/Plugin.class.php";
require_once "Context.php";

Context::init();

class JCPluginManager extends PluginManager {

    protected function initPlugin($pluginDir, $plugin_conf) {
        if (isset($plugin_conf['services'])) {
            $services_list = $plugin_conf['services'];
            foreach ($services_list as $service_name => $service_class) {
                Context::registerService($service_name, $pluginDir, $service_class);
            }
        }
        if (isset($plugin_conf['hooks'])) {
            $hooks_list = $plugin_conf['hooks'];
            foreach ($hooks_list as $hook) {
                Context::registerHook($hook);
            }
        }
        if (isset($plugin_conf['hook_handlers'])) {
            $hook_handlers = $plugin_conf['hook_handlers'];

            foreach ($hook_handlers as $hook => $hook_handler) {
                $service = $hook_handler['service'];
                $handler = $hook_handler['handler'];
                Context::registerHookHandler($hook, $service, $handler);
            }
        }
	
	$context = array();
	$context['pluginDir'] = $pluginDir;
	$context['pluginConf'] = $plugin_conf;	
        parent::initPlugin($pluginDir, $plugin_conf);
	Context::callHook('init_plugin', &$context);
    }

}

class JCAutoloader {

    public static function autoload($class_name) {
        foreach (explode(",", spl_autoload_extensions()) as $ext) {
            if (@include_once($class_name . trim($ext)))
                return;
        }

        $classname = str_replace("\\", "/", ltrim($class_name, "\\"));

        if (file_exists(DIR_PLUGIN . "/" . $classname . '.php')) {
			//TODO die503 eltávolítani
            if (!include_once(DIR_PLUGIN . "/" . $classname . ".php"))
				die503(DIR_PLUGIN . "/" . $classname . ".php not found!");
        }
 
        $context = array('class_name' => $class_name);
        Context::callHook('autoload', &$context);
    }

}

if (strpos(".class.php", spl_autoload_extensions()) === false) {
    spl_autoload_extensions(spl_autoload_extensions() . ",.class.php");
}
spl_autoload_register(array('JCAutoloader', 'autoload'));

$app_context_path = dirname(dirname(dirname(__FILE__))) . '/tmp/cache/app_context.cache.php';
if (!file_exists($app_context_path)) {
    $pm = new JCPluginManager(dirname(__DIR__));
    $pm->init();

    $app_context = array();
    $app_context['services'] = Context::$services_map;
    $app_context['hooks'] = Context::$hooks_map;
	//TODO kitalálni hogy évüljön el a cache
    /*file_put_contents($app_context_path, "<?php\n" . '$app_context = ' . var_export($app_context, TRUE) . ';' . "\n?>");*/
} else {
    include($app_context_path);
    Context::$services_map = $app_context['services'];
    Context::$hooks_map = $app_context['hooks'];
}
?>