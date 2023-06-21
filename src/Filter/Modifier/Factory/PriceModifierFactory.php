<?php

namespace App\Filter\Modifier\Factory;

use App\Filter\Modifier\PriceModifierInterface;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

class PriceModifierFactory implements PriceModifierFactoryInterface
{

    public function create(string $modifierType): PriceModifierInterface
    {
        // Convert snake_case to ClassName (PascalCase)
        $modifierClassBaseName = str_replace('_', '', ucwords($modifierType, '_'));

        $modifier = self::PRICE_MODIFIER_NAMESPACE . $modifierClassBaseName;

        if (!class_exists($modifier)) {
            throw new ClassNotFoundError($modifier);
        }

        return new $modifier();
    }
}