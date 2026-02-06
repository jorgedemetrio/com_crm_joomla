<?php
/**
 * Test script for IP Proxy validation logic.
 *
 * This script isolates the logic intended for the controller method `getValidProxyIp`
 * and verifies it against various input scenarios.
 */

// Define JEXEC to allow inclusion of the helper
define('_JEXEC', 1);

// Include the helper
require_once dirname(__DIR__) . '/com_crm/site/helpers/security.php';

$tests = [
    'Single IPv4' => [
        'input' => '192.168.1.1',
        'expected' => '192.168.1.1'
    ],
    'Multiple IPv4' => [
        'input' => '10.0.0.1, 192.168.1.1',
        'expected' => '10.0.0.1'
    ],
    'Multiple IPv4 with spaces' => [
        'input' => ' 10.0.0.1 , 192.168.1.1 ',
        'expected' => '10.0.0.1'
    ],
    'IPv6' => [
        'input' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
        'expected' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'
    ],
    'Invalid first, valid second' => [
        'input' => 'unknown, 192.168.1.1',
        'expected' => '192.168.1.1'
    ],
    'All invalid' => [
        'input' => 'unknown, invalid',
        'expected' => ''
    ],
    'Empty' => [
        'input' => '',
        'expected' => ''
    ],
    'XSS Attempt' => [
        'input' => '<script>alert(1)</script>',
        'expected' => ''
    ],
    'Long Garbage' => [
        'input' => str_repeat('a', 100),
        'expected' => ''
    ],
    'Valid but too long (impossible for valid IP but good for truncation test)' => [
        // IPs max out at 45 chars (IPv6 mapped), so truncation shouldn't really cut a valid IP
        // But if we simulate a weird case where logic fails to validate but we want to ensure truncation
        // Wait, logic validates IP, so it shouldn't return a long string unless it's a valid IP.
        // Let's just test that the output is always <= 45 chars even if validation was skipped (hypothetically)
        // Actually, let's test a valid IP that is exactly 45 chars (IPv4-mapped IPv6 can be long)
        // 0000:0000:0000:0000:0000:ffff:192.168.100.228 is 45 chars
        'input' => '0000:0000:0000:0000:0000:ffff:192.168.100.228',
        'expected' => '0000:0000:0000:0000:0000:ffff:192.168.100.228'
    ]
];

$failed = 0;

foreach ($tests as $name => $data) {
    // Use the helper method
    $result = CrmSecurityHelper::getValidProxyIp($data['input']);
    if ($result !== $data['expected']) {
        echo "FAILED: $name\n";
        echo "  Input: '{$data['input']}'\n";
        echo "  Expected: '{$data['expected']}'\n";
        echo "  Got: '$result'\n";
        $failed++;
    } else {
        echo "PASSED: $name\n";
    }
}

if ($failed > 0) {
    echo "\n$failed tests failed.\n";
    exit(1);
}

echo "\nAll tests passed.\n";
exit(0);
