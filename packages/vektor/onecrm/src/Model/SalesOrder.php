<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;
use Vektor\Utilities\Formatter;

class SalesOrder extends Model
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
        'due_date',
        'delivery_date',
        'shipping_account_id',
        'shipping_contact_id',
        'shipping_address_street',
        'shipping_address_city',
        'shipping_address_state',
        'shipping_address_statecode',
        'shipping_address_postalcode',
        'shipping_address_country',
        'shipping_address_countrycode',
        'billing_account_id',
        'billing_contact_id',
        'billing_address_street',
        'billing_address_city',
        'billing_address_state',
        'billing_address_statecode',
        'billing_address_postalcode',
        'billing_address_country',
        'billing_address_countrycode',
        'amount',
        'amount_usdollar',
        'subtotal',
        'subtotal_usd',
        'pretax',
        'pretax_usd',
        'terms',

        'so_stage',
        'so_number',
        'prefix',
        'date_entered',
        'date_modified',
        'shipping_provider_id',

        'lines',
        'new_record',
    ];

    protected $casts = [
        'lines' => 'array',
        'new_record' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'due_date',
        'delivery_date',
        'amount_usdollar',
        'subtotal_usd',
        'pretax_usd',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'new_record' => false,
        'terms' => 'Due on Receipt',
    ];

    /**
     * The attributes that are not to be used to update.
     *
     * @var array
     */
    protected $excluded_update_attributes = [
    ];

    public $crm;

    public $crm_model;

    public function __construct()
    {
        if ($this->shipping_before_billing) {
            $this->appends = array_merge($this->appends, [
                'billing_account_id',
                'billing_contact_id',
            ]);
        } else {
            $this->appends = array_merge($this->appends, [
                'shipping_account_id',
                'shipping_contact_id',
            ]);
        }

        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        return $this;
    }

    /**
     * Set the model's shipping_address_street.
     */
    public function setShippingAddressStreetAttribute(array|null $value): void
    {
        $this->attributes['shipping_address_street'] = !empty($value) ? Formatter::arrayToString($value, "\n") : $value;
    }

    /**
     * Set the model's shipping_address_city.
     */
    public function setShippingAddressCityAttribute(string|null $value): void
    {
        $this->attributes['shipping_address_city'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Set the model's shipping_address_state.
     */
    public function setShippingAddressStateAttribute(string|null $value): void
    {
        $this->attributes['shipping_address_state'] = !empty($value) ? Formatter::name($value) : $value;
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
     * Set the model's billing_address_state.
     */
    public function setBillingAddressStateAttribute(string|null $value): void
    {
        $this->attributes['billing_address_state'] = !empty($value) ? Formatter::name($value) : $value;
    }

    /**
     * Get the model's due_date.
     */
    public function getDueDateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : date('Y-m-d');
    }

    /**
     * Get the model's delivery_date.
     */
    public function getDeliveryDateAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : date('Y-m-d');
    }

    /**
     * Get the model's amount_usdollar.
     */
    public function getAmountUsdollarAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['amount']) ? $this->attributes['amount'] : null);
    }

    /**
     * Get the model's subtotal_usd.
     */
    public function getSubtotalUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['subtotal']) ? $this->attributes['subtotal'] : null);
    }

    /**
     * Get the model's pretax_usd.
     */
    public function getPretaxUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['pretax']) ? $this->attributes['pretax'] : null);
    }

    /**
     * Get the model's shipping_account_id.
     */
    public function getShippingAccountIdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_account_id']) && $this->shipping_before_billing == false ? $this->attributes['billing_account_id'] : null);
    }

    /**
     * Get the model's shipping_contact_id.
     */
    public function getShippingContactIdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['billing_contact_id']) && $this->shipping_before_billing == false ? $this->attributes['billing_contact_id'] : null);
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
     * Get the model's billing_account_id.
     */
    public function getBillingAccountIdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_account_id']) && $this->shipping_before_billing == true ? $this->attributes['shipping_account_id'] : null);
    }

    /**
     * Get the model's billing_contact_id.
     */
    public function getBillingContactIdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['shipping_contact_id']) && $this->shipping_before_billing == true ? $this->attributes['shipping_contact_id'] : null);
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

    public function index($data = [])
    {
        $data['fields'] = $this->fillable;
        $_response = $this->crm_model->index('sales_orders', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            return $response['data']['records'];
        }

        return [];
    }

    public function show($id, $data = [])
    {
        $_response = $this->crm_model->show('sales_orders', $id, $data);
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

        $_response = $this->crm_model->create('sales_orders', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->new_record = true;
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }
}
