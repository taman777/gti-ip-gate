<?php
$conf = __DIR__ . '/allow_ips_conf.cgi';

if (!file_exists($conf)) {
    http_response_code(500);
    exit('IP config not found');
}

$allowed = array_filter(array_map('trim', file($conf)));
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (!ip_allowed($ip, $allowed)) {
    http_response_code(403);
    exit('Access denied');
}

require __DIR__ . '/admin.php';

function ip_allowed(string $ip, array $rules): bool {
    foreach ($rules as $rule) {
        if ($rule === '' || str_starts_with($rule, '#')) continue;

        if (strpos($rule, '/') === false) {
            if ($ip === $rule) return true;
        } else {
            if (ip_in_cidr($ip, $rule)) return true;
        }
    }
    return false;
}

function ip_in_cidr(string $ip, string $cidr): bool {
    [$subnet, $mask] = explode('/', $cidr);
    return (ip2long($ip) & ~((1 << (32 - $mask)) - 1))
        === ip2long($subnet);
}
