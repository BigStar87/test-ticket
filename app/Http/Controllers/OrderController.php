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
     * @param   int     $event_id                      //ID события
     * @param   string  $event_date                    //дата и время на которое были куплены билеты
     * @param   int     $ticket_adult_price            //цена взрослого билета
     * @param   int     $ticket_adult_quantity         //количество купленных взрослых билетов
     * @param   int     $ticket_kid_price              //цена детского билета
     * @param   int     $ticket_kid_quantity           //количество купленных детских билетов
     * @param   int     $ticket_preferential_price     //цена льготного билета
     * @param   int     $ticket_preferential_quantity  //количество купленных льготных билетов
     * @param   int     $ticket_group_price            //цена группового билета
     * @param   int     $ticket_group_quantity         //количество купленных групповых билетов
     *
     * @return void
     */
    public function store(int    $event_id,
                          string $event_date,
                          int    $ticket_adult_price,
                          int    $ticket_adult_quantity,
                          int    $ticket_kid_price,
                          int    $ticket_kid_quantity,
                          int    $ticket_preferential_price,
                          int    $ticket_preferential_quantity,
                          int    $ticket_group_price,
                          int    $ticket_group_quantity): void
    {
        $barcode    = $this->generateUniqueBarcode();
        $equalPrice = ($ticket_adult_price * $ticket_adult_quantity) + ($ticket_kid_price * $ticket_kid_quantity);
        $created    = now();

        $orderData = [
            'event_id'                     => $event_id,
            'event_date'                   => $event_date,
            'ticket_adult_price'           => $ticket_adult_price,
            'ticket_adult_quantity'        => $ticket_adult_quantity,
            'ticket_kid_price'             => $ticket_kid_price,
            'ticket_kid_quantity'          => $ticket_kid_quantity,
            'ticket_group_price'           => $ticket_group_price,
            'ticket_group_quantity'        => $ticket_group_quantity,
            'ticket_preferential_price'    => $ticket_preferential_price,
            'ticket_preferential_quantity' => $ticket_preferential_quantity,
            'barcode'                      => $barcode,
        ];

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
                if (!empty($ticket_adult_quantity))
                {
                    $this->createTickets($obj, 'adult', $ticket_adult_price);
                }

                if (!empty($ticket_kid_quantity))
                {
                    $this->createTickets($obj, 'kid', $ticket_kid_price);
                }

                if (!empty($ticket_group_quantity))
                {
                    $this->createTickets($obj, 'group', $ticket_kid_price);
                }

                if (!empty($ticket_preferential_quantity))
                {
                    $this->createTickets($obj, 'preferential', $ticket_kid_price);
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
