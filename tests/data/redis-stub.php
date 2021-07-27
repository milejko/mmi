<?php

namespace {
    class Redis {    
        function pconnect() {

        }
        function auth() {

        }
        function select() {

        }
        function flushDB() {
            $GLOBALS['FAKE_REDIS'] = [];
        }
        function del(string $key) {
            unset($GLOBALS['FAKE_REDIS'][$key]);
        }
        function get(string $key) {
            return isset($GLOBALS['FAKE_REDIS'][$key]) ? $GLOBALS['FAKE_REDIS'][$key] : null;
        }
        function set(string $key, $value) {
            $GLOBALS['FAKE_REDIS'][$key] = $value;
        }
    }
}