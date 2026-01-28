<?php
// tests/verify_lead_xml_security.php

$xmlFile = __DIR__ . '/../com_crm/administrator/forms/lead.xml';

if (!file_exists($xmlFile)) {
    echo "Error: lead.xml not found at $xmlFile\n";
    exit(1);
}

$xml = simplexml_load_file($xmlFile);

if ($xml === false) {
    echo "Error: Failed to parse lead.xml\n";
    exit(1);
}

$found = false;
$secure = false;

// Traverse fields to find 'site'
foreach ($xml->fieldset as $fieldset) {
    foreach ($fieldset->field as $field) {
        if ((string)$field['name'] === 'site') {
            $found = true;
            $filter = (string)$field['filter'];
            echo "Found 'site' field. Current filter: '$filter'\n";

            if ($filter === 'url') {
                $secure = true;
            }
            break 2;
        }
    }
}

if (!$found) {
    echo "Error: 'site' field not found in lead.xml\n";
    exit(1);
}

if ($secure) {
    echo "SUCCESS: 'site' field is using secure filter='url'.\n";
    exit(0);
} else {
    echo "FAILURE: 'site' field is NOT using secure filter='url'. Potential XSS vulnerability.\n";
    exit(1);
}
