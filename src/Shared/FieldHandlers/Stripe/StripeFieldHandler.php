<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Stripe;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use enupal\stripe\elements\db\PaymentFormsQuery;
use enupal\stripe\elements\PaymentForm;

use function assert;

/**
 * @psalm-suppress MixedReturnTypeCoercion
 */
class StripeFieldHandler
{
    /**
     * @return PaymentForm[]
     *
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getAll(
        Element $element,
        string $field,
    ): array {
        $query = $element->getFieldValue($field);

        assert($query instanceof PaymentFormsQuery);

        return $query->all();
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOneOrNull(
        Element $element,
        string $field,
    ): ?PaymentForm {
        $query = $element->getFieldValue($field);

        assert($query instanceof PaymentFormsQuery);

        $form = $query->one();

        assert($form instanceof PaymentForm || $form === null);

        return $form;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): PaymentForm {
        $form = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($form instanceof PaymentForm);

        return $form;
    }
}
