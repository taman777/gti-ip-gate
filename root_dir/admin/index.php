<?php
$conf = __DIR__ . '/allow_ips_conf.cgi';

if (!file_exists($conf)) {
    http_response_code(500);
    exit('IP config not found');
}

$allowed = array_filter(array_map('trim', file($conf)));
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// 1. IP チェック
if (!ip_allowed($ip, $allowed)) {
    http_response_code(403);
    exit('Access denied');
}

// 2. パス判定
$request_uri = $_SERVER['REQUEST_URI'] ?? '';

// 管理画面 (admin.php) へのアクセスかどうかを判定
// 許可IPであっても、パスなし（/）などは「目的のファイルなし」として制限
if (strpos($request_uri, 'admin.php') !== false) {
    require __DIR__ . '/admin.php';
} else {
    // パスなし、または他のファイルへのアクセス
    // root_dir に実ファイルがない場合は禁止とする
    http_response_code(403);
    exit('Forbidden: No target file or directory.');
}

// --- Functions ---

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
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);
    $mask_long = ~((1 << (32 - (int)$mask)) - 1);
    
    return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
}
