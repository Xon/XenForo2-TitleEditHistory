<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\Entity;

/**
 * Interface IHistoryTrackedTitle
 *
 * @property int $user_id
 */
interface IHistoryTrackedTitle
{
    /**
     * @return array
     */
    public function getTitleEditKeys();

    /**
     * @return int
     */
    public function getTitleEditCount();

    /**
     * @param \XF\Phrase|string|null $error
     * @return bool
     */
    public function canViewTitleHistory(&$error = null);

    /**
     * @param \XF\Phrase|string|null $error
     * @return bool
     */
    public function canView(&$error = null);

    /**
     * @param \XF\Phrase|string|null $error
     * @return bool
     */
    public function canEditTitle(&$error = null);
}