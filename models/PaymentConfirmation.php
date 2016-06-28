<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * PaymentConfirmation Model
 */
class PaymentConfirmation extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_payment_confirmations';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'order_no',
        'email',
        'transferr_date',
        'account_owner',
        'bank_name',
        'transfer_amount',
        'destination_account',
        'notes'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}