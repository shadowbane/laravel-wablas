<?php

return [
    /*
     * Wablas API Endpoint
     */
    'endpoint' => env('WABLAS_ENDPOINT', 'https://sawit.wablas.com/api'),

    /*
     * Your Wablas API Token
     */
    'token' => env('WABLAS_TOKEN', null),

    /*
     * Notifiable's WhatsApp number
     * Fill with your user's whatsapp column
     */
    'whatsapp_number_field' => env('WHATSAPP_NUMBER_FIELD', "personal_information"),

    /*
     * Notifiable's WhatsApp number
     * Only fill if whatsapp_number_field is JSON and you want to select a key from it
     */
    'whatsapp_number_json_field' => env('WHATSAPP_NUMBER_JSON_FIELD', 'whatsapp'),

    /*
     * If application is Local AND run in debug mode, send to this number instead
     */
    'debug_number' => env('DEBUG_WHATSAPP_NUMBER', null),
];
