<?php

namespace {
    function apcu_fetch(string $key) {
        return isset($GLOBALS['FAKE_APC'][$key]) ? $GLOBALS['FAKE_APC'][$key] : null;
    }
    function apcu_clear_cache() {
        $GLOBALS['FAKE_APC'] = [];
    }
    function apcu_delete(string $key) {
        unset($GLOBALS['FAKE_APC'][$key]);
    }
    function apcu_store(string $key, mixed $var, int $ttl = 0) {
        $GLOBALS['FAKE_APC'][$key] = $var;
    }
}