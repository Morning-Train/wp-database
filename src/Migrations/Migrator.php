<?php namespace Morningtrain\WP\Eloquent\Migrations;

use Morningtrain\WP\Core\Abstracts\AbstractProject;
use Symfony\Component\Finder\Finder;

class Migrator {

	protected AbstractProject $context;


	public function __construct($context) {
		$this->context = $context;
	}

	/**
	 * Get Migration files finder
	 * @return null|Finder
	 */
	public function getMigrationFiles() {
		$migrations_path = $this->context->getBaseDir() . 'database/migrations';

		if(!is_dir($migrations_path)) {
			return null;
		}

		$finder = new Finder();

		return $finder->files()->name('/[a-z0-9_]+-[0-9.]+\.php$/')->in($this->context->getBaseDir() . 'database/migrations');
	}

	/**
	 * Get migration option name
	 * @return string
	 */
	protected function getOptionName() {
		return "db_migrated_version-{$this->context->textDomain()}";
	}

	/**
	 * Get last migrated version
	 * @return false|mixed|void
	 */
	public function getLastMigratedVersion() {
		return get_option($this->getOptionName());
	}

	/**
	 * Set last migrated version
	 * @param $version
	 *
	 * @return bool
	 */
	protected function setLastMigratedVersion($version) {
		return update_option($this->getOptionName(), $version);
	}

	/**
	 * Get pending migration files
	 * @return null|Finder
	 */
	public function getPendingMigrationFiles() {
		$last_migrated_version = $this->getLastMigratedVersion();
		if($this->context->version() <= $last_migrated_version) {
			return null;
		}

		$finder = $this->getMigrationFiles();

		return $finder->filter(function(\SplFileInfo $file) use ($last_migrated_version) {
			return $this->getVersionFromFileName($file->getBasename()) > $last_migrated_version;
		});
	}

	/**
	 * Get Version from file name
	 * @param $file_name
	 *
	 * @return string
	 */
	protected function getVersionFromFileName($file_name) {
		return substr($file_name, strpos($file_name, '-'));
	}

	/**
	 * Run pending migrations
	 * @return void
	 */
	public function runPendingMigrations() {
		$this->runMigrations($this->getPendingMigrationFiles());

		$this->setLastMigratedVersion($this->context->version());
	}

	/**
	 * @param $migration_files
	 *
	 * @return void
	 */
	public function runMigrations($migration_files) {
        if(empty($migration_files)) {
            return;
        }
		foreach($migration_files as $migration_file) {
			$this->runMigration($migration_file);
		}
	}

	/**
	 * @param \SplFileInfo $migration_file
	 *
	 * @return void
	 */
	public function runMigration(\SplFileInfo $migration_file) {
		try {
			$migration = require($migration_file->getPathname());

			if(is_object($migration) && method_exists($migration, 'up')) {
				$migration->up();
			}
		} catch(\Exception $e) {
			// DO Nothing for now
		}
	}
}