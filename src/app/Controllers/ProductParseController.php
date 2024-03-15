<?php

namespace Slim\Mpstats\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class ProductParseController extends Controller
{
    public function parse(Request $request, Response $response, array $args)
    {
        $ts = time();
        $client = new Client;
        $url = "https://search.wb.ru/exactmatch/ru/common/v4/search";

        $words = [
            "джинсы",
            "платье",
            "футболка",
        ];

        $params = [
            "ab_testing" => false,
            "appType" => 1,
            "curr" => "rub",
            "dest" => "-1257786",
            'query' => 'some',
            "resultset" => "catalog",
            "sort" => "popular",
            "spp" => "30",
            "suppressSpellcheck" => false,
        ];

        $collectionsArr = [];
        foreach ($words as $word){
            $params["query"] = $word;
            do{
                $result = $client->get($url, [
                    'query' => $params,
                    'headers' => [
                        'Accept' => '*/*',
                        'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                        'Accept-Encoding' => 'gzip, deflate, br',
                        'Connection' => 'keep-alive',
                        'Origin' => 'https://www.wildberries.ru',
                        'Referer' => 'https://www.wildberries.ru/catalog/0/search.aspx?search='.$word,
                        'Sec-Ch-Ua' => '"Not A(Brand";v="99", "Google Chrome";v="121", "Chromium";v="121"',
                        'Sec-Ch-Ua-Mobile' => '?0',
                        'Sec-Fetch-Dest' => 'empty',
                        'Sec-Fetch-Mode' => 'cors',
                        'Sec-Fetch-Site' => 'cross-site',
                        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                        'x-queryid' => 'qid117285924170860727820240314113707',
                        'sec-ch-ua-platform' => '"Linux"',

                    ]
                ]);
                if($result->getStatusCode() !== 200){
                    throw new \Exception("bad response");
                }
                $result = json_decode($result->getBody()->getContents(), true);
            }
            while (count($result['data']['products']) < 100);

            foreach ($result['data']['products'] as $key => $product){
                $position = $key + 1;

                $collectionsArr[] = "(" . implode(",", [
                    'ts' => $ts,
                    'q' => "'".$word. "'",
                    'id' => $product['id'],
                    'position' => $position,
                ]) . ")";

            }

        }

        $insertData = implode(",", $collectionsArr);


        $this->db->write("INSERT INTO product_collection (ts, q, id, position) VALUES $insertData");

        $response->getBody()->write(json_encode([
            'status' => true,
            'message' => "Parsing data success completed",
        ]));


       return $response;
    }

    public function get(Request $request, Response $response, array $args)
    {
        $queryParams = $request->getQueryParams();
        if(empty($queryParams['q'])){
            $response->getBody()->write(json_encode([
                'status' => false,
                'message' => "Missing required get param 'q'",
            ]));

        }else{
            $data = $this->db->select("SELECT * FROM product_collection WHERE q = :q ORDER BY ts DESC LIMIT 100 ", ['q' => $queryParams['q']]);


            $response->getBody()->write(json_encode([
                'status' => true,
                'message' => "",
                'result' => $data->rows()
            ]));
        }

        return $response;
    }
}