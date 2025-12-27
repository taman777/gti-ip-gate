<?php
// ブラウザやプロキシのキャッシュを防止し、常にIP判定が実行されるようにする
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$conf = __DIR__ . '/allow_ips_conf.cgi';

if (!file_exists($conf)) {
    http_response_code(500);
    exit('IP config not found');
}

$allowed = array_filter(array_map('trim', file($conf)));
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
// Cloudflareやプロキシ経由の場合のIP取得
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
}

// 1. IP チェック
if (!ip_allowed($ip, $allowed)) {
    http_response_code(403);
    exit('Access denied');
}

// 2. パス判定と実在確認
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$request_path = parse_url($request_uri, PHP_URL_PATH);

// root_dir の絶対パスを取得
$root_dir_path = realpath(dirname(__DIR__));

// ベースとなるパス（admin/index.php がある場所の一個上）を取得
// 例: /gti-ip-gate/root_dir/admin/index.php -> /gti-ip-gate/root_dir
$script_base = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($script_base === DIRECTORY_SEPARATOR || $script_base === '\\') {
    $script_base = '';
}

// REQUEST_URI からベース部分を取り除いて、相対的なファイルパスを特定する
$relative_path = $request_path;
if ($script_base !== '' && strpos($request_path, $script_base) === 0) {
    $relative_path = substr($request_path, strlen($script_base));
}

$target_file = $root_dir_path . $relative_path;

// デバッグ用出力 (?debug=1 で有効)
if (isset($_GET['debug'])) {
    header('Content-Type: text/html; charset=UTF-8');
    echo "<h3>Debug: GTI IP Gate (admin/index.php)</h3>";
    echo "<pre>";
    echo "IP_ADDRESS: " . htmlspecialchars($ip) . "\n";
    echo "REQUEST_URI: " . htmlspecialchars($request_uri) . "\n";
    echo "REQUEST_PATH: " . htmlspecialchars($request_path) . "\n";
    echo "SCRIPT_NAME: " . htmlspecialchars($_SERVER['SCRIPT_NAME']) . "\n";
    echo "SCRIPT_BASE: " . htmlspecialchars($script_base) . "\n";
    echo "RELATIVE_PATH: " . htmlspecialchars($relative_path) . "\n";
    echo "ROOT_DIR_PATH: " . htmlspecialchars($root_dir_path) . "\n";
    echo "TARGET_FILE (Full): " . htmlspecialchars($target_file) . "\n";
    echo "FILE_EXISTS: " . (file_exists($target_file) ? 'YES' : 'NO') . "\n";
    echo "IS_FILE: " . (is_file($target_file) ? 'YES' : 'NO') . "\n";
    echo "</pre>";
    if (!isset($_GET['continue'])) exit;
}

// 管理画面 (admin.php) へのアクセス判定
if (basename($request_path) === 'admin.php') {
    require __DIR__ . '/admin.php';
    exit;
}

if ($target_file && strpos(realpath($target_file) ?: $target_file, $root_dir_path) === 0 && is_file($target_file)) {
    // 実在するファイルの場合は適切な方法で表示/実行
    $extension = pathinfo($target_file, PATHINFO_EXTENSION);
    
    if ($extension === 'php') {
        // PHPファイルの場合は実行
        chdir(dirname($target_file));
        require $target_file;
    } else {
        // 静的ファイルの場合はMIMEタイプを設定して出力
        $mime_types = [
            'html' => 'text/html',
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'pdf'  => 'application/pdf',
        ];
        $content_type = $mime_types[$extension] ?? 'application/octet-stream';
        header("Content-Type: $content_type");
        readfile($target_file);
    }
    exit;
}

// 許可IPであっても、パスなし判定時やファイルが存在しない場合は 403
http_response_code(403);
exit('Forbidden: No target file or directory.');

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
    // 32ビット整数の範囲を超えることがあるため、(int) キャストやビット演算に注意
    $mask_int = (int)$mask;
    if ($mask_int === 0) return true;
    $mask_long = ~((1 << (32 - $mask_int)) - 1);
    
    return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
}
