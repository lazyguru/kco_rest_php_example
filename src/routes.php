<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.html', []);;
});
$app->get('/terms', function ($request, $response, $args) {
    return 'These are terms';
});
$app->get('/info', function ($request, $response, $args) {
    /** @noinspection ForgottenDebugOutputInspection */
    return phpinfo();
});
$app->get('/start', function ($request, $response, $args) {
    $settings = $this->get('settings')['klarna'];
    $connector = \Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $checkout = new \Klarna\Rest\Checkout\Order($connector);
    $checkout->create([
        "purchase_country"  => "us",
        "purchase_currency" => "usd",
        "locale"            => "en-us",
        "order_amount"      => 10000,
        "order_tax_amount"  => 2000,
        "order_lines"       => [
            [
                "type"             => "physical",
                "reference"        => "123050",
                "name"             => "Tomatoes",
                "quantity"         => 10,
                "quantity_unit"    => "kg",
                "unit_price"       => 600,
                "tax_rate"         => 2500,
                "total_amount"     => 6000,
                "total_tax_amount" => 1200
            ],
            [
                "type"                  => "physical",
                "reference"             => "543670",
                "name"                  => "Bananas",
                "quantity"              => 1,
                "quantity_unit"         => "bag",
                "unit_price"            => 5000,
                "tax_rate"              => 2500,
                "total_amount"          => 4000,
                "total_discount_amount" => 1000,
                "total_tax_amount"      => 800
            ]
        ],
        "merchant_urls"     => [
            "terms"        => 'https://' . $request->getServerParam('HTTP_HOST') . '/terms',
            "checkout"     => 'https://' . $request->getServerParam('HTTP_HOST') . '/checkout/{checkout.order.id}',
            "confirmation" => 'https://' . $request->getServerParam('HTTP_HOST') . '/success/{checkout.order.id}',
            "push"         => 'https://' . $request->getServerParam('HTTP_HOST') . '/push/{checkout.order.id}'
        ]
    ]);

    $order = $checkout->fetch();

    // Store checkout order id
    $orderId = $order->getId();

    return $response->withStatus(302)->withHeader('Location', '/checkout/' . $orderId);
})->setName('start');

$app->get('/prefill', function ($request, $response, $args) {
    $settings = $this->get('settings')['klarna'];
    $connector = \Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $checkout = new \Klarna\Rest\Checkout\Order($connector);
    $checkout->create([
        "purchase_country"  => "us",
        "purchase_currency" => "usd",
        "locale"            => "en-us",
        "order_amount"      => 10000,
        "order_tax_amount"  => 2000,
        "order_lines"       => [
            [
                "type"             => "physical",
                "reference"        => "123050",
                "name"             => "Tomatoes",
                "quantity"         => 10,
                "quantity_unit"    => "kg",
                "unit_price"       => 600,
                "tax_rate"         => 2500,
                "total_amount"     => 6000,
                "total_tax_amount" => 1200
            ],
            [
                "type"                  => "physical",
                "reference"             => "543670",
                "name"                  => "Bananas",
                "quantity"              => 1,
                "quantity_unit"         => "bag",
                "unit_price"            => 5000,
                "tax_rate"              => 2500,
                "total_amount"          => 4000,
                "total_discount_amount" => 1000,
                "total_tax_amount"      => 800
            ]
        ],
        "billing_address"   => [
            "city"            => 'Columbus',
            "country_code"    => "US",
            "email"           => 'checkout@testdrive.klarna.com',
            "family_name"     => 'Doe',
            "given_name"      => 'John',
            "phone_number"    => '844-552-7621',
            "postal_code"     => '43215',
            "region"          => 'OH',
            "street_address"  => '629 N. High St, #300',
            "street_address2" => null,
            "title"           => 'Mr',
        ],
        "customer"          => [
            "date_of_birth" => '1990-01-01',
            "type"          => 'person',
        ],
        "merchant_urls"     => [
            "terms"        => 'https://' . $request->getServerParam('HTTP_HOST') . '/terms',
            "checkout"     => 'https://' . $request->getServerParam('HTTP_HOST') . '/checkout/{checkout.order.id}',
            "confirmation" => 'https://' . $request->getServerParam('HTTP_HOST') . '/success/{checkout.order.id}',
            "push"         => 'https://' . $request->getServerParam('HTTP_HOST') . '/push/{checkout.order.id}'
        ]
    ]);

    $order = $checkout->fetch();

    // Store checkout order id
    $orderId = $order->getId();

    $this->flash->addMessage('Prefill', 'You were prefilled');
    return $response->withStatus(302)->withHeader('Location', '/checkout/' . $orderId);
})->setName('prefill');

$app->get('/checkout/{id}', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $settings = $this->get('settings')['klarna'];
    $connector = Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $checkout = new \Klarna\Rest\Checkout\Order($connector, $args['id']);

    $order = $checkout->fetch();

    return $this->view->render($response, 'checkout.html', [
        'snippet'  => $order->offsetGet('html_snippet'),
        'messages' => $messages
    ]);
})->setName('checkout');

$app->get('/success/{id}', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $settings = $this->get('settings')['klarna'];
    $connector = Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $checkout = new \Klarna\Rest\Checkout\Order($connector, $args['id']);

    $order = $checkout->fetch();

    return $this->view->render($response, 'success.html', [
        'snippet'  => $order->offsetGet('html_snippet'),
        'order_id' => $args['id'],
    ]);
})->setName('success');

$app->post('/push/{id}', function ($request, $response, $args) {
    $settings = $this->get('settings')['klarna'];
    $connector = Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $order = new Klarna\Rest\OrderManagement\Order($connector, $args['id']);
    $order->acknowledge();

    return 'ok';
})->setName('push');

$app->get('/capture/{id}', function ($request, $response, $args) {
    $settings = $this->get('settings')['klarna'];
    $connector = Klarna\Rest\Transport\Connector::create(
        $settings['merchantId'],
        $settings['sharedSecret'],
        \Klarna\Rest\Transport\ConnectorInterface::NA_TEST_BASE_URL
    );

    $order = new Klarna\Rest\OrderManagement\Order($connector, $args['id']);
    $capture = $order->createCapture([
        'captured_amount' => 10000,
        'order_lines'     => [
            [
                'type'             => 'physical',
                'reference'        => '123050',
                'name'             => 'Tomatoes',
                'quantity'         => 10,
                'quantity_unit'    => 'kg',
                'unit_price'       => 600,
                'tax_rate'         => 2500,
                'total_amount'     => 6000,
                'total_tax_amount' => 1200
            ],
            [
                'type'                  => 'physical',
                'reference'             => '543670',
                'name'                  => 'Bananas',
                'quantity'              => 1,
                'quantity_unit'         => 'bag',
                'unit_price'            => 5000,
                'tax_rate'              => 2500,
                'total_amount'          => 4000,
                'total_discount_amount' => 1000,
                'total_tax_amount'      => 800
            ]
        ],
        'shipping_info'   => [
            [
                'shipping_company' => 'USPS',
                'tracking_number'  => '1Z.....'
            ]
        ]
    ]);

    return $this->view->render($response, 'capture.html', [
        'capture'  => $capture->fetch()
    ]);

})->setName('capture');
