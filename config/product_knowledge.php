<?php

return [
    'locales' => ['en', 'el', 'ru', 'ka'],
    'preferred_label_locales' => ['en', 'el', 'ru', 'ka'],
    'normalization' => [
        'max_term_length' => 191,
        'collapse_whitespace' => true,
    ],
    'google_taxonomy' => [
        'base_url' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.%s.txt',
        'default_locales' => ['en-US', 'el-GR', 'ru-RU'],
        'download_dir' => 'product-knowledge/google-taxonomy',
    ],
    'wikidata' => [
        'endpoint' => 'https://query.wikidata.org/sparql',
        'user_agent' => env('WIKIDATA_USER_AGENT', env('APP_NAME', 'Buyer Greece') . '/1.0 (product knowledge importer; contact: admin@example.com)'),
        'languages' => ['en', 'el', 'ru', 'ka'],
        'class_qids' => ['Q431289', 'Q10929058', 'Q811701'], // brand, product model, model series
        'page_size' => 200,
        'max_pages' => 15,
        'pause_microseconds' => 1200000,
        'cache_dir' => 'product-knowledge/wikidata-cache',
    ],
    'typesense' => [
        'host' => env('TYPESENSE_HOST', 'http://127.0.0.1:8108'),
        'api_key' => env('TYPESENSE_API_KEY', ''),
        'collection' => env('TYPESENSE_PRODUCT_KNOWLEDGE_COLLECTION', 'buyer_product_knowledge_v1'),
        'batch_size' => 250,
        'search_limit' => 8,
    ],
    'type_rank' => [
        'brand' => 30,
        'product_model' => 40,
        'model_series' => 35,
        'category' => 20,
        'wikidata_entity' => 10,
    ],
];
