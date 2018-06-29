<?php namespace Octommerce\Octommerce\Models;

use Model;
use System\Models\MailTemplate;
// use Octommerce\Octommerce\Models\Order;

/**
 * OrderStatus Model
 */
class OrderStatus extends Model
{
    use \October\Rain\Database\Traits\SimpleTree;

    const PARENT_ID = 'parent_code';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_order_statuses';

    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'code';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'orders' => [
            'Octommerce\Octommerce\Models\Order',
            'key' => 'status_code',
            'otherKey' => 'code'
        ]
    ];
    public $belongsTo = [
        'mail_template'       => 'System\Models\MailTemplate',
        'admin_mail_template' => 'System\Models\MailTemplate',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
