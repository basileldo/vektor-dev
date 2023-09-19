<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ImportOuiData extends Command
{
    protected $signature = 'import:oui-data';
    protected $description = 'Import the latest IEEE OUI data into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = 'http://standards-oui.ieee.org/oui/oui.csv';
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->body();

            // You can now parse and import the data into your database.
            // For example, you can use the Laravel Eloquent ORM or DB::insert.

            // Example using DB::insert:
            $lines = explode(PHP_EOL, $data);
            foreach ($lines as $line) {
                $fields = str_getcsv($line);
                if (count($fields) === 3) {
                    // Assuming you have a "oui_data" table with columns: oui, organization_name, and organization_address.
                    DB::table('oui_data')->insert([
                        'oui' => $fields[0],
                        'organization_name' => $fields[1],
                        'organization_address' => $fields[2],
                    ]);
                }
            }

            $this->info('IEEE OUI data imported successfully.');
        } else {
            $this->error('Failed to fetch IEEE OUI data from the source.');
        }
    }
}
