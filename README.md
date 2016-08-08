
## cloudflare-api
Just started this class not ready will add to examples for each completed method

php binding for cloudlfare api v4



### examples

```php
$api = new cloudflare_api('example@example.com','agdegbhj1d0fe343dff8fddcb30131');

//get_zones
$result = $api->get_zones();

//dns_records
$identifier = $api->identifier('example.com');
$result = $api->dns_records($identifier);

//create_dns_record | eg A record for ftp.example.com to point to 127.0.0.1
$identifier = $api->identifier('example.com');
$result = $api->create_dns_record($identifier,'A','ftp','127.0.0.1');

//purge_site
$identifier = $api->identifier('example.com');
$result = $api->purge_site($identifier);

//purge_files
$identifier = $api->identifier('example.com');
$files = [
	'http://example.com/skin.css',
    'http://example.com/skin.js'
];
$result = $api->purge_files($identifier,$files);
```
