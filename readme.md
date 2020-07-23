# opendata-client
eGov.kz opendata client

## usage
```php

use Svs\Client\OpenData;

$apiKey = '#########################';
$client = new OpenData\Client($apiKey);
$result = $client
    ->{'GBDUL'}
    ->getLegalEntityByBin('###########')
;

echo json_encode($result->getBody()->getContents(), JSON_PRETTY_PRINT);
```

produces (for example):

```json
[
    {
        "statuskz": "####",
        "okedru": "####",
        "id": "####",
        "statusru": "####",
        "datereg": "2018-02-03",
        "okedkz": "####",
        "namekz": "####",
        "nameru": "####",
        "bin": "####",
        "director": "####",
        "addressru": "####",
        "addresskz": "####"
    }
]
```
