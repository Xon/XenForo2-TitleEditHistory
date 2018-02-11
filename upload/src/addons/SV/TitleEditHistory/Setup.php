<?php

namespace SV\TitleEditHistory;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
	public function checkRequirements(&$errors = [], &$warnings = []) {
		$required = '5.4.0';
		$phpversion = phpversion();
		if (version_compare($phpversion, $required, '<'))
		{
			$errors[] = "PHP {$required} or newer is required. {$phpversion} does not meet this requirement. Please ask your host to upgrade PHP";
		}
	}

	public function install(array $stepParams = [])
	{
		$this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
			$table->addColumn('thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
			$table->addColumn('thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
			$table->addColumn('thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
		});

	}

	public function upgrade(array $stepParams = [])
	{
		if ($this->addOn->version_id <= 10050)
		{
			// rename if possible
			$this->schemaManager()->alterTable('xf_thread', function(Alter $table){
				$table->renameColumn('edit_count', 'thread_title_edit_count')->type('int')->nullable(false);
				$table->renameColumn('last_edit_date', 'thread_title_last_edit_date')->type('int')->nullable(false);
				$table->renameColumn('last_edit_user_id', 'thread_title_last_edit_user_id')->type('int')->nullable(false);
			});

			// make sure we clean-up the old columns!
			$this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
				$table->dropColumns(['edit_count', 'last_edit_date', 'last_edit_user_id']);
			});
		}
	}

	public function uninstall(array $stepParams = [])
	{
		$this->db()->query("
            DELETE FROM xf_edit_history
            WHERE content_type = 'thread_title'
        ");

		$this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
			$table->dropColumns(['thread_title_edit_count', 'thread_title_last_edit_date', 'thread_title_last_edit_user_id']);
		});
	}
}