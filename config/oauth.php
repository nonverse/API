<?php

return [
    /**
     * RS256 Private key file
     */
    'private_key' => file_get_contents('../storage/oauth-private.key'),
    //TODO OAuth should use it's own keys. JWTs that are not related to OAuth should be signed using a separate key

    /**
     * RS256 Public key file
     */
    'public_key' => file_get_contents('../storage/oauth-public.key'),
];
