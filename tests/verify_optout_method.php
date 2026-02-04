<?php
// tests/verify_optout_method.php

$controllerFile = __DIR__ . '/../com_crm/site/controllers/optout.php';

if (!file_exists($controllerFile)) {
    echo "Error: optout.php not found at $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Check if getValidProxyIp method definition exists
// We use a regex to look for "function getValidProxyIp" with any visibility modifier
if (!preg_match('/function\s+getValidProxyIp\s*\(/', $content)) {
    echo "FAILURE: Method getValidProxyIp is NOT defined in optout.php\n";
    echo "This will cause a fatal error when 'unsubscribe' is called.\n";
    exit(1);
}

echo "SUCCESS: Method getValidProxyIp is defined in optout.php.\n";
exit(0);
// Check if it is called
$isCalled = strpos($content, '$this->getValidProxyIp') !== false;

// Check if it is defined
$isDefined = strpos($content, 'function getValidProxyIp') !== false;

if ($isCalled && !$isDefined) {
    echo "FAILURE: getValidProxyIp is called but NOT defined in optout.php (Fatal Error Vulnerability).\n";
    exit(1);
}

if (!$isCalled) {
     echo "WARNING: getValidProxyIp is not called. Is this intentional?\n";
     exit(0);
}

if ($isDefined) {
    echo "SUCCESS: getValidProxyIp is defined.\n";
    exit(0);
}
