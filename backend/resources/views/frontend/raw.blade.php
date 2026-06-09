<?php
if (isset($data) && is_array($data)) {
    extract($data, EXTR_SKIP);
}
$isLaravel = true;

if (!isset($template) || !is_string($template)) {
    throw new RuntimeException('Frontend template is missing.');
}
$scriptName = isset($script_name) ? (string) $script_name : '/frontend/public/index.php';
$originalScriptName = $_SERVER['SCRIPT_NAME'] ?? null;
$_SERVER['SCRIPT_NAME'] = $scriptName;

include $template;

if ($originalScriptName === null) {
    unset($_SERVER['SCRIPT_NAME']);
} else {
    $_SERVER['SCRIPT_NAME'] = $originalScriptName;
}
?>
