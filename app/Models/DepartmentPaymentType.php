<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentPaymentType extends Model
{
    protected $table = 'department_payment_type';
    protected $fillable = ['department_id', 'payment_type_id','level'];
    use HasFactory;

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
