<?php namespace Morningtrain\WP\Eloquent\Eloquent;

use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Facade;
use Morningtrain\WP\Core\Abstracts\AbstractSingleton;
use Illuminate\Database\Capsule\Manager;

class Application extends AbstractSingleton {

    protected $manager;

    protected function __construct() {
        $this->manager = new Manager();
        $this->setupWp();
        $this->setupEloquent();
    }

    /**
     * Setup WP connection
     * @return void
     */
    protected function setupWp() {
        $this->manager->addConnection([], 'wp');
        $this->manager->getDatabaseManager()->extend('wp', function () {
            return WpConnection::instance();
        });
	    $this->manager->getDatabaseManager()->setDefaultConnection('wp');
    }

    /**
     * Setup Eloquent
     * @return void
     */
    protected function setupEloquent(){
        $app = $this->manager->getContainer();
        $app->instance('db', $this->manager->getDatabaseManager());
        Facade::setFacadeApplication($app);
        $this->manager->setAsGlobal();
        $this->manager->setEventDispatcher(new Dispatcher($app));
        $this->manager->bootEloquent();

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
    }

    /**
     * Get Capsule Manager
     * @return Manager
     */
    public function getCapsule(){
        return $this->manager;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::getInstance()->getCapsule()->getConnection()->$method(...$parameters);
    }
}