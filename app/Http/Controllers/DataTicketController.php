<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DataTicketController extends OrderController
{

    /**
     * Формирование фейковых данных
     *
     * @return JsonResponse
     */
    public function orderData(): JsonResponse
    {
        $ticketTypes = TicketType::all();

        $ticketData = [];
        foreach ($ticketTypes as $ticketType)
        {
            $ticketData[$ticketType->name] = $ticketType->price;
        }

        $event_id                     = rand(2, 5);
        $event_date                   = Carbon::now()->addDays(rand(1, 30))->toDateTimeString();
        $ticket_adult_price           = $ticketData['adult'];
        $ticket_adult_quantity        = rand(0, 3);
        $ticket_kid_price             = $ticketData['child'];
        $ticket_kid_quantity          = rand(0, 3);
        $ticket_preferential_price    = $ticketData['preferential'];
        $ticket_preferential_quantity = rand(0, 3);
        $ticket_group_price           = $ticketData['group'];
        $ticket_group_quantity        = rand(0, 3);

        $this->store(
            $event_id,
            $event_date,
            $ticket_adult_price,
            $ticket_adult_quantity,
            $ticket_kid_price,
            $ticket_kid_quantity,
            $ticket_preferential_price,
            $ticket_preferential_quantity,
            $ticket_group_price,
            $ticket_group_quantity,
        );

        return response()->json($this->response);
    }
}
