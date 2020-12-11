<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BarcodeFormat implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $sampletype_id = preg_replace(['/^type\./','/\..*$/'],'', $attribute);
        $sampletype = \App\sampletype::find($sampletype_id);
        $format = $sampletype->tubeLabelType->barcodeFormat;

        return preg_match("/$format/", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The barcode format for :input is invalid for this sample-type';
    }
}
