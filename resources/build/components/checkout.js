import _config from "../utilities/config.js";
_config.init();

import _utilities from "../utilities/utilities.js";
import { _api, _storage } from "../utilities/api.js";

import { helpers as _validation_helpers } from "@vuelidate/validators";
import _validation from "../utilities/validation.js";

import debounce from "lodash/debounce";

let fields = {
    first_name: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    last_name: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    email: {
        validations: {
            rules: {
                required: _validation.rules.required,
                email: _validation.rules.email
            }
        },
        storage: true
    },
    phone: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    password: {
        validations: {
            rules: {
                required: _validation.rules.required,
                minLength: _validation.rules.minLength(8)
            }
        }
    },
    password_confirmation: {
        validations: {
            rules: {
                required: _validation.rules.required,
                sameAs: _validation_helpers.withParams(
                    { type: "sameAs" },
                    function(value) {
                        if (
                            typeof(this.field_values) !== "undefined"
                            && typeof(this.field_values.password) !== "undefined"
                        ) {
                            if (this.field_values.password == "") {
                                return true;
                            }
                            if (this.field_values.password == value) {
                                return true;
                            }
                            return false;

                        } else {
                            return true;
                        }
                    }
                )
            },
            messages: {
                sameAs: "This confirmation password must match"
            }
        }
    },
    shipping_address_line_1: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    shipping_address_line_2: {
        storage: true
    },
    shipping_city: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    shipping_county: {
        storage: true
    },
    shipping_postcode: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    shipping_country: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    same_as_shipping: {
        default: true,
        storage: true
    },
    billing_address_line_1: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    billing_address_line_2: {
        storage: true
    },
    billing_city: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    billing_county: {
        storage: true
    },
    billing_postcode: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    billing_country: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        storage: true
    },
    shipping_method: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        }
    },
    agree_terms: {
        validations: {
            rules: {
                required: _validation_helpers.withParams(
                    { type: "required" },
                    function(value) {
                        return value;
                    }
                )
            },
            messages: {
                required: "Please agree to the terms & conditions to proceed"
            }
        },
        storage: true
    },
    amount: {
        validations: {
            rules: {
                required: _validation.rules.required
            }
        },
        default: "0.00"
    },
    stripe_customer_id: {
        default: _config.get("payments.stripe.stripe_customer_id")
    }
};

if (_config.get("checkout.email_domain_check.enabled")) {

    if (typeof(fields.email.validations.rules) === "undefined") {
        fields.email.validations.rules = {};
    }

    fields.email.validations.rules.matchesDomain = (value) => {
        let is_valid = true;
        const regex = new RegExp("@(" + _config.get("checkout.email_domain_check.list").replaceAll(".", "\\.") + ")$", "gi");
        if (value != "") { return regex.test(value); }
        return is_valid;
    };

    if (typeof(fields.email.validations.messages) === "undefined") {
        fields.email.validations.messages = {};
    }

    fields.email.validations.messages.matchesDomain = "Please enter an email with an approved domain";
}

if (_config.get("checkout.customer_unique")) {

    if (typeof(fields.email.validations.rules) === "undefined") {
        fields.email.validations.rules = {};
    }

    fields.email.validations.rules.isUnique = debounce((value) => {
        if (value == "") { return true; }
        return _storage.post(_config.get("api.exists"), (_response) => {
            return !_storage.isSuccess(_response);
        }, {
            data: {
                email: value
            }
        });
    }, 200);

    if (typeof(fields.email.validations.messages) === "undefined") {
        fields.email.validations.messages = {};
    }

    fields.email.validations.messages.isUnique = "This email has already been used to place an order. Please enter another one";
}

let _checkout = {
    name: "c-checkout",
    data() {
        return {
            hide_pricing: _config.get("checkout.hide_pricing"),
            shipping_required: _config.get("checkout.shipping_required"),
            billing_required: _config.get("checkout.billing_required"),
            agree_terms: _config.get("checkout.agree_terms"),
            exists: null,
            logging_in: false,
            countries: [],
            shipping_methods: [],

            fields: fields,
            cart_fetched: false,
            items: [],
            product_items: [],
            shipping_items: [],
            count: 0,
            subtotal: 0,
            product_subtotal: 0,
            shipping: 0,
            tax: 0,
            total: 0,
            formatted: {
                subtotal: null,
                shipping: null,
                tax: null,
                total: null,
            },

            ref: "checkout",
            action: "checkout",
            field_values: _validation.createFieldsData(fields),
            field_storage: _validation.createFieldsStorage(fields),
            validation_messages: _validation.createFieldsValidationMessages(fields),
        };
    },
    watch: {
        "shipping_required": {
            handler(new_val, old_val) {
                if (new_val != old_val) {
                    if (new_val === false) {
                        this.field_values.shipping_address_line_1 = "";
                        this.field_values.shipping_address_line_2 = "";
                        this.field_values.shipping_city = "";
                        this.field_values.shipping_county = "";
                        this.field_values.shipping_postcode = "";
                        this.field_values.shipping_country = "";

                        this.field_values.same_as_shipping = false;
                    }
                }
            }, immediate: true
        },
        "billing_required": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (new_val === true && this.field_values.same_as_shipping == true) {
                        this.field_values.billing_address_line_1 = this.field_values.shipping_address_line_1;
                        this.field_values.billing_address_line_2 = this.field_values.shipping_address_line_2;
                        this.field_values.billing_city = this.field_values.shipping_city;
                        this.field_values.billing_county = this.field_values.shipping_county;
                        this.field_values.billing_postcode = this.field_values.shipping_postcode;
                        this.field_values.billing_country = this.field_values.shipping_country;
                    } else {
                        this.field_values.billing_address_line_1 = "";
                        this.field_values.billing_address_line_2 = "";
                        this.field_values.billing_city = "";
                        this.field_values.billing_county = "";
                        this.field_values.billing_postcode = "";
                        this.field_values.billing_country = "";

                        this.field_values.same_as_shipping = false;
                    }
                }
            }, immediate: true
        },
        "field_values.email": {
            handler(new_val, old_val) {
                if (new_val != old_val) {
                    this.checkUserExistence(new_val);
                }
            }
        },
        "field_values.password": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.exists === true) {
                    this.attemptsUserLogin(new_val);
                }
            }
        },
        "field_values.same_as_shipping": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (new_val === true && this.billing_required == true) {
                        this.field_values.billing_address_line_1 = this.field_values.shipping_address_line_1;
                        this.field_values.billing_address_line_2 = this.field_values.shipping_address_line_2;
                        this.field_values.billing_city = this.field_values.shipping_city;
                        this.field_values.billing_county = this.field_values.shipping_county;
                        this.field_values.billing_postcode = this.field_values.shipping_postcode;
                        this.field_values.billing_country = this.field_values.shipping_country;
                    } else {
                        this.field_values.billing_address_line_1 = "";
                        this.field_values.billing_address_line_2 = "";
                        this.field_values.billing_city = "";
                        this.field_values.billing_county = "";
                        this.field_values.billing_postcode = "";
                        this.field_values.billing_country = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_address_line_1": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_address_line_1 = new_val;
                    } else {
                        this.field_values.billing_address_line_1 = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_address_line_2": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_address_line_2 = new_val;
                    } else {
                        this.field_values.billing_address_line_2 = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_city": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_city = new_val;
                    } else {
                        this.field_values.billing_city = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_county": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_county = new_val;
                    } else {
                        this.field_values.billing_county = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_postcode": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_postcode = new_val;
                    } else {
                        this.field_values.billing_postcode = "";
                    }
                }
            }, immediate: true
        },
        "field_values.shipping_country": {
            handler(new_val, old_val) {
                if (new_val != old_val && this.shipping_required == true) {
                    if (this.field_values.same_as_shipping == true && this.billing_required == true) {
                        this.field_values.billing_country = new_val;
                    } else {
                        this.field_values.billing_country = "";
                    }
                }
            }, immediate: true
        },
    },
    methods: {
        getShippingMethods() {
            return _storage.get(_config.get("api.shipping_methods.index"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    let response = _storage.getResponseData(_response);
                    this.shipping_methods = response.shipping_methods;
                }
            });
        },
        getCountries() {
            return _storage.get(_config.get("api.countries.index"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    let response = _storage.getResponseData(_response);
                    this.countries = response.countries;
                }
            });
        },
        formatOptions(options) {
            let value = [];
            for (const [option_key, option_value] of Object.entries(options)) {
                if (option_key != 'parent_id') {
                    if (option_key == "size") {
                        value.push("<strong>" + _utilities.ucfirst(option_key) + ":</strong>&nbsp;" + option_value.toUpperCase());
                    } else {
                        value.push("<strong>" + _utilities.ucfirst(option_key) + ":</strong>&nbsp;" + _utilities.ucfirst(option_value));
                    }
                }
            }
            return value.join(" | ");
        },
        formatPrice(price) {
            return "£" + parseFloat(price).toFixed(2);
        },
        changeShippingMethod(form, method) {
            if (this.field_values.shipping_method == method.code) {
                this.field_values.shipping_method = null;
                if (typeof(form.validation_rules.shipping_method) !== "undefined") {
                    form.validation_rules.shipping_method.$touch();
                }
                this.manageCartShipping(null);
                return;
            }
            if (!method.is_disabled) {
                this.field_values.shipping_method = method.code;
                if (typeof(form.validation_rules.shipping_method) !== "undefined") {
                    form.validation_rules.shipping_method.$touch();
                }
                this.manageCartShipping(method);
            }
        },
        addCartShipping(shipping_method) {
            if (shipping_method != null) {
                _storage.post(_config.get("api.cart.store"), (_response) => {
                    if (_storage.isSuccess(_response)) {
                        this.$emit("update:cart");
                        this.updateCart();
                    }
                }, {
                    data: {
                        id: "shipping",
                        name: "Postage & Packaging",
                        qty: 1,
                        price: shipping_method.price,
                        weight: 0,
                        options: {
                            method_name: shipping_method.name,
                            method_code: shipping_method.code,
                            shipping_provider_id: (typeof(shipping_method.configuration) !== "undefined" && typeof(shipping_method.configuration.onecrm_shipping_provider_id) !== "undefined") ? shipping_method.configuration.onecrm_shipping_provider_id : null
                        }
                    }
                });
            } else {
                this.$emit("update:cart");
                this.updateCart();
            }
        },
        manageCartShipping(shipping_method) {
            if (this.shipping_items.length > 0) {
                _storage.delete(_config.get("api.cart.remove") + "/shipping", (_response) => {
                    this.addCartShipping(shipping_method);
                });
            } else {
                this.addCartShipping(shipping_method);
            }
        },
        updateCart() {
            _storage.get(_config.get("api.cart.index"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    let response = _storage.getResponseData(_response);
                    this.items = response.items;
                    this.product_items = response.product_items;
                    this.shipping_items = response.shipping_items;
                    this.count = response.count;
                    this.subtotal = response.subtotal;
                    this.product_subtotal = response.product_subtotal;
                    this.shipping = response.shipping;
                    this.tax = response.tax;
                    this.total = response.total;
                    this.formatted.subtotal = response.formatted.subtotal;
                    this.formatted.product_subtotal = response.formatted.product_subtotal;
                    this.formatted.shipping = response.formatted.shipping;
                    this.formatted.tax = response.formatted.tax;
                    this.formatted.total = response.formatted.total;

                    if (this.shipping_items.length > 0) {
                        this.shipping_items.forEach((shipping_item) => {
                            if (typeof(shipping_item.options.method_code) !== "undefined") {
                                this.field_values.shipping_method = shipping_item.options.method_code;
                            }
                        });
                    }
                }

                this.cart_fetched = true;
                this.is_loading = false;
            });
        },
        checkUserExistence: debounce(function(email) {
            if (email != "") {
                _storage.post(_config.get("api.exists"), (_response) => {
                    if (_storage.isSuccess(_response)) {
                        let response = _storage.getResponseData(_response);
                        this.exists = response.exists;
                    }
                }, {
                    data: {
                        email: email
                    }
                });
            } else {
                this.exists = null;
            }
        }, 200),
        attemptsUserLogin: debounce(function(password) {
            if (this.field_values.email != "" && password != "") {
                _storage.post(_config.get("api.login"), (_response) => {
                    if (_storage.isSuccess(_response)) {
                        this.logging_in = true;
                        setTimeout(() => {
                            this.$root.$refs.checkout.load();
                            setTimeout(() => {
                                window.localStorage.setItem("checkout.logging_in", true);
                                window.location.reload();
                            }, 600);
                        }, 1000);
                    }
                }, {
                    data: {
                        email: this.field_values.email,
                        password: password
                    }
                });
            }
        }, 1000),
    },
    computed: {
        full_name() {
            return [
                this.field_values.first_name,
                this.field_values.last_name,
            ].filter(Boolean).join(" ");
        },
        checkout_validation() {
            let rules = {};

            rules.first_name = fields.first_name;
            rules.last_name = fields.last_name;
            rules.email = fields.email;
            rules.phone = fields.phone;

            if (this.exists === true) {
                rules.password = fields.password;
            }

            if (this.exists === false) {
                rules.password = fields.password;
                rules.password_confirmation = fields.password_confirmation;
            }

            if (this.shipping_required === true) {
                rules.shipping_address_line_1 = fields.shipping_address_line_1;
                rules.shipping_address_line_2 = fields.shipping_address_line_2;
                rules.shipping_city = fields.shipping_city;
                rules.shipping_county = fields.shipping_county;
                rules.shipping_postcode = fields.shipping_postcode;
                rules.shipping_country = fields.shipping_country;
                rules.same_as_shipping = fields.same_as_shipping;
            }

            if ((this.shipping_required === true && this.field_values.same_as_shipping === false && this.billing_required === true) || this.shipping_required === false) {
                rules.billing_address_line_1 = fields.billing_address_line_1;
                rules.billing_address_line_2 = fields.billing_address_line_2;
                rules.billing_city = fields.billing_city;
                rules.billing_county = fields.billing_county;
                rules.billing_postcode = fields.billing_postcode;
                rules.billing_country = fields.billing_country;
            }

            rules.shipping_method = fields.shipping_method;
            if (this.agree_terms === true) {
                rules.agree_terms = fields.agree_terms;
            }
            rules.amount = fields.amount;
            rules.stripe_customer_id = fields.stripe_customer_id;

            return _validation.createFieldsValidationRules(rules);
        },
        available_shipping_methods() {
            return this.shipping_methods.filter((shipping_method) => {
                if (shipping_method.configuration) {
                    let rules_fulfilled = true;
                    if (typeof(shipping_method.configuration.items_from) !== "undefined") {
                        if (this.count < shipping_method.configuration.items_from) {
                            rules_fulfilled = false;
                        }
                    }
                    if (typeof(shipping_method.configuration.items_to) !== "undefined") {
                        if (this.count > shipping_method.configuration.items_to) {
                            rules_fulfilled = false;
                        }
                    }
                    if (rules_fulfilled == true) {
                        shipping_method.is_disabled = false;
                        shipping_method.is_hidden = false;
                    } else {
                        shipping_method.is_disabled = true;
                        shipping_method.is_hidden = true;
                    }
                }
                if (shipping_method.is_disabled === true) {
                    if (this.field_values.shipping_method == shipping_method.code) {
                        this.field_values.shipping_method = null;
                        this.manageCartShipping(null);
                    }
                }
                if (shipping_method.is_hidden === true) {
                    return false;
                }
                return true;
            });
        },
    },
    created() {
        let logging_in = window.localStorage.getItem("checkout.logging_in");
        if (logging_in == "true") {
            this.logging_in = true;
            window.localStorage.removeItem("checkout.logging_in");
        }

        this.getCountries();
        this.getShippingMethods();
        this.updateCart();
    },
    template: `
    <div class="checkout">
        <slot
        :hide_pricing="hide_pricing"
        :shipping_required="shipping_required"
        :billing_required="billing_required"
        :agree_terms="agree_terms"
        :exists="exists"
        :logging_in="logging_in"
        :countries="countries"
        :available_shipping_methods="available_shipping_methods"
        :fields="fields"
        :cart_fetched="cart_fetched"
        :items="items"
        :product_items="product_items"
        :shipping_items="shipping_items"
        :count="count"
        :subtotal="subtotal"
        :product_subtotal="product_subtotal"
        :shipping="shipping"
        :tax="tax"
        :total="total"
        :formatted="formatted"
        :ref="ref"
        :action="action"
        :field_values="field_values"
        :field_storage="field_storage"
        :validation_rules="checkout_validation"
        :validation_messages="validation_messages"
        :full_name="full_name"
        :formatOptions="formatOptions"
        :formatPrice="formatPrice"
        :changeShippingMethod="changeShippingMethod"
        ></slot>
    </div>
    `
};

export default _checkout;