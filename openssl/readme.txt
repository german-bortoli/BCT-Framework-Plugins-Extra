
@file
OpenSSL.

Provides various OpenSSL functionality, or will do, but at least
for now it provides public / private key authentication for the API service
connection.

@section API connection authentication.

This works by passing header variables when sending your query, these
are:

- OPENSSL_KEY_ID : The GUID of the public key or keypair object used for
verification.

- OPENSSL_API_TIMESTAMP : UNIX timestamp - this prevents replay and is
also used in the hash. Must be within ten minutes
either side.
- OPENSSL_API_HASH : Hex encoded Sha1 hash of api method + api call variables
and their values, urlencoded, and in call order + above
timestamp. E.g. $hash = sha1('method=foo&var1=bar' . time())
- OPENSSL_API_SIGNATURE : Base64 encoded signature of the hash generated
using your private key. Use OPENSSL_PKCS1_PADDING.


@package openssl
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk

