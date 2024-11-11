<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_id',
        'ticket_type',
        'ticket_price',
        'barcode',
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketTypes(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'event_id', 'event_id');
    }
}
