<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MacAddressController extends Controller
{
    public function lookup(Request $request)
    {
        $macAddresses = $request->input('mac_addresses');
        $macAddresses = preg_split('/[\s,:.-]+/', $macAddresses);

        $response = [];

        foreach ($macAddresses as $mac) {
            $mac = strtoupper(str_replace([':', '-', '.', ':', ' '], '', $mac));

            // Check if the MAC address is valid (12 characters long)
            if (strlen($mac) === 12) {
                $ouiData = DB::table('oui_data')
                    ->select('organization_name')
                    ->where('oui', '=', substr($mac, 0, 6))
                    ->first();

                if ($ouiData) {
                    $response[] = [
                        'mac_address' => $mac,
                        'vendor' => $ouiData->organization_name,
                    ];
                } else {
                    // Vendor not found in the database
                    $response[] = [
                        'mac_address' => $mac,
                        'vendor' => 'Unknown',
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
