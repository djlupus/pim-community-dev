<?php

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\FlatTranslator;

use Akeneo\Pim\Enrichment\Component\Product\Connector\FlatTranslator\FlatPropertyValueTranslator\PropertyFlatValueTranslatorInterface;

class FlatPropertyValueTranslatorRegistry
{
    /**
     * @var array
     */
    private $translators = [];

    public function addTranslator(PropertyFlatValueTranslatorInterface $propertyFlatTranslator): void
    {
        $this->translators[] = $propertyFlatTranslator;
    }

    public function getTranslator(string $column): ?PropertyFlatValueTranslatorInterface
    {
        foreach ($this->translators as $translator) {
            if ($translator->support($column)) {
                return $translator;
            }
        }

        return null;
    }
}
