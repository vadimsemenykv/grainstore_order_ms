<?php

namespace App\Controller;

use App\Infrastructure\Constraint\ConstraintCreator;
use App\Infrastructure\DB\AlphaNumericGenerator;
use App\Infrastructure\Exception\Attribute;
use App\Infrastructure\Exception\AttributesContainer;
use App\Infrastructure\Exception\ErrorCodeHolder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Service\Contract\Model\Order\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
    use ConstraintCreator;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function wrapExceptionInHttp(string $httpExceptionClassName, \Exception $exception)
    {
        /** @var AttributesContainer | ErrorCodeHolder $httpException */
        $httpException = new $httpExceptionClassName(
            $exception->getMessage(),
            $exception,
            $exception->getCode()
        );
        if ($exception instanceof ErrorCodeHolder && null !== $exception->getErrorCode()) {
            $httpException->setErrorCode($exception->getErrorCode());
        }
        return $httpException;
    }

    protected function getPlainArrayFromAttributes(array $attributes = [])
    {
        $plainArray = array_reduce($attributes, function ($result, Attribute $attribute) {
            $result[$attribute->attributeName] = \is_array($attribute->attributeVal)
            || \is_object($attribute->attributeVal)
                ? \json_encode($attribute->attributeVal)
                : $attribute->attributeVal;
            return $result ?: [];
        });

        return $plainArray ?? [];
    }

    /**
     * @param DocumentManager $documentManager
     * @param $type
     * @return string
     */
    protected function generateId(DocumentManager $documentManager, $type): string
    {
        $generator = new AlphaNumericGenerator();
        $generator->setKey($type);
        return $generator->generate($documentManager, new Order());
    }
}
