# Storeden Connect API for PHP

This repository contains the PHP Connect SDK that make your PHP app consume the Storeden Connect API.

## Setup

Storeden Connect SDK can be installed:

```sh
git clone https://github.com/storeden/connect-api-sdk.git
```

## Example

```php
$conf = [
    'api_key' => '{your-app-key}',
    'api_exchange' => '{your-app-exchange}'
];

$api = new Storeden\Storeden($config);

$_store_info = $api->get('/store/info.json');

echo 'Store Name: '.$_store_info->response->store_name;

```

## License

For more information, please see the [license file](https://github.com/storeden/connect-api-sdk/blob/master/LICENSE).
