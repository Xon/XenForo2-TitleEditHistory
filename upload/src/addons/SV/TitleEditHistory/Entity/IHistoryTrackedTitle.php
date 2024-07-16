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
    public function getTitleEditKeys(): array;

    public function getTitleEditCount(): int;

    /**
     * @param \XF\Phrase|string|null $error
     * @return bool
     */
    public function canViewTitleHistory(&$error = null): bool;

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