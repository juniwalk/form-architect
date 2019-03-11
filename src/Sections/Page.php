<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Sections;

final class Page extends AbstractSection
{
    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return true;
    }
}
