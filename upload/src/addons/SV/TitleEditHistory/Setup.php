<?php

namespace SV\TitleEditHistory;

use SV\Utils\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
    // from https://github.com/Xon/XenForo2-Utils cloned to src/addons/SV/Utils
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $callback)
        {
            $sm->createTable($tableName, $callback);
            $sm->alterTable($tableName, $callback);
        }

        foreach ($this->getAlterTables() as $tableName => $callback)
        {
            $sm->alterTable($tableName, $callback);
        }
    }

    public function upgrade10050Step1()
    {
        // rename if possible
        $this->schemaManager()->alterTable('xf_thread', function (Alter $table) {
            $table->renameColumn('edit_count', 'thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
            $table->renameColumn('last_edit_date', 'thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
            $table->renameColumn('last_edit_user_id', 'thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
        });

        // make sure we clean-up the old columns!
        $this->schemaManager()->alterTable('xf_thread', function (Alter $table) {
            $table->dropColumns(['edit_count', 'last_edit_date', 'last_edit_user_id']);
        });
    }

    public function upgrade2010500Step1()
    {
        $this->installStep1();
    }

    public function uninstallStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $callback)
        {
            $sm->dropTable($tableName);
        }
    }

    public function uninstallStep2()
    {
        $sm = $this->schemaManager();

        foreach ($this->getRemoveAlterTables() as $tableName => $callback)
        {
            $sm->alterTable($tableName, $callback);
        }
    }

    public function uninstallStep3()
    {
        $this->db()->query(
            "
            DELETE FROM xf_edit_history
            WHERE content_type IN (
              'thread_title',
              'resource_title'
              )
        "
        );
    }

    /**
     * @return array
     */
    protected function getTables()
    {
        $tables = [];

        return $tables;
    }

    public static $supportedAddOns = [
        'XFRM' => true,
    ];

    /**
     * @return array
     */
    protected function getAlterTables()
    {
        $tables = [];

        $tables['xf_thread'] = function (Alter $table) {
            $this->addOrChangeColumn($table, 'thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
            $this->addOrChangeColumn($table, 'thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
            $this->addOrChangeColumn($table, 'thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
        };

        if ($this->addonExists('XFRM'))
        {
            $tables['xf_rm_resource'] = function (Alter $table) {
                $this->addOrChangeColumn($table, 'resource_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'resource_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'resource_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            };
        }

        return $tables;
    }

    protected function getRemoveAlterTables()
    {
        $tables = [];

        $tables['xf_thread'] = function (Alter $table) {
            $table->dropColumns(['thread_title_edit_count', 'thread_title_last_edit_date', 'thread_title_last_edit_user_id']);
        };

        if ($this->addonExists('XFRM'))
        {
            $tables['xf_rm_resource'] = function (Alter $table) {
                $table->dropColumns(['resource_title_edit_count', 'resource_title_last_edit_date', 'resource_title_last_edit_user_id']);
            };
        }

        return $tables;
    }
}
