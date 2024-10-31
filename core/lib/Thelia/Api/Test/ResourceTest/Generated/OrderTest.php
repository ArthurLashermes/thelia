<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class OrderTest extends WebTestCase
{
    public function test__api__admin_orders_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/orders";
    $body = ["orderProducts" => [["orderProductTaxes" => [["title" => $faker->text(maxNbChars: 20),"description" => $faker->text(maxNbChars: 20),"amount" => $faker->randomFloat(2),"promoAmount" => $faker->randomFloat(2)],],"productRef" => $faker->text(maxNbChars: 20),"productSaleElementsRef" => $faker->text(maxNbChars: 20),"productSaleElementsId" => $faker->numberBetween(0,10),"quantity" => $faker->numberBetween(0,10),"price" => $faker->randomFloat(2),"promoPrice" => $faker->randomFloat(2),"wasNew" => $faker->boolean(),"wasInPromo" => $faker->boolean(),"weight" => $faker->text(maxNbChars: 20),"eanCode" => $faker->text(maxNbChars: 20),"taxRuleTitle" => $faker->text(maxNbChars: 20),"taxRuleDescription" => $faker->text(maxNbChars: 20),"parent" => $faker->numberBetween(0,10),"virtual" => $faker->boolean(),"virtualDocument" => $faker->boolean()],],"orderProductTax" => [["title" => $faker->text(maxNbChars: 20),"description" => $faker->text(maxNbChars: 20),"amount" => $faker->randomFloat(2),"promoAmount" => $faker->randomFloat(2)],],"orderCoupons" => [["code" => $faker->text(maxNbChars: 20),"type" => $faker->text(maxNbChars: 20),"amount" => $faker->randomFloat(2),"title" => $faker->text(maxNbChars: 20),"expirationDate" => $faker->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z'),"isCumulative" => $faker->boolean(),"isRemovingPostage" => $faker->boolean(),"isAvailableOnSpecialOffers" => $faker->boolean(),"serializedConditions" => $faker->text(maxNbChars: 20),"perCustomerUsageCount" => $faker->boolean(),"usageCanceled" => $faker->boolean()],],"invoiceOrderAddress" => ["id" => 1],"deliveryOrderAddress" => ["id" => 1],"paymentModule" => ["id" => 1],"deliveryModule" => ["id" => 1],"orderStatus" => ["id" => 1],"customer" => ["id" => 1],"currency" => ["id" => 1],"lang" => ["id" => 1],"invoiceDate" => $faker->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z'),"currencyRate" => $faker->randomFloat(2),"discount" => $faker->randomFloat(2),"postage" => $faker->randomFloat(2),"postageTax" => $faker->randomFloat(2),"postageTaxRuleTitle" => $faker->text(maxNbChars: 20),"transactionRef" => $faker->text(maxNbChars: 20),"deliveryRef" => $faker->text(maxNbChars: 20),"invoiceRef" => $faker->text(maxNbChars: 20),"cartId" => $faker->numberBetween(0,10)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

//Entry point
}
