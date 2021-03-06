<?php

namespace App\Controller;

use App\Resources\QuoteFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @var QuoteFinder
     */
    private $quoteFinder;

    /**
     * QuoteController constructor.
     * @param QuoteFinder $quoteFinder
     */
    public function __construct(QuoteFinder $quoteFinder)
    {
        $this->quoteFinder = $quoteFinder;
    }

    /**
     * @Route("/shout/{author}")
     */
    public function index(Request $request, string $author)
    {
        try {
            $limit = $this->getLimit($request);

            $quotes = $this->quoteFinder->findShoutedQuotesByActorWithLimit($author, $limit);
        } catch (BadRequestHttpException $e) {
            return new JsonResponse(
                $e->getMessage(),
                $e->getStatusCode()
            );
        }

        return new JsonResponse(
            $quotes,
            Response::HTTP_OK
        );
    }

    private function getLimit(Request $request): int
    {
        return $request->query->getInt('limit');
    }
}
