<?php declare(strict_types=1);

namespace App\Infrastructure\Request;

use App\Infrastructure\Exception\Attribute;
use App\Infrastructure\Exception\Http\BadRequestHttpException;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public const MISSING_FIELD = 'missing_field';
    public const EXTRA_FIELD = 'unexpected_field';
    public const EMPTY_FIELD = 'empty_field';
    public const INVALID_VALUE = 'invalid_value';


    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    public function validate(array $payload, array $constraints)
    {
        $errors = $this->validator->validate(
            $payload,
            new Constraints\Collection([
                'fields' => $constraints,
                'missingFieldsMessage' => self::MISSING_FIELD,
            ])
        );
        if (count($errors)) {
            $exception = new BadRequestHttpException();
            /** @var ConstraintViolation  $error */
            foreach ($errors as $error) {
                $attributePointer = str_replace(['][', '[', ']'], ['.', '', ''], $error->getPropertyPath());
                $exception->addAttribute(new Attribute($attributePointer, $error->getMessage()));
            }
            throw $exception;
        }
    }
}