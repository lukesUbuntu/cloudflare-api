
## cloudflare-api
Just started this class not ready will add to examples for each completed method

php binding for cloudflare api v4



### examples

```php
$api = new cloudflare_api('example@example.com','agdegbhj1d0fe343dff8fddcb30131');


//get_zones
$result = $api->get_zones();

//dns_records
$identifier = $api->identifier('example.com');
$result = $api->dns_records($identifier);

//get_dns_record
$result = $api->get_dns_record($identifier,'A','example.com');


//create_dns_record | eg A record for ftp.example.com to point to 127.0.0.1
$identifier = $api->identifier('example.com');
$result = $api->create_dns_record($identifier,'A','ftp','127.0.0.1');

//get_dns_record_id
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.example.com');

//update_dns_record
$identifier = $api->identifier('example.com');
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.example.com');
$response = $api->update_dns_record($identifier,$dns_record_id,'A','ftp','127.0.1.1');

//delete_dns_record
$identifier = $api->identifier('example.com');
$dns_record_id = $api->get_dns_record_id($identifier,'A','ftp.example.com');
$response = $api->delete_dns_record($identifier,$dns_record_id);

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

//analytics
$analytics = $api->analytics($identifier,-10080, 0);

/**
* User Section
*/
//get_user_details 
$user_details = $api->get_user_details();

//update_user_details
$response = $api->update_user_details("first_name","last_name","telephone","country","zipcode");

```
