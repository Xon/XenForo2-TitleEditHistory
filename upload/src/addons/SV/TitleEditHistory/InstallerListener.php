<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\TitleEditHistory;

use XF\AddOn\AddOn;
use XF\Entity\AddOn as AddOnEntity;

abstract class InstallerListener
{
    public static function addonPostRebuild(AddOn $addOn, AddOnEntity $installedAddOn, array $json): void
    {
        static::runInstallSteps($addOn);
    }

    public static function addonPostInstall(AddOn $addOn, AddOnEntity $installedAddOn, array $json, array &$stateChanges): void
    {
        static::runInstallSteps($addOn);
    }

    public static function addonPostUninstall(AddOn $addOn, $addOnId, array $json): void
    {
        static::runInstallSteps($addOn);
    }

    protected static function runInstallSteps(AddOn $addOn): void
    {
        if (empty(Setup::$supportedAddOns[$addOn->getAddOnId()]))
        {
            return;
        }

        // kick off the installer
        $setup = new Setup($addOn, \XF::app());
        $setup->applySchema();
        $setup->applyContentTypeFields();
    }
}