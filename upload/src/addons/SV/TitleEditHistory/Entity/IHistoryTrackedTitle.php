<?php

/*
 * This file is part of a XenForo add-on.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SV\TitleEditHistory\Entity;

interface IHistoryTrackedTitle
{
    public function getTitleEditKeys();

    /**
     * @return int
     */
    public function getTitleEditCount();

    /**
     * @param string|null $error
     * @return bool
     */
    public function canViewTitleHistory(&$error = null);

    /**
     * @param string|null $error
     * @return bool
     */
    public function canView(&$error = null);

    /**
     * @param string|null $error
     * @return bool
     */
    public function canEditTitle(&$error = null);
}