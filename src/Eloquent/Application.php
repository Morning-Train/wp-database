<?php

    namespace Morningtrain\WP\Database\Eloquent;

    use Illuminate\Database\DatabaseServiceProvider;
    use Illuminate\Database\DatabaseTransactionsManager;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Events\Dispatcher;
    use Illuminate\Support\Facades\Facade;
    use Illuminate\Database\Capsule\Manager;
    use Illuminate\Support\Facades\Schema;

    class Application
    {

        protected static Manager $manager;

        public static function setup()
        {
            static::$manager = new Manager();
            static::setupWp();
            static::setupEloquent();
        }

        /**
         * Setup WP connection
         * @return void
         */
        protected static function setupWp()
        {
            static::$manager->addConnection([], 'wp');
            static::$manager->getDatabaseManager()->extend('wp', function () {
                return WpConnection::instance();
            });
            static::$manager->getDatabaseManager()->setDefaultConnection('wp');
        }

        /**
         * Setup Eloquent
         * @return void
         */
        protected static function setupEloquent()
        {
            $app = static::$manager->getContainer();
            $app->instance('db', static::$manager->getDatabaseManager());
            static::$manager->setAsGlobal();
            static::$manager->setEventDispatcher(new Dispatcher($app));
            static::$manager->bootEloquent();

            $app->bind('db.connection', function ($app) {
                return $app['db']->connection();
            });

            $app->bind('db.schema', function ($app) {
                return $app['db']->connection()->getSchemaBuilder();
            });

            $app->singleton('db.transactions', function ($app) {
                return new DatabaseTransactionsManager;
            });

            Model::setConnectionResolver($app['db']);
            Schema::setFacadeApplication($app);
        }

        /**
         * Get Capsule Manager
         * @return Manager
         */
        public static function getCapsule()
        {
            return static::$manager;
        }

        /**
         * Dynamically pass methods to the default connection.
         *
         * @param string $method
         * @param array $parameters
         * @return mixed
         */
        public static function __callStatic($method, $parameters)
        {
            return static::getCapsule()->getConnection()->$method(...$parameters);
        }
    }