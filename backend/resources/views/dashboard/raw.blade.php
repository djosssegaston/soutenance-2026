<?php
if (isset($data) && is_array($data)) {
    extract($data, EXTR_SKIP);
}
if (!isset($template) || !is_string($template)) {
    throw new RuntimeException('Dashboard template is missing.');
}
include $template;
