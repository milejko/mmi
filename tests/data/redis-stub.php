<?php

namespace {
    class Redis
    {
        public function pconnect()
        {
        }

        public function auth()
        {
        }

        public function select()
        {
        }

        public function flushDB()
        {
            $GLOBALS['FAKE_REDIS'] = [];
        }

        public function del(string $key)
        {
            unset($GLOBALS['FAKE_REDIS'][$key]);
        }

        public function get(string $key)
        {
            return isset($GLOBALS['FAKE_REDIS'][$key]) ? $GLOBALS['FAKE_REDIS'][$key] : null;
        }

        public function set(string $key, $value)
        {
            $GLOBALS['FAKE_REDIS'][$key] = $value;
        }
    }
}
