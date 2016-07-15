<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\OrderStatus;

class SeedOrderStatuses extends Seeder
{

    public function run()
    {
        OrderStatus::create([
            'code' => 'waiting',
            'name' => 'Waiting',
            'description' => 'Waiting customer to pay.',
            'color' => '#f39c12',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => true,
            // 'mail_template_id' => '',
            'sort_order' => 1,
        ]);

        OrderStatus::create([
            'code' => 'paid',
            'name' => 'Paid',
            'description' => 'Order is paid and ready to be packed.',
            'color' => '#1abc9c',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => true,
            // 'mail_template_id' => '',
            'sort_order' => 2,
            'parent_code' => 'waiting',
        ]);

        OrderStatus::create([
            'code' => 'expired',
            'name' => 'Expired',
            'description' => 'Payment timeout.',
            'color' => '#c0392b',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 3,
            'parent_code' => 'waiting',
        ]);

        OrderStatus::create([
            'code' => 'packing',
            'name' => 'Packing',
            'description' => 'Order is being packed.',
            'color' => '#8e44ad',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 4,
            'parent_code' => 'paid',
        ]);

        OrderStatus::create([
            'code' => 'void',
            'name' => 'Void',
            'description' => 'Payment canceled.',
            'color' => '#7f8c8d',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 5,
            'parent_code' => 'paid',
        ]);

        OrderStatus::create([
            'code' => 'shipped',
            'name' => 'Shipped',
            'description' => 'Items have been shipped to courier.',
            'color' => '#3498db',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 6,
            'parent_code' => 'packing',
        ]);

        OrderStatus::create([
            'code' => 'delivered',
            'name' => 'Delivered',
            'description' => 'Items have been delivered to customer.',
            'color' => '#27ae60',
            'is_active' => true,
            'send_email' => true,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 7,
            'parent_code' => 'shipped',
        ]);

        OrderStatus::create([
            'code' => 'refunded',
            'name' => 'Refunded',
            'description' => 'Order is refunded.',
            'color' => '#e74c3c',
            'is_active' => true,
            'send_email' => false,
            'attach_pdf' => false,
            // 'mail_template_id' => '',
            'sort_order' => 8,
            'parent_code' => 'delivered',
        ]);

    }

}