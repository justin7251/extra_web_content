<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;


class HomeController {
    /**
     * Function return Packages price in json format
     */
    public function index() {
        $data = $this->fetchInformation();
        $response = new JsonResponse($data);
        $response->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_UNESCAPED_UNICODE);
        return $response;
    }

     /**
     * Function fetach product information and order by the highest price
     */
    public function fetchInformation()
    {
        $httpClient = new \GuzzleHttp\Client();
        $response = $httpClient->get('https://wltest.dns-systems.net');
        $htmlString = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new \DOMXPath($doc);
        $titles = $xpath->evaluate('//*[@id="subscriptions"]/div/div[4]/div/div/div/div[1]/h3');
        $description = $xpath->evaluate('//*[@id="subscriptions"]/div/div[4]/div/div/div/div[2]/ul/li[1]/div');
        $prices = $xpath->evaluate('//*[@id="subscriptions"]/div/div[4]/div/div/div/div[2]/ul/li[3]/div/span');
        $discount = $xpath->evaluate('//*[@id="subscriptions"]/div/div[4]/div/div/div/div/ul/li[3]/div/p');
        $content = array();
        // ‘option title’, ‘description’, ‘price’ and ‘discount’
        foreach ($titles as $key => $title) {
            $content[$key]['option title'] = $title->textContent;
        }
        foreach ($description as $key => $desc) {
            $content[$key]['description'] = $desc->textContent;
        }
        $annuel_product = 0;
        foreach ($prices as $key => $price) {
            if (str_replace('£', '', $price->textContent) > 60) {
                $annuel_product++;
            }
            $content[$key]['price'] = $price->textContent;
        }
        foreach ($discount as $key => $dis) {
            $content[$key + $annuel_product]['discount'] = $dis->textContent;
        }
        usort(
            $content,
            function ($a, $b) {
                $a = str_replace('£', '', $a['price']);
                $b = str_replace('£', '', $b['price']);
                return $a <=> $b;
            }
        );
        return array_reverse($content);
    }
}