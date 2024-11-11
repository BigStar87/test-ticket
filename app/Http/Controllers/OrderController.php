<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected array $response;
    protected Order $order;
    protected Ticket $ticket;
    protected FakeController $fakeController;

    public function __construct(Order $order, Ticket $ticket, FakeController $fakeController)
    {
        $this->order          = $order;
        $this->ticket         = $ticket;
        $this->fakeController = $fakeController;
    }

    /**
     * Бронирование билетов и сохранение их в БД
     *
     * @param   array  $data
     *
     * @return void
     */
    public function store(array $data): void
    {
        $barcode    = $this->generateUniqueBarcode();
        $equalPrice = ($data['ticket_adult_price'] * $data['ticket_adult_quantity']) + ($data['ticket_kid_price'] * $data['ticket_kid_quantity']);
        $created    = now();

        $orderData = [
            'event_id'                     => $data['event_id'],
            'event_date'                   => $data['event_date'],
            'ticket_adult_price'           => $data['ticket_adult_price'],
            'ticket_adult_quantity'        => $data['ticket_adult_quantity'],
            'ticket_kid_price'             => $data['ticket_kid_price'],
            'ticket_kid_quantity'          => $data['ticket_kid_quantity'],
            'barcode'                      => $barcode,
        ];

        if (array_key_exists('ticket_group_quantity', $data))
        {
            $equalPrice += $data['ticket_group_price'] * $data['ticket_group_quantity'];

            $orderData['ticket_group_price']    = $data['ticket_group_price'];
            $orderData['ticket_group_quantity'] = $data['ticket_group_quantity'];
        }

        if (array_key_exists('ticket_preferential_quantity', $data))
        {
            $equalPrice += $data['ticket_preferential_price'] * $data['ticket_preferential_quantity'];

            $orderData['ticket_preferential_price']    = $data['ticket_preferential_price'];
            $orderData['ticket_preferential_quantity'] = $data['ticket_preferential_quantity'];
        }

        // Попытка забронировать билет
        $ticketResponse = $this->attemptBooking($orderData);
        if (isset($ticketResponse['message']) && $ticketResponse['message'] === 'order successfully booked')
        {
            $approvalResponse = $this->fakeController->approveOrder($barcode);
            if (isset($approvalResponse['message']) && $approvalResponse['message'] === 'order successfully approved')
            {
                $data = [
                    'equal_price' => $equalPrice,
                    'created'     => $created
                ];

                $mergeData = array_merge($orderData, $data);

                // Создание заказа
                $obj = $this->order::create($mergeData);

                // Создание билетов для заказа
                if (!empty($orderData['ticket_adult_quantity']))
                {
                    $this->createTickets($obj, 'adult', $orderData['ticket_adult_price']);
                }

                if (!empty($orderData['ticket_kid_quantity']))
                {
                    $this->createTickets($obj, 'kid', $orderData['ticket_kid_price']);
                }

                if (!empty($orderData['ticket_group_quantity']))
                {
                    $this->createTickets($obj, 'group', $orderData['ticket_group_price']);
                }

                if (!empty($orderData['ticket_preferential_quantity']))
                {
                    $this->createTickets($obj, 'preferential', $orderData['ticket_preferential_price']);
                }

                $this->response = ['message' => 'Order successfully booked and approved.'];
            }
            else
            {
                $this->response = ['error' => $approvalResponse['error']];
            }
        }
        else
        {
            $this->response = ['error' => $ticketResponse['error']];
        }
    }

    /**
     * Сохранение билетов в БД с уникальным баркодом для каждого заказа
     *
     * @param   object  $order
     * @param   string  $type
     * @param   int     $price
     *
     * @return void
     */
    private function createTickets(object $order, string $type, int $price): void
    {
        $this->ticket::create([
            'order_id'     => $order->id,
            'event_id'     => $order->event_id,
            'ticket_type'  => $type,
            'ticket_price' => $price,
            'barcode'      => Str::uuid(),
        ]);
    }

    /**
     * Метод для генерации уникального barcode
     *
     * @return string
     */
    private function generateUniqueBarcode(): string
    {
        return Str::uuid();
    }

    /**
     * Бронирование билетов
     *
     * @param   array  $orderData
     *
     * @return string[]
     */
    private function attemptBooking(array $orderData): array
    {
        $ticketResponse = $this->fakeController->bookOrder($orderData);

        if (isset($ticketResponse['error']) && $ticketResponse['error'] === 'barcode already exists')
        {
            $orderData['barcode'] = $this->generateUniqueBarcode();
            $ticketResponse       = $this->fakeController->bookOrder($orderData);
        }

        return $ticketResponse;
    }
}
