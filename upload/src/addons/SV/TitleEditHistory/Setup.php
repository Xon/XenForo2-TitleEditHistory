<?php

namespace SV\TitleEditHistory;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $callback)
        {
            $sm->createTable($tableName, $callback);
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

    public function upgrade2010000Step1()
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

    protected function resourceManagerInstalled()
    {
        return $this->schemaManager()->tableExists('xf_rm_resource');
    }


    /**
     * @param Create|Alter $table
     * @param string       $name
     * @param string|null  $type
     * @param string|null  $length
     * @return \XF\Db\Schema\Column
     */
    protected function addOrChangeColumn($table, $name, $type = null, $length = null)
    {
        if ($table instanceof Create)
        {
            $table->checkExists(true);

            return $table->addColumn($name, $type, $length);
        }
        else if ($table instanceof Alter)
        {
            if ($table->getColumnDefinition($name))
            {
                return $table->changeColumn($name, $type, $length);
            }

            return $table->addColumn($name, $type, $length);
        }
        else
        {
            throw new \LogicException("Unknown schema DDL type " . get_class($table));
        }
    }

    /**
     * @return array
     */
    protected function getTables()
    {
        $tables = [];

        return $tables;
    }

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

        if ($this->resourceManagerInstalled())
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

        if ($this->resourceManagerInstalled())
        {
            $tables['xf_rm_resource'] = function (Alter $table) {
                $table->dropColumns(['resource_title_edit_count', 'resource_title_last_edit_date', 'resource_title_last_edit_user_id']);
            };
        }

        return $tables;
    }
}
