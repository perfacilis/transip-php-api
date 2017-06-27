# transip-php-api
PHP wrapper for the new Transip API, which uses JSON web tokens. 

@Author

## Transip API documentation
The latest official [Transip API documention](https://api.transip.nl/rest/docs.html?_ga=2.63831983.1856092536.1498598136-604397782.1486557205)
lists all available methods and corresponding paramters. These methods shall be
converted into self containing classes so POST and GET requests can easily be 
done without havving to check if you've got all nessasary data added.

## Set up JSON Web Tokens 
As defined by [JWT.io](https://jwt.io/) and [Web Standard RFC 7519](https://tools.ietf.org/html/rfc7519)
all transactions must be signed by a temporary token. The lifetime of each token
can be defined up until one month, allthough a shorter lifetime obviously is 
more secure.

Due to the lifetime of each given token, the tokens must be refreshed 
frequently. To ease this process, the API shall include a JWT handler to get 
this done for you.

## Getting started.
1. Include the autoload.php
2. …

## Author
- Roy Arisse (support@perfacilis.com)
