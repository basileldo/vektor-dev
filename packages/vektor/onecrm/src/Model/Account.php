<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;
use Vektor\Utilities\Formatter;

class Account extends Model
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
        'name',
        'phone_office',
        'email1',
        'shipping_address_street',
        'shipping_address_city',
        'shipping_address_state',
        'shipping_address_statecode',
        'shipping_address_postalcode',
        'shipping_address_countrycode',
        'shipping_address_country',
        'billing_address_street',
        'billing_address_city',
        'billing_address_state',
        'billing_address_statecode',
        'billing_address_postalcode',
        'billing_address_countrycode',
        'billing_address_country',
        'account_type',
        'account_status',
        'new_record',
    ];

    protected $casts = [
        'new_record' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'account_type' => 'Customer',
        'account_status' => 'Active',
        'shipping_address_countrycode' => 'GB',
        'shipping_address_country' => 'United Kingdom',
        'billing_address_countrycode' => 'GB',
        'billing_address_country' => 'United Kingdom',
        'new_record' => false,
    ];

    /**
     * The attributes that are not to be used to update.
     *
     * @var array
     */
    protected $excluded_update_attributes = [
        'name',
        'phone_office',
        'email1',
        'account_type',
        'account_status',
    ];

    public $crm;

    public $crm_model;

    public function __construct()
    {
        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        if ($this->shipping_before_billing) {
            $this->appends = array_merge($this->appends, [
                'billing_address_street',
                'billing_address_city',
                'billing_address_state',
                'billing_address_postalcode',
                'billing_address_countrycode',
                'billing_address_country',
            ]);
        } else {
            $this->appends = array_merge($this->appends, [
                'shipping_address_street',
                'shipping_address_city',
                'shipping_address_state',
                'shipping_address_postalcode',
                'shipping_address_countrycode',
                'shipping_address_country',
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
     * Set the model's shipping_address_street.
     */
    public function setShippingAddressStreetAttribute(array|null $value): void
    {
        $this->attributes['shipping_address_street'] = !empty($value) ? Formatter::arrayToString($value, "\n") : null;
    }

    /**
     * Set the model's shipping_address_city.
     */
    public function setShippingAddressCityAttribute(string|null $value): void
    {
        $this->attributes['shipping_address_city'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Set the model's shipping_address_postalcode.
     */
    public function setShippingAddressPostalcodeAttribute(string|null $value): void
    {
        $this->attributes['shipping_address_postalcode'] = (!empty($value) && $this->attributes['shipping_address_countrycode'] == 'GB') ? Formatter::postcode($value) : $value;
    }

    /**
     * Set the model's billing_address_street.
     */
    public function setBillingAddressStreetAttribute(array|null $value): void
    {
        $this->attributes['billing_address_street'] = !empty($value) ? Formatter::arrayToString($value, "\n") : $value;
    }

    /**
     * Set the model's billing_address_city.
     */
    public function setBillingAddressCityAttribute(string|null $value): void
    {
        $this->attributes['billing_address_city'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Set the model's billing_address_postalcode.
     */
    public function setBillingAddressPostalcodeAttribute(string|null $value): void
    {
        $this->attributes['billing_address_postalcode'] = (!empty($value) && $this->attributes['billing_address_countrycode'] == 'GB') ? Formatter::postcode($value) : $value;
    }

    /**
     * Get the model's shipping_address_street.
     */
    public function getShippingAddressStreetAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_street']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_street'] : null);
    }

    /**
     * Get the model's shipping_address_city.
     */
    public function getShippingAddressCityAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_city']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_city'] : null);
    }

    /**
     * Get the model's shipping_address_state.
     */
    public function getShippingAddressStateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_state']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_state'] : null);
    }

    /**
     * Get the model's shipping_address_state.
     */
    public function getShippingAddressStatecodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_statecode']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_statecode'] : null);
    }

    /**
     * Get the model's shipping_address_postalcode.
     */
    public function getShippingAddressPostalcodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_postalcode']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_postalcode'] : null);
    }

    /**
     * Get the model's shipping_address_country.
     */
    public function getShippingAddressCountryAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_country']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_country'] : null);
    }

    /**
     * Get the model's shipping_address_countrycode.
     */
    public function getShippingAddressCountrycodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_address_countrycode']) && $this->shipping_before_billing == false ? $this->attributes['billing_address_countrycode'] : null);
    }

    /**
     * Get the model's billing_address_street.
     */
    public function getBillingAddressStreetAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_street']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_street'] : null);
    }

    /**
     * Get the model's billing_address_city.
     */
    public function getBillingAddressCityAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_city']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_city'] : null);
    }

    /**
     * Get the model's billing_address_state.
     */
    public function getBillingAddressStateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_state']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_state'] : null);
    }

    /**
     * Get the model's billing_address_statecode.
     */
    public function getBillingAddressStatecodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_statecode']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_statecode'] : null);
    }

    /**
     * Get the model's billing_address_postalcode.
     */
    public function getBillingAddressPostalcodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_postalcode']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_postalcode'] : null);
    }

    /**
     * Get the model's billing_address_country.
     */
    public function getBillingAddressCountryAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_country']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_country'] : null);
    }

    /**
     * Get the model's billing_address_countrycode.
     */
    public function getBillingAddressCountrycodeAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_address_countrycode']) && $this->shipping_before_billing == true ? $this->attributes['shipping_address_countrycode'] : null);
    }

    public function show($id, $data = [])
    {
        $_response = $this->crm_model->show('accounts', $id, $data);
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
            $_existing_response = $this->crm_model->show('accounts', $data['id']);
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
                    'any_email' => $data['email1'],
                ],
            ];

            $_existing_response = $this->crm_model->index('accounts', $existing_data);
            $existing_response = Api::transformResponse($_existing_response);

            if ($existing_response['success']) {
                if (
                    isset($existing_response['data']['records'])
                    && !empty($existing_response['data']['records'])
                ) {
                    $_existing_record = current($existing_response['data']['records']);
                }
            }
        }


        if ($_existing_record) {
            if (!empty($this->excluded_update_attributes)) {
                foreach ($this->excluded_update_attributes as $excluded_update_attribute) {
                    unset($data[$excluded_update_attribute]);
                }
            }

            $this->crm_model->update('accounts', $_existing_record['id'], $data);
            $existing_record = $this->crm_model->show('accounts', $_existing_record['id']);

            $this->id = $existing_record['data']['record']['id'];

            return $existing_record['data']['record'];
        }

        $_response = $this->crm_model->create('accounts', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->new_record = true;
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }
}
