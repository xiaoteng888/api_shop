<?php

return [
    'alipay' => [
        'app_id'         => '2016101500689325',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjdSo9YFcUEJFjVyx7YLjaLlhHeCh3bLFuwb7GkDZSx6OGKMaBqhXPEAfg4Y2ubXBhMlNWXixO5XGJzFvY6D6czDLt+icISgT387qCK1N7A9CHLEajvbbG4ON2GnDvUKcdgfh28NU4aHx/BF+DRgf7GsaoJfjx8MZf7JNG/+kKEfZ3O9d73bKlGCj55FzU1/ny4np7c4lUiyXfsOmFOA7pswScuk170ovgXhUl7IiofpsN4OUGyt8diO5cvj7MZEqEsSoScTGOI17VAzPHqf30v8MtkTj0ZOeesK5SZsp5oDOJP5VGQXOeFSYwBscwf/rOE2yTJsdy7azm9CMjslSQwIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEAh994OGch9IFWL/dAfHCVQXuuTWtWuiR2A+sutnHLw0h7ONdCZHYhGZpo5zOw8vq7/Lq7abNgLjKO/h7JybAc269nDc29jwHRk1CkfRdIlj6/Bw5kdIMvXt0XSF75vN7EQ5LUxigku6+1DoQJw7FN93T6lbj+1DEEHlf9lsdnNYw/fhsnDp1qVmKd44Ngfn2Vpsxe3R64CGrCcoz1uNtKb79MU1TqwJ7/jUog6GIi0RFgMRDY9w2R2c7qjpm/PTQBd/SODy+naFbngFXcB5AHtqk/8rGJxEviTEaqrjLLOSEd/hGOalWuhfN2d3etZjwhiKTQswr6d4DvDRrPqlOC8wIDAQABAoIBAA32ClZRUo3bFE4Na9TdhJib6scNtnWd9XplmgEpJDTGp9KpW6sS8Ee/M/Q9vXraBKLtVQCBa+qTS1oVM8rDi9OO7FngwA+Hsk8exCgOUmkNvXcuJWhLanym3xOSnrqncSc4WSCbU1rtrP721kIDX+2DWZQ01Apdlvy/MdFiX9qCjfX9QWtgwvisB4OPVganBoD/1ELZZvjkKaMD6dM1GX71Bwz/JadEnLCbWWsfCDnwzkK/RAzW1F0IVYhj1SmdfIP5JHAErFUOL0W7MLOxyyZh2IzkrMvhfY8bLW7aUK5gG7PA+yZXlG0XiBAeE89WtdyH4pnKwHBBFjk+Rxl6YxECgYEA1sJAjM2lza3PKKhoGbWyFGflCkZ71Z+nbgX4jZaEwrz09IwnZpYW6YxcGuNqNTkCVhshejOMwAR2ybyeEzC9KABOqCXxqXzpGYAy1pgiEEi6xOZdzoQPI/OCA+4PkFvKT1/X11LT4ohnhqVTSeDqsuGRUC/6S9oYl0Gx+MLuY4cCgYEAofcc2VYY5pVNRfRdScAC42b1uoWKtocOgWPt3VU+jaByX5vBhqjPjsWzBYuaqVsxoFeRl6G2JgGRgsxOCCT/eWIk2yV+TTs+MjbJSXNJ0bX43JG3f1EHitmwyVgPLNk0Drfl+DN1hsaTuWUuztMAyuiMp3PiJEY3gx7MM0rJ2DUCgYEAkSkYDJRsgAv9vJOoWchwqE5ZiIp/5bNWro2mpb2Z2y9xu2KIt1S4BAD1uM8C2RrBs4X/KORzPM9Ho7C0gHAvSzx57jpHkLJ3tN7sfhSy4aLna3Py8EZfHYPY2+Mr7f29T82OzWKVCcf9ggt5srQur968JCFp4591g7fKHkVUcT0CgYAwj+lEeP4qVOXAbc5yyHGVQU+4R0YaCFCfFxlA3chNqqfgZtEV0RO6mSheJ0eOM9ujWd47eq8koh/A+gUDiCRZfsXSN2GxQmwZnjeoIDsn98nQSJieQetjBHZvSEn0hoOHTjhTvQomCPBODDqGpNl9/U/+/U321l7an6dWUbxHaQKBgF5fKQFkFLDuxDhj1P3WpZ4maXqfOe/NErcUouzqTZrj8GDYugmS6ggPahlZfRTvMjjyN4Rp/kAQ3VcNjKwlYztbnc+Dj7//rRiEi8WmBB4zvLSeRfoorohFKstSDii6DCW9CtFOrGOes4+bI/ak/oFkypxVxVp7A99Y7VRyQmBM',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];