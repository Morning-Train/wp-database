<?php

namespace Morningtrain\WP\Database\Migration;

class MigrationCreator
{
    public static function getFileContent(?string $table = null, bool $create = false) : string
    {
        $stub = static::getStub($table, $create);

        return static::populateStub($stub, $table);
    }

    protected static function getStub(string $table = null, bool $create = false) : string
    {
        if (empty($table)) {
            return file_get_contents(__DIR__ . '/stubs/migration.stub');
        } elseif ($create) {
            return file_get_contents(__DIR__ . '/stubs/migration.create.stub');
        }

        return file_get_contents(__DIR__ . '/stubs/migration.update.stub');
    }

    protected static function populateStub(string $stub, ?string $table = null) : string {
        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        return $stub;
    }
}
