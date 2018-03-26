<?php

namespace SV\TitleEditHistory;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

	public function installStep1()
	{
		$this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
			$table->addColumn('thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
			$table->addColumn('thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
			$table->addColumn('thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
		});
	}

	public function installStep2()
    {
        if ($this->resourceManagerInstalled())
        {
            $this->schemaManager()->alterTable('xf_rm_resource', function(Alter $table) {
                $table->addColumn('resource_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $table->addColumn('resource_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $table->addColumn('resource_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            });
        }
    }

	public function upgrade10050Step1()
	{
        // rename if possible
        $this->schemaManager()->alterTable('xf_thread', function(Alter $table){
            $table->renameColumn('edit_count', 'thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
            $table->renameColumn('last_edit_date', 'thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
            $table->renameColumn('last_edit_user_id', 'thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
        });

        // make sure we clean-up the old columns!
        $this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
            $table->dropColumns(['edit_count', 'last_edit_date', 'last_edit_user_id']);
        });
	}

	public function upgrade2000070Step1()
    {
        if ($this->resourceManagerInstalled())
        {
            $this->schemaManager()->alterTable('xf_rm_resource', function(Alter $table) {
                $table->addColumn('resource_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $table->addColumn('resource_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $table->addColumn('resource_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            });
        }
    }

	public function uninstallStep1()
    {
        $this->db()->query(
            "
            DELETE FROM xf_edit_history
            WHERE content_type = 'thread_title'
        "
        );
    }

    public function uninstallStep2()
    {
		$this->schemaManager()->alterTable('xf_thread', function(Alter $table) {
			$table->dropColumns(['thread_title_edit_count', 'thread_title_last_edit_date', 'thread_title_last_edit_user_id']);
		});
	}

	public function uninstallStep3()
    {
        $db = $this->db();
        $db->delete('xf_edit_history', 'content_type = ' . $db->quote('resource_title'));
    }

    public function uninstallStep4()
    {
        if ($this->resourceManagerInstalled())
        {
            $this->schemaManager()->alterTable('xf_rm_resource', function(Alter $table) {
                $table->dropColumns(['resource_title_edit_count', 'resource_title_last_edit_date', 'resource_title_last_edit_user_id']);
            });
        }
    }

    protected function resourceManagerInstalled()
    {
        return $this->schemaManager()->tableExists('xf_rm_resource');
    }
}
