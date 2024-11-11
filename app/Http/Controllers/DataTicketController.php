<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DataTicketController extends OrderController
{
    protected array $data;

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

        $this->data = [
            'event_id'              => rand(1, 4),
            'event_date'            => Carbon::now()->addDays(rand(1, 30))->toDateTimeString(),
            'ticket_adult_price'    => $ticketData['adult'],
            'ticket_adult_quantity' => rand(0, 5),
            'ticket_kid_price'      => $ticketData['child'],
            'ticket_kid_quantity'   => rand(0, 5),
        ];

        if (array_key_exists('group', $ticketData))
        {
            $this->data['ticket_group_price'] = $ticketData['group'];
            $this->data['ticket_group_quantity'] = rand(0, 5);
        }

        if (array_key_exists('preferential', $ticketData))
        {
            $this->data['ticket_preferential_price'] = $ticketData['preferential'];
            $this->data['ticket_preferential_quantity'] = rand(0, 5);
        }

        $this->store($this->data);

        return response()->json($this->response);
    }
}
