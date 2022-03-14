<?php
namespace App\Tests;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HomeTest extends KernelTestCase
{
    public function testProduct()
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'http://127.0.0.1:8000/');

        $all_product = json_decode($response->getContent(), true);
        $this->assertEquals('£174.00', $all_product[0]['price']);
        $this->assertEquals('Optimum: 24GB Data - 1 Year', $all_product[0]['option title']);
        $this->assertEquals('Save £17.90 on the monthly price', $all_product[0]['discount']);

        $this->assertEquals('£5.99', $all_product[5]['price']);
        $this->assertEquals('Basic: 500MB Data - 12 Months', $all_product[5]['option title']);
        $this->assertEquals(false, (!empty($all_product[5]['discount']) ? true : false));
    }
}
