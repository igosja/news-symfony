<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AbstractController
 * @package App\Admin\Controller
 */
abstract class AbstractController extends BaseController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function jsonResult($data): Response
    {
        return new Response($this->serializer->serialize($data, JsonEncoder::FORMAT), 200, ['Content-Type' => 'application/json;charset=UTF-8']);

    }
}