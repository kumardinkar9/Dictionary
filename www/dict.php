<?php
    require __DIR__ . '/vendor/autoload.php';

    use \Curl\Curl;

    function getWordDefinition($dict) {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
        $curl->setOpt(CURLOPT_HTTPHEADER , array(
                "accept: application/json",
                "app_id: 7e4b8496",
                "app_key: 3d599136eefd684f51318f914a117e3e"
              ));
        $curl->get('https://od-api.oxforddictionaries.com:443/api/v1/entries/en/'.$dict.'');
        $result = '';
        $finalData = [];

        if ($curl->error) {
            return 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            $result = $curl->response->results;
            foreach ($result as $key => $value) {
                foreach ($value->lexicalEntries as $key1 => $value1) {
                    $finalData[$value1->lexicalCategory]['pronunciations'] = $value1->pronunciations;
                    if (isset($value1->entries[0]->senses) && is_array($value1->entries[0]->senses)) {
                        foreach ($value1->entries[0]->senses as $key2 => $value2) {
                            $finalData[$value1->lexicalCategory]['definitions'][]  = $value2->definitions[0];
                            if (isset($value2->examples) && is_array($value2->examples)) {
                                foreach ($value2->examples as $key3 => $value3) {
                                    $finalData[$value1->lexicalCategory]['examples'][]  = $value3->text;
                                }
                            }
                        }
                    }
                }
            }  
        }
        echo json_encode($finalData);
    }
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        
            {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    $postdata = file_get_contents("php://input");
    if (isset($postdata)) {
        $request = json_decode($postdata);
        $word = isset($request->word) ? $request->word : '';

        if ($word != "") {
            getWordDefinition($word);
        }
        else {
            echo "Empty word parameter!";
        }
    }
    else {
        echo "Not called properly with word parameter!";
    }