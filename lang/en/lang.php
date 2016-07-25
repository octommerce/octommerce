<?php return [
    'plugin' => [
        'name' => 'Octommerce',
        'description' => 'This is the most awesome e-commerce plugin.',
    ],
    'component' => [
    	'product_list' => [
            'name' => 'Product List',
            'description' => 'Display a list of products',
            'param' => [
                'category_param_title' => 'Dynamic category',
                'category_param_desc' => 'Get the category from parameter.',
                'categoryfilter_param_title' => 'Category filter',
                'categoryfilter_param_desc' => 'Select a category to filter the product list by. Leave empty to show all products.',
                'listfilter_param_title' => 'List filter',
                'listfilter_param_desc' => 'Select a lilst to filter the product list by. Leave empty to show all products.',
                'brandfilter_param_title' => 'Brand filter',
                'brandfilter_param_desc' => 'Select a brand to filter the product list by. Leave empty to show all products.',
                'hide_out_of_stock_title' => 'Hide out of stock',
                'hide_out_of_stock_desc' => 'Don\'t show products that out of stock.',
                'no_product_title' => 'No products message',
                'no_product_desc' => 'Message to display in the product list in case if there are no products. This property is used by the default component partial.',
                'products_per_page_title' => 'Products per page',
                'products_per_page_validation_message' => 'Invalid format of the products per page value',
                'page_param_title' => 'Pagination parameter name',
                'page_param_desc' => 'The expected parameter name used by the pagination pages.',
            ],
        ],
        'product_detail' => [
            'name' => 'Product Detail',
            'description' => 'Details of displayed product',
            'param' => [
                'id_param_title' => 'Slug param name',
                'id_param_desc' => 'The URL route parameter used for looking up the product by its slug.',
            ],
        ],
    ],
];