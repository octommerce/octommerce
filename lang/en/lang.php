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
                'usecategoryfilter_param_title' => 'Use category filter',
                'usecategoryfilter_param_desc' => 'Check if you want to use the category filter function',
                'categoryfilter_param_title' => 'Category filter',
                'categoryfilter_param_desc' => 'Select a category to filter the product list by. Leave empty to show all products.',
                'product_page_title' => 'Product page',
                'product_page_desc' => 'Name of the product page file for the "Learn more" links. This property is used by the default component partial.',
                'product_page_id_title' => 'Product page param name',
                'product_page_id_desc' => 'The expected parameter name used when creating links to the product page.',
                'no_product_title' => 'No products message',
                'no_product_desc' => 'Message to display in the product list in case if there are no products. This property is used by the default component partial.',
                'products_per_page_title' => 'Products per page',
                'products_per_page_validation_message' => 'Invalid format of the products per page value',
                'page_param_title' => 'Pagination parameter name',
                'page_param_desc' => 'The expected parameter name used by the pagination pages.',
            ],
        ],
    ],
];