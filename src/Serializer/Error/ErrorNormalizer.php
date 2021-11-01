<?php

namespace App\Serializer\Error;

use ApiPlatform\Core\Problem\Serializer\ErrorNormalizerTrait;
use Symfony\Component\Debug\Exception\FlattenException as LegacyFlattenException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ErrorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    use ErrorNormalizerTrait;

    public const FORMAT = 'jsonproblem';
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $code = $this->getErrorCode($object);
        if (is_null($code)) {
            if ($object instanceof \Exception) {
                $code = $object->getCode();
            } else {
                $code = $object->getStatusCode();
            }
        }

        $data = [
            'code' =>  $code,
            'message' => $this->getErrorMessage($object, $context),
        ];

        return $data;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return self::FORMAT === $format && ($data instanceof \Exception || $data instanceof FlattenException || $data instanceof LegacyFlattenException);
    }

}