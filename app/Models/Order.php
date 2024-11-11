<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_date',
        'ticket_adult_price',
        'ticket_adult_quantity',
        'ticket_kid_price',
        'ticket_kid_quantity',
        'ticket_group_price',
        'ticket_group_quantity',
        'ticket_preferential_price',
        'ticket_preferential_quantity',
        'barcode',
        'equal_price',
        'created',
    ];

    use HasFactory;

    public $timestamps = false;

    public function ticket(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
