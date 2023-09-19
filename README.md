Test Setup

Test 1:
    For imports the latest version of the IEEE OUI data into
    a database(s).
    Use command - php artisan import:oui-data 

Test 2:
    Example GET request for a single MAC lookup:
    
    -  GET /api/mac-lookup?mac_addresses=001122334455

    Example POST request for multiple MAC lookup:
    
        POST /api/mac-lookup
        {
            "mac_addresses": ["001122334455", "AABBCCDDEEFF"]
        }

    
