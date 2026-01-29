<?php
// tests/verify_optout_ratelimit.php

$controllerFile = __DIR__ . '/../com_crm/site/controllers/optout.php';

if (!file_exists($controllerFile)) {
    echo "Error: optout.php not found at $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Check if checkRateLimit method definition exists
if (strpos($content, 'protected function checkRateLimit($ip)') === false) {
    echo "FAILURE: Method checkRateLimit not defined in optout.php\n";
    exit(1);
}

// Check if checkRateLimit is called inside unsubscribe
// We look for the call logic roughly
if (strpos($content, '$this->checkRateLimit($ip)') === false) {
    echo "FAILURE: Method checkRateLimit is not called in optout.php\n";
    exit(1);
}

// Check if it handles the return value (redirect or error)
if (strpos($content, 'COM_CRM_OPTOUT_TOO_MANY_REQUESTS') === false) {
    echo "FAILURE: Error message COM_CRM_OPTOUT_TOO_MANY_REQUESTS is not used.\n";
    exit(1);
}

echo "SUCCESS: Rate limiting logic appears to be present in optout.php.\n";
exit(0);
