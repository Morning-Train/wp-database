<?php

namespace Morningtrain\WP\Database\Migration;

class StubsHandler
{
    public static function parseStub(string $stubName, array $args = []) : string
    {
        $stubContent = static::getStubContent($stubName);

        return static::populateStub($stubContent, $args);
    }

    protected static function getStubContent(string $stubName) : string
    {
        $stubFile = __DIR__ . "/stubs/{$stubName}.stub";

        if(file_exists($stubFile)) {
            return file_get_contents($stubFile);
        }

        throw new \Exception("Stub file {$stubFile} does not exist");
    }

    protected static function populateStub(string $stubContent, array $args = []) : string {
        $keys = array_map(function ($key) {
            return "{{ {$key} }}";
        }, array_keys($args));

        return str_replace($keys, $args, $stubContent);
    }
}
