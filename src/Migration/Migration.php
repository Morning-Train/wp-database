<?php

    namespace Morningtrain\WP\Database\Migration;

    use Illuminate\Database\ConnectionResolverInterface;
    use Illuminate\Database\Migrations\DatabaseMigrationRepository;
    use Illuminate\Database\Migrations\MigrationRepositoryInterface;
    use Morningtrain\WP\Database\Eloquent\Application;
    use Illuminate\Database\Migrations\Migrator;

    class Migration
    {
        protected static array $paths = [];
        protected static array $options = [];
        protected static ?Migrator $migrator = null;

        public static function setup(array $paths, $options = [])
        {
            static::$paths = $paths;
            static::$options = $options;

            $capsule = Application::getCapsule();
            $databaseMigrationRepository = new DatabaseMigrationRepository($capsule->getDatabaseManager(), 'migration');
            if (!$databaseMigrationRepository->repositoryExists()) {
                $databaseMigrationRepository->createRepository();
            }
            $capsule->getContainer()->instance(MigrationRepositoryInterface::class, $databaseMigrationRepository);
            $capsule->getContainer()->instance(ConnectionResolverInterface::class, $capsule->getDatabaseManager());
            static::$migrator = $capsule->getContainer()->make(Migrator::class);
        }

        public static function getPath(): ?string
        {
            return static::$paths[0] ?? null;
        }

        public static function migrate(?array $paths = null, $options = [])
        {
            if ($paths === null) {
                $paths = static::$paths;
            }

            if ($options === null) {
                $options = static::$options;
            }

            static::$migrator->run($paths, $options);
        }

        public static function rollback(?array $paths = null, $options = [])
        {
            if ($paths === null) {
                $paths = static::$paths;
            }

            if ($options === null) {
                $options = static::$options;
            }

            static::$migrator->rollback($paths, $options);
        }
    }