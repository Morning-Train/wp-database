<?php

    namespace Morningtrain\WP\Database\Migration;

    class Migration
    {
        protected static ?string $dir = null;

        public static function setup(string $dir)
        {
            static::$dir = $dir;
        }
    }