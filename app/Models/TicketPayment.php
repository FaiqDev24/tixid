<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPayment extends Model
{
    use SoftDeletes;

    protected $fillable = ['ticket_id', 'qrcode', 'booked_date', 'paid_date', 'status'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
