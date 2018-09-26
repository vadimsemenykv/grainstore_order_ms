<?php
/**
 * Created by Marketing Pdffiller.
 * User: Vadym Semeniuk <semeniuk.vadym@pdffiller.team>
 * Date: 12.06.18
 */

namespace App\Infrastructure\Constraint;

use Symfony\Component\Validator\Constraints;
use App\Infrastructure\Request\Validator as RequestValidator;

trait ConstraintCreator
{
    public function constraintOptional(array $constraints = []): Constraints\Optional
    {
        return new Constraints\Optional($constraints);
    }

    public function constraintChoice(
        array $choices,
        string $message = RequestValidator::INVALID_VALUE,
        array $options = []
    ): Constraints\Choice {
        $options = array_merge(['choices' => $choices, 'message' => $message], $options);
        return new Constraints\Choice($options);
    }

    public function constraintRequired(array $constraints = []): Constraints\Required
    {
        return new Constraints\Required($constraints);
    }

    public function constraintNotBlank(
        string $message = RequestValidator::EMPTY_FIELD,
        array $options = []
    ): Constraints\NotBlank {
        $options = array_merge(['message' => $message], $options);
        return new Constraints\NotBlank($options);
    }

    public function constraintCollection(
        array $fields,
        string $missingFieldsMessage = RequestValidator::MISSING_FIELD,
        string $extraFieldsMessage = RequestValidator::EXTRA_FIELD,
        array $options = []
    ): Constraints\Collection {
        $options = array_merge(
            ['fields' => $fields, 'missingFieldsMessage' => $missingFieldsMessage, 'extraFieldsMessage' => $extraFieldsMessage],
            $options
        );
        return new Constraints\Collection($options);
    }

    public function constraintType(string $type, ?string $message = null, array $options = []): Constraints\Type
    {
        $options = array_merge(['type' => $type, 'message' => $message], $options);
        return new Constraints\Type($options);
    }

    public function constraintGreaterThanOrEqual(
        $value,
        string $message = RequestValidator::INVALID_VALUE,
        array $options = []
    ): Constraints\GreaterThanOrEqual {
        $options = array_merge(['value' => $value, 'message' => $message], $options);
        return new Constraints\GreaterThanOrEqual($options);
    }

    public function constraintRange(
        $min,
        $max,
        string $message = RequestValidator::INVALID_VALUE,
        array $options = []
    ) : Constraints\Range {
        $options = array_merge(['min' => $min, 'max' => $max, 'invalidMessage' => $message], $options);
        return new Constraints\Range($options);
    }

    public function constraintEqualTo(
        $value,
        string $message = RequestValidator::INVALID_VALUE,
        array $options = []
    ) : Constraints\EqualTo {
        $options = array_merge(['value' => $value, 'message' => $message], $options);
        return new Constraints\EqualTo($options);
    }
}
