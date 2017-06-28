# transip-php-api

WARNING:
THIS API IS CURRENTLY IN DEVELOPMENT STAGE; THEREFORE ONLY USE IT AS A CODE EXAMPLE OR FOR TESTING PURPOSES.

PHP wrapper for the new Transip API, which uses JSON web tokens.
The API is based upon the [TransIP API v5.4](https://www.transip.nl/transip/api/), though due to changes in the
available endpoints some methods and their return values have changed.

## Transip API documentation
The latest official [Transip API documention](https://api.transip.nl/rest/docs.html?_ga=2.63831983.1856092536.1498598136-604397782.1486557205)
lists all available methods and corresponding paramters. These methods shall be converted into self containing classes
so requests can easily be done without having to check if you've got all nessasary data added.

## Set up JSON Web Tokens 
As defined by [JWT.io](https://jwt.io/) and [Web Standard RFC 7519](https://tools.ietf.org/html/rfc7519) all transactions
must be signed by a temporary token. The lifetime of each token can be defined up until one month, allthough a shorter
lifetime obviously is more secure.

Due to the lifetime of each given token, the tokens must be refreshed frequently. To ease this process, the API shall
include a JWT handler to get this done for you.

## Getting started.
1. Download the [authenticatie class](https://api.transip.nl/downloads/TransIP_AccessToken.tar.gz?_ga=2.195528682.714284166.1498641216-602751744.1497268499)
   and save the TransIP_AccessToken.php into the root of the Package.
   When the link doesn't work, login to your CP and navigate to Account » API.
2. In TransIP_AccessToken.php, set both the `LOGIN` and `PRIVATE_KEY` constants, with your username and the private key
   you get by generating a new Key Pair in the CP sequentially.
3. See one of the examples on how to call the API.

## Known issues
1. The API currently generates a lot of Access tokens. Perhaps there's a way to cache these tokens, as long as it won't
   compromise the security.
2. Not all available endpoints are converted into classes and methods yet.

## Author
- Roy Arisse (support@perfacilis.com)
