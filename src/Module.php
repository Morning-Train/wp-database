<?php namespace Morningtrain\WP\Eloquent;

use Morningtrain\WP\Core\Abstracts\AbstractModule;
use Morningtrain\WP\Eloquent\Eloquent\Application;
use Morningtrain\WP\Eloquent\Migrations\Migrator;


class Module extends AbstractModule {

    protected bool $use_views = false;

    protected static bool $booted = false;

    public function init() {
        parent::init();

        if(!static::$booted) {
            Application::getInstance();
            static::$booted = true;
        }

        $this->migrateTables();
    }

    public function migrateTables() {
        $context = $this->getProjectContext();

        if(empty($context)) {
            return;
        }

        $migrator = new Migrator($context);

        $migrator->runPendingMigrations();
    }
}