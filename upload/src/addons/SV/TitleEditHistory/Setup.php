<?php

namespace SV\TitleEditHistory;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

/**
 * @version 2.3.1
 */
class Setup extends AbstractSetup
{
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
    }

    public function installStep2()
    {
        $sm = $this->schemaManager();

        foreach ($this->getAlterTables() as $tableName => $callback)
        {
            if ($this->tableExists($tableName))
            {
                $sm->alterTable($tableName, $callback);
            }
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
            if ($this->tableExists($tableName))
            {
                $sm->alterTable($tableName, $callback);
            }
        }
    }

    public function uninstallStep3()
    {
        $this->db()->query(
            "
            DELETE FROM xf_edit_history
            WHERE content_type IN (
              'thread_title',
              'resource_title',
              'xfmg_media_title',
              'xfmg_album_title'
              )
        "
        );
    }

    protected function getTables(): array
    {
        return [];
    }

    public static $supportedAddOns = [
        'XFRM' => true,
        'XFMG' => true,
    ];

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        $this->installStep1();
        $this->installStep2();
    }

    public function postRebuild()
    {
        $this->installStep1();
        $this->installStep2();
    }

    protected function getAlterTables(): array
    {
        return [
            'xf_thread' => function (Alter $table) {
                $this->addOrChangeColumn($table, 'thread_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'thread_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'thread_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            },
            'xf_rm_resource' => function (Alter $table) {
                $this->addOrChangeColumn($table, 'resource_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'resource_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'resource_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            },
            'xf_mg_media_item' => function (Alter $table) {
                $this->addOrChangeColumn($table, 'media_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'media_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'media_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            },
            'xf_mg_album' => function (Alter $table) {
                $this->addOrChangeColumn($table, 'album_title_edit_count')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'album_title_last_edit_date')->type('int')->nullable(false)->setDefault(0);
                $this->addOrChangeColumn($table, 'album_title_last_edit_user_id')->type('int')->nullable(false)->setDefault(0);
            },
        ];
    }

    protected function getRemoveAlterTables(): array
    {
        return [
            'xf_thread' => function (Alter $table) {
                $table->dropColumns(['thread_title_edit_count', 'thread_title_last_edit_date', 'thread_title_last_edit_user_id']);
            },

            'xf_rm_resource' => function (Alter $table) {
                $table->dropColumns(['resource_title_edit_count', 'resource_title_last_edit_date', 'resource_title_last_edit_user_id']);
            },

            'xf_mg_media_item' => function (Alter $table) {
                $table->dropColumns(['media_title_edit_count', 'media_title_last_edit_date', 'media_title_last_edit_user_id']);
            },

            'xf_mg_album' => function (Alter $table) {
                $table->dropColumns(['album_title_edit_count', 'album_title_last_edit_date', 'album_title_last_edit_user_id']);
            },
        ];
    }
}
