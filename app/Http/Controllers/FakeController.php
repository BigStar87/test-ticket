<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class FakeController extends Controller
{
    /**
     * Замокированный метод для бронирования заказа
     *
     * @param   array  $orderData
     *
     * @return string[]
     */
    public function bookOrder(array $orderData): array
    {
        // Замокированный ответ от API
        Http::fake([
            'https://api.site.com/book' => Http::response(['message' => 'order successfully booked']),
        ]);
        $response = Http::post('https://api.site.com/book', $orderData);

        // Случайный ответ
        $mockResponses = [
            ['message' => 'order successfully booked'],
//            ['error' => 'barcode already exists'],
        ];

        return $mockResponses[array_rand($mockResponses)];
    }

    /**
     * Метод для подтверждения заказа
     *
     * @param   string  $barcode
     *
     * @return string[]
     */
    public function approveOrder(string $barcode): array
    {
        // Замокированный ответ от API
        Http::fake([
            'https://api.site.com/approve' => Http::response(['message' => 'order successfully approve']),
        ]);
        $response = Http::post('https://api.site.com/approve', ['barcode' => $barcode]);

        // Случайный ответ
        $mockResponses = [
            ['message' => 'order successfully approved'],
//            ['error' => 'event cancelled'],
//            ['error' => 'no tickets'],
//            ['error' => 'no seats'],
//            ['error' => 'fan removed'],
        ];

        return $mockResponses[array_rand($mockResponses)];
    }
}
