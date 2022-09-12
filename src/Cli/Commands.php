<?php

    namespace Morningtrain\WP\Database\Cli;

    use Morningtrain\WP\Database\Database;
    use Morningtrain\WP\Database\Migration\Migration;

    class Commands
    {

        public static function register()
        {
            \WP_CLI::add_command('make:migration', [static::class, 'make']);
            \WP_CLI::add_command('dbmigrate', [static::class, 'migrate']);
        }

        public static function migrate()
        {
            echo "Running Migrations\n";
            try {
                Migration::migrate();
            } catch (\Exception $e) {
                \WP_CLI::error($e->getMessage());
            }
            \WP_CLI::success('Migrations has been run');
        }

        public static function make(array $args)
        {
            $migrationName = $args[0];
            $dateStr = (new \DateTime())->format('Y_m_d_His');

            $fileName = "{$dateStr}_{$migrationName}.php";
            $path = \trailingslashit(Migration::getPath());

            echo \WP_CLI::colorize("Make Migration: %g{$migrationName}%n\n");


            if (!is_dir($path)) {
                if (file_exists($path)) {
                    \WP_CLI::error("{$path} exists but is not a directory");
                }
                mkdir($path);
            }

            if (file_exists($path . $fileName)) {
                \WP_CLI::error("Migration File: {$fileName} already exists");
            }

            echo \WP_CLI::colorize("Generating migration file\n");
            echo \WP_CLI::colorize("in %y{$path}{$fileName}%n\n");


            file_put_contents($path . $fileName, static::getMigrationFileContents($migrationName));

            \WP_CLI::success('Migration file was created');
        }

        protected static function getMigrationFileContents(string $migrationName): string
        {
            $className = implode('', array_map('ucfirst', explode('_', $migrationName)));
            if (\str_starts_with($migrationName, 'create') && \str_ends_with($migrationName, 'table')) {
                $tableName = trim(str_replace(['create', 'table'], '', $migrationName), '_');
                $upMethodContent = <<<EOT
Schema::create('{$tableName}', function (Blueprint \$table) {
                \$table->bigIncrements('id');
           });
EOT;
                $downMethodContent = <<<EOT
Schema::dropIfExists('{$tableName}');
EOT;
            } else {
                $upMethodContent = '// Write your Schema up changes here';
                $downMethodContent = '// Write your Schema down changes here';
            }
            return <<<EOT
<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class {$className} extends Migration
    {
        public function up(): void
        {
            {$upMethodContent}
        }

        public function down(): void
        {
            {$downMethodContent}
        }
    }
?>
EOT;
        }
    }