<?php
// tests/verify_integracao_json.php

define('_JEXEC', 1);

// Mock JFactory and Dbo
class JFactory {
    public static function getDbo() {
        return new MockDbo();
    }
}

class MockDbo {
    public function quoteName($name) { return "`$name`"; }
    public function quote($val) { return "'$val'"; }
    public function getQuery($new = false) { return new MockQuery(); }
}

class MockQuery {
    public function select($s) { return $this; }
    public function from($f) { return $this; }
    public function where($w) { return $this; }
}

// Mock JText
class JText {
    public static function _($key) { return $key; }
}

// Mock JTable
class JTable {
    protected $_tbl;
    protected $_tbl_key;
    protected $_db;
    protected $_errors = [];

    public function __construct($table, $key, &$db) {
        $this->_tbl = $table;
        $this->_tbl_key = $key;
        $this->_db = $db;
    }

    public function check() {
        return true;
    }

    public function setError($error) {
        $this->_errors[] = $error;
    }

    public function getErrors() {
        return $this->_errors;
    }
}

// Load the classes under test
require_once __DIR__ . '/../com_crm/administrator/tables/crm.php';
require_once __DIR__ . '/../com_crm/administrator/tables/integracao.php';

echo "Classes loaded.\n";

$db = new MockDbo();
$table = new CrmTableIntegracao($db);

$tests = [
    'Valid JSON Object' => [
        'input' => '{"key": "value"}',
        'should_pass' => true
    ],
    'Valid JSON Array' => [
        'input' => '["value1", "value2"]',
        'should_pass' => true
    ],
    'Invalid JSON (missing brace)' => [
        'input' => '{"key": "value"',
        'should_pass' => false
    ],
    'Invalid JSON (trailing comma)' => [
        'input' => '{"key": "value",}',
        'should_pass' => false
    ],
    'Empty String' => [
        'input' => '',
        'should_pass' => true
    ],
    'Null' => [
        'input' => null,
        'should_pass' => true
    ],
    'Simple String (Invalid JSON)' => [
        'input' => 'Just a string',
        'should_pass' => false
    ]
];

$failed = 0;

echo "Running tests...\n";

foreach ($tests as $name => $data) {
    // Reset errors
    // Since we can't easily reset private/protected properties without reflection or helper methods,
    // and we are mocking JTable, let's just create a new instance if needed, or rely on check() clearing/setting errors.
    // Standard JTable doesn't clear errors on check().
    // But for this test, we just check return value.

    $table->params_json = $data['input'];

    // Check if method exists (before implementation it won't exist in CrmTableIntegracao, but inherits from JTable/CrmTable)
    // Actually CrmTableIntegracao currently DOES NOT have check(), so it uses JTable::check() which returns true.
    // So BEFORE fix, "Invalid JSON" tests will FAIL (return true instead of false).

    $result = $table->check();

    $passed = ($result === $data['should_pass']);

    if (!$passed) {
        echo "[FAIL] $name: Expected " . ($data['should_pass'] ? 'TRUE' : 'FALSE') . ", got " . ($result ? 'TRUE' : 'FALSE') . "\n";
        $failed++;
    } else {
        echo "[PASS] $name\n";
    }
}

// Before implementation, we expect failures on invalid inputs because the check is missing.
if ($failed > 0) {
    echo "\n$failed tests failed (As Expected before fix).\n";
} else {
    echo "\nAll tests passed (Unexpected before fix).\n";
}
