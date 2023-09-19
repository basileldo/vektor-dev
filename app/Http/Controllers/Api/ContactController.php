<?php

namespace App\Http\Controllers\Api;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Mail;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Utilities\Formatter;

class ContactController extends ApiController
{
    public function handleFileUpload(Request $request)
    {
        try {
            $path = $request->file('file')->store('files');

            return $this->response([
                'success' => true,
                'data' => [
                    'file_name' => str_replace('files/', '', $path),
                    'file_path' => url($path),
                ],
            ]);
        } catch (\Exception $e) {
        }

        return $this->response([
            'error' => true,
        ]);
    }

    public function handle(Request $request)
    {
        $data = [
            'title' => $request->input('title'),
            'first_name' => Formatter::name($request->input('first_name')),
            'last_name' => Formatter::name($request->input('last_name')),
            'email' => Formatter::email($request->input('email')),
            'phone' => Formatter::phone($request->input('phone')),
            'newsletter' => $request->input('newsletter'),
            'light' => $request->input('light'),
            'callback' => $request->input('callback'),
            'address_line_1' => Formatter::name($request->input('address_line_1')),
            'address_line_2' => Formatter::name($request->input('address_line_2')),
            'city' => Formatter::name($request->input('city')),
            'county' => Formatter::name($request->input('county')),
            'postcode' => Formatter::postcode($request->input('postcode')),
            'country' => $request->input('country'),
            'message' => $request->input('message'),
        ];

        Mail::to(config('app.company.email'))->send(new ContactFormSubmitted($data));

        return $this->response([
            'success' => true,
            'success_message' => 'The form submitted successfully',
        ]);
    }
}
