<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOfferRequest;
use App\Http\Resources\OfferResource;
use App\Services\DTO\OfferDTO;
use App\Services\Handlers\Offers\CreateOfferHandler;
use App\Services\Handlers\Offers\GetOffersByUserHandler;
use App\Services\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class OfferController
 * @package App\Http\Controllers\V1
 */
class OfferController extends Controller
{
    use ResponseTrait;

    /**
     * @param CreateOfferRequest $request
     * @param CreateOfferHandler $handler
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOffer(CreateOfferRequest $request, CreateOfferHandler $handler): JsonResponse
    {
        $handler->handle(OfferDTO::fromArray([
            'user'          => $request->get('user'),
            'price_from'    => $request->get('price_from'),
            'price_to'      => $request->get('price_to'),
            'rooms'         => $request->get('rooms'),
            'region_id'     => $request->get('region_id'),
        ]));

        return $this->response('Объявление создано', true);
    }

    /**
     * @param Request $request
     * @param GetOffersByUserHandler $handler
     * @return JsonResponse
     */
    public function myOffers(Request $request, GetOffersByUserHandler $handler): JsonResponse
    {
        $offers = $handler->handle($request->get('user'));
        return $this->response('Мои объявления', OfferResource::collection($offers));
    }
}
