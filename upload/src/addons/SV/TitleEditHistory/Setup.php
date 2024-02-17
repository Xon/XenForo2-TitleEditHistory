<?php

namespace SV\TitleEditHistory;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Behavior\DevOutputWritable;
use XF\Db\Schema\Alter;
use XF\Entity\ContentTypeField;
use function array_values;
use function str_replace;

/**
 * @version 2.3.1
 */
class Setup extends AbstractSetup
{
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1(): void
    {
        $this->applySchema();
    }

    public function installStep2(): void
    {
        $this->applyContentTypeFields();
    }

    public function upgrade10050Step1(): void
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

    public function uninstallStep1(): void
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $callback)
        {
            $sm->dropTable($tableName);
        }
    }

    public function uninstallStep2(): void
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

    public function uninstallStep3(): void
    {
        $contentTypes = [];
        foreach (static::$supportedAddOns as $data)
        {
            foreach ($data as $contentType => $contentTypeFields)
            {
                $contentTypes[$contentType] = $contentType;
            }
        }
        if (count($contentTypes) === 0)
        {
            return;
        }

        $db = $this->db();
        $db->query('DELETE  FROM xf_edit_history WHERE content_type IN (' . $db->quote($contentTypes) . ')');
    }

    public function uninstallStep4(): void
    {
        // ensure this add-on removes any content type fields
        // development mode could have been turned on or off at any time
        $this->applyContentTypeFields(true);
    }

    public function applyContentTypeFields(bool $deleteAll = false): void
    {
        $db = $this->db();
        $em = $this->app()->em();
        $db->beginTransaction();
        $fieldsToPatch = [];
        foreach (static::$supportedAddOns as $addon => $data)
        {
            $addonIsActive = \XF::isAddOnActive($addon);
            foreach ($data as $contentType => $contentTypeFields)
            {
                foreach ($contentTypeFields as $fieldName => $class)
                {
                    /** @var ContentTypeField|null $field */
                    $field = $em->find('XF:ContentTypeField', [$contentType, $fieldName]);
                    if (!$deleteAll && $addonIsActive)
                    {
                        if ($field === null)
                        {
                            /** @var ContentTypeField|null $field */
                            $field = $em->create('XF:ContentTypeField');
                            $field->content_type = $contentType;
                            $field->field_name = $fieldName;
                        }
                        // SV/TitleEditHistory is not active/valid when the rebuild code, so do not link to an
                        $field->addon_id = '';
                        // entity content type field needs to have a : so \XF::finder() will work as expected
                        if ($fieldName === 'entity')
                        {
                            $class = str_replace('\\Entity\\', ':', $class);
                        }
                        $field->field_value = $class;

                        $field->saveIfChanged($saved, true, false);

                        $fieldsToPatch[] = $field;
                    }
                    else if ($field !== null)
                    {
                        $field->delete(true, false);
                    }
                }
            }
        }

        // trigger contentTypes rebuild
        \XF::triggerRunOnce();

        // patch database to reflect the correct add-on, so when this add-on is disabled the correct entries are also disabled
        foreach($fieldsToPatch as $field)
        {
            $field->fastUpdate('addon_id', 'SV/TitleEditHistory');
        }

        $db->commit();
    }

    public function applySchema(): void
    {
        $sm = $this->schemaManager();

        foreach ($this->getTables() as $tableName => $callback)
        {
            $sm->createTable($tableName, $callback);
            $sm->alterTable($tableName, $callback);
        }

        foreach ($this->getAlterTables() as $tableName => $callback)
        {
            if ($this->tableExists($tableName))
            {
                $sm->alterTable($tableName, $callback);
            }
        }
    }

    protected function getTables(): array
    {
        return [];
    }

    public static $supportedAddOns = [
        'XF' => [
            'thread_title' => [
                'edit_history_handler_class' => \SV\TitleEditHistory\EditHistory\Thread::class,
                'entity' => \XF\Entity\Thread::class,
            ],
        ],
        'XFRM' => [
            'resource_title' => [
                'edit_history_handler_class' => \SV\TitleEditHistory\EditHistory\ResourceItem::class,
                'entity' => \XFRM\Entity\ResourceItem::class,
            ],
        ],
        'XFMG' => [
            'xfmg_album_title' => [
                'edit_history_handler_class' => \SV\TitleEditHistory\EditHistory\Album::class,
                'entity' => \XFMG\Entity\Album::class,
            ],
            'xfmg_media_title' => [
                'edit_history_handler_class' => \SV\TitleEditHistory\EditHistory\MediaItem::class,
                'entity' => \XFMG\Entity\MediaItem::class,
            ],
        ],
    ];

    public function postUpgrade($previousVersion, array &$stateChanges): void
    {
        //$previousVersion = (int)$previousVersion;
        $this->applySchema();
        $this->applyContentTypeFields();
    }

    public function postRebuild(): void
    {
        $this->applySchema();
        $this->applyContentTypeFields();
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
