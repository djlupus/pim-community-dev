<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Category\CategoryTree\Query;

use Akeneo\Channel\Component\Model\LocaleInterface;
use Akeneo\Pim\Enrichment\Component\Category\CategoryTree\ReadModel;
use Akeneo\UserManagement\Component\Model\UserInterface;

/**
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ListRootCategoriesWithCountNotIncludingSubCategories
{
    /**
     * @param string $translationLocaleCode
     * @param int    $userId
     * @param int    $rootCategoryIdToExpand
     *
     * @return ReadModel\RootCategory[]
     */
    public function list(string $translationLocaleCode, int $userId, int $rootCategoryIdToExpand): array;
}
