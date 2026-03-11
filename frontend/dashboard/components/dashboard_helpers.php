<?php

if (!function_exists('dashboard_escape')) {
  function dashboard_escape($value)
  {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('dashboard_format_fcfa')) {
  function dashboard_format_fcfa($amount, $compact = true)
  {
    $amount = (int) $amount;

    if (!$compact) {
      return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    if ($amount >= 1000000000) {
      $formatted = number_format($amount / 1000000000, 1, '.', '');
      return rtrim(rtrim($formatted, '0'), '.') . ' Mrd FCFA';
    }

    if ($amount >= 1000000) {
      $formatted = number_format($amount / 1000000, 2, '.', '');
      return rtrim(rtrim($formatted, '0'), '.') . 'M FCFA';
    }

    return number_format($amount, 0, ',', ' ') . ' FCFA';
  }
}
