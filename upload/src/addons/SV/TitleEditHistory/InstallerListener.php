<?php

namespace SV\TitleEditHistory;

use XF\AddOn\AddOn;

class InstallerListener
{
    public static function addonPostRebuild(/** @noinspection PhpUnusedParameterInspection */
        AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json)
    {
        if (empty(Setup::$supportedAddOns[$addOn->getAddOnId()]))
        {
            return;
        }

        // kick off the installer
        $setup = new Setup($addOn, \XF::app());
        $setup->installStep1();
    }

    public static function addonPostInstall(/** @noinspection PhpUnusedParameterInspection */
        AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json, array &$stateChanges)
    {
        if (empty(Setup::$supportedAddOns[$addOn->getAddOnId()]))
        {
            return;
        }

        // kick off the installer
        $setup = new Setup($addOn, \XF::app());
        $setup->installStep1();
    }
}