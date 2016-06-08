<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\OrderStatus;

class SeedOrderStatuses extends Seeder
{

    public function run()
    {
        OrderStatus::create([
            'code' => 'waiting',
            'name' => 'Waiting for Payment',
            // 'description' => '',
            'color' => '#F5A623',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => true,
            // 'mail_template_id' => '',
            'sort_order' => 1,
        ]);

        OrderStatus::create([
            'code' => 'expired',
            'name' => 'Expired',
            // 'description' => '',
            'color' => '#D0021B',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 2,
        ]);

        OrderStatus::create([
            'code' => 'paid',
            'name' => 'Paid',
            // 'description' => '',
            'color' => '#02D005',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => true,
            // 'mail_template_id' => '',
            'sort_order' => 3,
        ]);

        OrderStatus::create([
            'code' => 'shipped',
            'name' => 'Shipped',
            // 'description' => '',
            'color' => '#BD02D0',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 4,
        ]);

        OrderStatus::create([
            'code' => 'delivered',
            'name' => 'Delivered',
            // 'description' => '',
            'color' => '#02D005',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 5,
        ]);

        OrderStatus::create([
            'code' => 'refunded',
            'name' => 'Refunded',
            // 'description' => '',
            'color' => '#F5A623',
            'is_active' => true,
            'send_email' => false,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 6,
        ]);

        OrderStatus::create([
            'code' => 'void',
            'name' => 'Void',
            // 'description' => '',
            'color' => '#9B9B9B',
            'is_active' => true,
            'send_email' => false,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 7,
        ]);

        OrderStatus::create([
            'code' => 'closed',
            'name' => 'Closed',
            // 'description' => '',
            'color' => '#9B9B9B',
            'is_active' => true,
            'send_email' => false,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 8,
        ]);
    }

}