<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;
use Vektor\Utilities\Formatter;

class Contact extends Model
{
    public $shipping_before_billing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'primary_account_id',
        'first_name',
        'last_name',
        'phone_work',
        'email1',
        'email_opt_in_date',
        'email_opt_in',
        'email_opt_out',
        'primary_address_street',
        'primary_address_city',
        'primary_address_state',
        'primary_address_postalcode',
        'primary_address_countrycode',
        'primary_address_country',
        'alt_address_street',
        'alt_address_city',
        'alt_address_state',
        'alt_address_postalcode',
        'alt_address_countrycode',
        'alt_address_country',
        'new_record',
    ];

    protected $casts = [
        'new_record' => 'boolean',
        'email_opt_in' => 'integer',
        'email_opt_out' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'email_opt_in_date',
        'email_opt_out',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'email_opt_in' => 0,
        'primary_address_countrycode' => 'GB',
        'primary_address_country' => 'United Kingdom',
        'alt_address_countrycode' => 'GB',
        'alt_address_country' => 'United Kingdom',
        'new_record' => false,
    ];

    /**
     * The attributes that are not to be used to update.
     *
     * @var array
     */
    protected $excluded_update_attributes = [
        'email1',
    ];

    public $crm;

    public $crm_model;

    public function __construct()
    {
        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        if ($this->shipping_before_billing) {
            $this->appends = array_merge($this->appends, [
                'alt_address_street',
                'alt_address_city',
                'alt_address_state',
                'alt_address_postalcode',
                'alt_address_countrycode',
                'alt_address_country',
            ]);
        } else {
            $this->appends = array_merge($this->appends, [
                'primary_address_street',
                'primary_address_city',
                'primary_address_state',
                'primary_address_postalcode',
                'primary_address_countrycode',
                'primary_address_country',
            ]);
        }

        return $this;
    }

    /**
     * Set the model's email1.
     */
    public function setEmail1Attribute(string|null $value): void
    {
        $this->attributes['email1'] = !empty($value) ? Formatter::email($value) : $value;
    }

    /**
     * Set the model's primary_address_street.
     */
    public function setPrimaryAddressStreetAttribute(array|null $value): void
    {
        $this->attributes['primary_address_street'] = !empty($value) ? Formatter::arrayToString($value, "\n") : null;
    }

    /**
     * Set the model's primary_address_city.
     */
    public function setPrimaryAddressCityAttribute(string|null $value): void
    {
        $this->attributes['primary_address_city'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Set the model's primary_address_postalcode.
     */
    public function setPrimaryAddressPostalcodeAttribute(string|null $value): void
    {
        $this->attributes['primary_address_postalcode'] = (!empty($value) && $this->attributes['primary_address_countrycode'] == 'GB') ? Formatter::postcode($value) : $value;
    }

    /**
     * Set the model's alt_address_street.
     */
    public function setAltAddressStreetAttribute(array|null $value): void
    {
        $this->attributes['alt_address_street'] = !empty($value) ? Formatter::arrayToString($value, "\n") : $value;
    }

    /**
     * Set the model's alt_address_city.
     */
    public function setAltAddressCityAttribute(string|null $value): void
    {
        $this->attributes['alt_address_city'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Set the model's alt_address_postalcode.
     */
    public function setAltAddressPostalcodeAttribute(string|null $value): void
    {
        $this->attributes['alt_address_postalcode'] = (!empty($value) && $this->attributes['alt_address_countrycode'] == 'GB') ? Formatter::postcode($value) : $value;
    }

    /**
     * Get the model's primary_address_street.
     */
    public function getPrimaryAddressStreetAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_street']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_street'] : null);
    }

    /**
     * Get the model's primary_address_city.
     */
    public function getPrimaryAddressCityAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_city']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_city'] : null);
    }

    /**
     * Get the model's primary_address_state.
     */
    public function getPrimaryAddressStateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_state']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_state'] : null);
    }

    /**
     * Get the model's primary_address_state.
     */
    public function getPrimaryAddressStatecodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_statecode']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_statecode'] : null);
    }

    /**
     * Get the model's primary_address_postalcode.
     */
    public function getPrimaryAddressPostalcodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_postalcode']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_postalcode'] : null);
    }

    /**
     * Get the model's primary_address_country.
     */
    public function getPrimaryAddressCountryAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_country']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_country'] : null);
    }

    /**
     * Get the model's primary_address_countrycode.
     */
    public function getPrimaryAddressCountrycodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['alt_address_countrycode']) && $this->shipping_before_billing == false ? $this->attributes['alt_address_countrycode'] : null);
    }

    /**
     * Get the model's alt_address_street.
     */
    public function getAltAddressStreetAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_street']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_street'] : null);
    }

    /**
     * Get the model's alt_address_city.
     */
    public function getAltAddressCityAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_city']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_city'] : null);
    }

    /**
     * Get the model's alt_address_state.
     */
    public function getAltAddressStateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_state']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_state'] : null);
    }

    /**
     * Get the model's alt_address_statecode.
     */
    public function getAltAddressStatecodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_statecode']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_statecode'] : null);
    }

    /**
     * Get the model's alt_address_postalcode.
     */
    public function getAltAddressPostalcodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_postalcode']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_postalcode'] : null);
    }

    /**
     * Get the model's alt_address_country.
     */
    public function getAltAddressCountryAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_country']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_country'] : null);
    }

    /**
     * Get the model's alt_address_countrycode.
     */
    public function getAltAddressCountrycodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['primary_address_countrycode']) && $this->shipping_before_billing == true ? $this->attributes['primary_address_countrycode'] : null);
    }

    /**
     * Get the model's email_opt_in_date.
     */
    public function getEmailOptInDateAttribute(string|null $value): string
    {
        return !empty($value) ? $value : date('Y-m-d');
    }

    /**
     * Get the model's email_opt_in_date.
     */
    public function getEmailOptOutAttribute(string|null $value): string
    {
        return ($this->attributes['email_opt_in'] == 0) ? 1 : 0;
    }

    public function show($id, $data = [])
    {
        $_response = $this->crm_model->show('contacts', $id, $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }

    public function persist()
    {
        $data = $this->toArray();
        unset($data['new_record']);

        $_existing_record = null;

        if (isset($data['id'])) {
            $_existing_response = $this->crm_model->show('contacts', $data['id']);
            $existing_response = Api::transformResponse($_existing_response);

            if ($existing_response['success']) {
                if (
                    isset($existing_response['data']['record'])
                    && ! empty($existing_response['data']['record'])
                ) {
                    $_existing_record = $existing_response['data']['record'];
                }
            }
        } else {
            $existing_data = [
                'filters' => [
                    'primary_account_id' => $data['primary_account_id'],
                    'any_email' => $data['email1'],
                ],
            ];

            $_existing_response = $this->crm_model->index('contacts', $existing_data);
            $existing_response = Api::transformResponse($_existing_response);

            if ($existing_response['success']) {
                if (
                    isset($existing_response['data']['records'])
                    && ! empty($existing_response['data']['records'])
                ) {
                    $_existing_record = current($existing_response['data']['records']);
                }
            }
        }

        if ($_existing_record) {
            if (! empty($this->excluded_update_attributes)) {
                foreach ($this->excluded_update_attributes as $excluded_update_attribute) {
                    unset($data[$excluded_update_attribute]);
                }
            }

            $this->crm_model->update('contacts', $_existing_record['id'], $data);
            $existing_record = $this->crm_model->show('contacts', $_existing_record['id']);

            $this->id = $existing_record['data']['record']['id'];

            return $existing_record['data']['record'];
        }

        $_response = $this->crm_model->create('contacts', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->new_record = true;
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }
}
