<?php

namespace SV\TitleEditHistory;

use XF\AddOn\AddOn;
use XF\Entity\AddOn as AddOnEntity;

/**
 * @version 2.3.1
 */
class InstallerListener
{
    /**
     * @version 2.3.1
     *
     * @param AddOn $addOn
     * @param AddOnEntity $installedAddOn
     * @param array $json
     *
     * @return void
     */
    public static function addonPostRebuild(/** @noinspection PhpUnusedParameterInspection */
        AddOn $addOn,
        AddOnEntity $installedAddOn,
        array $json
    )
    {
        static::runInstallSteps($addOn);
    }

    /**
     * @version 2.3.1
     *
     * @param AddOn $addOn
     * @param AddOnEntity $installedAddOn
     * @param array $json
     * @param array $stateChanges
     *
     * @return void
     */
    public static function addonPostInstall(/** @noinspection PhpUnusedParameterInspection */
        AddOn $addOn,
        AddOnEntity $installedAddOn,
        array $json,
        array &$stateChanges
    )
    {
        static::runInstallSteps($addOn);
    }

    /**
     * @since 2.3.1
     *
     * @param AddOn $addOn
     *
     * @return void
     */
    protected static function runInstallSteps(AddOn $addOn)
    {
        if (empty(Setup::$supportedAddOns[$addOn->getAddOnId()]))
        {
            return;
        }

        // kick off the installer
        $setup = new Setup($addOn, \XF::app());
        $setup->installStep1();
        $setup->installStep2();
    }
}