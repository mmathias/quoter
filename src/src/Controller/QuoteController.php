<?php

namespace App\Controller;

use App\Message\QuoteRequestDTO;
use App\Resources\QuoteFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    /**
     * @var QuoteFinder
     */
    private $quoteFinder;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * QuoteController constructor.
     * @param QuoteFinder $quoteFinder
     * @param MessageBusInterface $bus
     */
    public function __construct(QuoteFinder $quoteFinder, MessageBusInterface $bus)
    {
        $this->quoteFinder = $quoteFinder;
        $this->bus = $bus;
    }

    /**
     * @Route("/shout/{author}")
     */
    public function index(Request $request, string $author)
    {
        try {
            $limit = $this->getLimit($request);
            $quoteRequestDTO = new QuoteRequestDTO('steve-jobs', $limit);
            $this->bus->dispatch($quoteRequestDTO);

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
