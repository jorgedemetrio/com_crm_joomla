<?php
/**
 * Verification script for CrmControllerOptout::getValidProxyIp existence.
 */

// Mock Joomla environment
define('_JEXEC', 1);

if (!function_exists('jimport')) {
    function jimport($path) {}
}

if (!class_exists('JControllerLegacy')) {
    class JControllerLegacy {
        public function __construct() {}
        public static function getInstance($prefix, $config = []) {}
    }
}

// Include the controller
require_once __DIR__ . '/../com_crm/site/controllers/optout.php';

try {
    $reflection = new ReflectionClass('CrmControllerOptout');
    if ($reflection->hasMethod('getValidProxyIp')) {
        echo "SUCCESS: Method getValidProxyIp exists.\n";
        exit(0);
    } else {
        echo "FAILURE: Method getValidProxyIp DOES NOT exist.\n";
        exit(1);
    }
} catch (ReflectionException $e) {
    echo "ERROR: Class CrmControllerOptout not found or other reflection error: " . $e->getMessage() . "\n";
    exit(2);
}
