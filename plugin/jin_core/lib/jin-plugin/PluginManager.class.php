<?php

require_once dirname(__DIR__) . "/spyc/spyc.php";

class PluginManager {

    public function __construct($pluginsDir) {
        $this->pluginsDir = $pluginsDir;
        $this->loadedPlugins = array();
    }

    protected function initPlugin($pluginDir, $plugin_conf) {
        $plugin_class = $plugin_conf['plugin_class'];
        spl_autoload_call($pluginDir . "/" . $plugin_class);
        $plugin = new $plugin_class;
        $plugin->init();
    }

    protected function processPluginLoading($pluginDir, $plugin_conf) {
        if (isset($plugin_conf['dependencies'])) {
            $deps = $plugin_conf['dependencies'];
            if ($deps)
                $this->loadPluginDependencies($deps);
        }
        $this->initPlugin($pluginDir, $plugin_conf);
    }

    public function loadPlugin($pluginDir) {
        if (in_array($pluginDir, $this->loadedPlugins))
            return;
        $plugin_conf = spyc_load_file($pluginDir . "/plugin.yaml");
        $this->processPluginLoading($pluginDir, $plugin_conf);
        $this->loadedPlugins[] = $pluginDir;
    }

    protected function loadPluginDependencies($deps) {
        $deps = explode(',', $deps);
        foreach ($deps as $dep)
            $this->loadPlugin($this->pluginsDir . '/' . $dep);
    }

    public function init() {
        $dh = opendir($this->pluginsDir);
        while ($pluginDir = readdir($dh)) {
            if (strpos($pluginDir, '.') === 0)
                continue;
            $this->loadPlugin($this->pluginsDir . '/' . $pluginDir);
        }
        closedir($dh);
    }

}

?>