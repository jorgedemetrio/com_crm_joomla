<?php
// Mock Joomla environment enough to load SimpleXML
define('_JEXEC', 1);

$xmlFile = __DIR__ . '/../com_crm/administrator/forms/agendamento.xml';

if (!file_exists($xmlFile)) {
    echo "Error: XML file not found at $xmlFile\n";
    exit(1);
}

$xml = simplexml_load_file($xmlFile);
if (!$xml) {
    echo "Error: Failed to parse XML\n";
    exit(1);
}

// Find status_job field
$statusJobField = null;
foreach ($xml->fieldset->field as $field) {
    if ((string)$field['name'] === 'status_job') {
        $statusJobField = $field;
        break;
    }
}

if (!$statusJobField) {
    echo "Error: Field 'status_job' not found\n";
    exit(1);
}

if ((string)$statusJobField['type'] !== 'list') {
    echo "Error: Field 'status_job' is not type 'list'. Found: " . $statusJobField['type'] . "\n";
    exit(1);
}

echo "Success: Field 'status_job' is type 'list'\n";

// Verify options
$expectedOptions = [
    'agendado',
    'em_execucao',
    'finalizado',
    'pausado',
    'erro'
];

$foundOptions = [];
foreach ($statusJobField->option as $option) {
    $foundOptions[] = (string)$option['value'];
}

$missing = array_diff($expectedOptions, $foundOptions);
$extra = array_diff($foundOptions, $expectedOptions);

if (!empty($missing) || !empty($extra)) {
    echo "Error: Options mismatch.\n";
    echo "Expected: " . implode(', ', $expectedOptions) . "\n";
    echo "Found: " . implode(', ', $foundOptions) . "\n";
    exit(1);
}

echo "Success: All expected options are present.\n";
exit(0);
