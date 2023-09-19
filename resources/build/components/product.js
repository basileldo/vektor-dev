import _config from "../utilities/config.js";
_config.init();

import { _storage } from "../utilities/api.js";

import cloneDeep from "lodash/cloneDeep";

let _product = {
    name: "c-product",
    emits: [
        "message:error",
        "message:hide",
        "message:success",
        "update:cart"
    ],
    props: {
        config: {
            type: Object,
            default() {
                return {};
            }
        }
    },
    data() {
        return {
            hide_pricing: _config.get("checkout.hide_pricing"),
            qty: 1,
            variations: {},
            variation_tiers: {},
            options: {},
            success_message: "",
            error_message: "",
            is_success_message_shown: false,
            is_error_message_shown: false,
            is_modal_shown: false,
            is_ready: false,
            is_loading: false,
        };
    },
    methods: {
        clearMessages() {
            this.is_success_message_shown = false;
            this.is_error_message_shown = false;
            this.$emit("message:hide");
        },
        showSuccessMessage(message) {
            this.success_message = message;
            this.is_success_message_shown = true;
            this.$emit("message:success", { message: this.success_message });
        },
        showErrorMessage(message) {
            this.error_message = message;
            this.is_error_message_shown = true;
            this.$emit("message:error", { message: this.error_message });
        },
        fetchTieredProperty(config, options, property) {
            let config_products = cloneDeep(config.products);
            if (config_products && config_products.length > 0 && property !== undefined) {
                let options_array = Object.entries(options);

                for (const product of config_products) {
                    product.match_count = 0;
                    if (product.attributes !== undefined && product.attributes.length > 0) {
                        for (const attribute of product.attributes) {
                            for (const [option_key, option_value] of options_array) {
                                if (option_key === attribute.name) {
                                    if (option_value != null && option_value === attribute.value) {
                                        product.match_count++;
                                    }
                                }
                            }
                        };
                    }
                }

                for (const product of config_products) {
                    if (product.match_count == options_array.length) {
                        return product[property];
                    }
                }

                for (const product of config_products) {
                    if (product.match_count > 0 && product.match_count < options_array.length) {
                        return product[property];
                    }
                }
            }
            if (typeof config[property] !== undefined) {
                return config[property];
            }
            return null;
        },
        formatPrice(price) {
            return `£${parseFloat(price).toFixed(2)}`;
        },
        clearProductOptions() {
            for (const [option_key, option_value] of Object.entries(this.options)) {
                this.options[option_key] = null;
            }

            for (const [variation_name, variation_value] of Object.entries(this.variations)) {
                variation_value.variations.forEach((variation) => {
                    variation.disabled = false;
                });
            }
            this.qty = 1;
        },
        evaluateVariationAvailability(new_val, old_val, name) {
            if (new_val != old_val) {
                if (typeof(this.variation_tiers[name]) !== "undefined" && typeof(this.variation_tiers[name][new_val]) !== "undefined") {
                    for (const [variation_name, variation_variations] of Object.entries(this.variation_tiers[name][new_val])) {
                        if (typeof(this.variations[variation_name]) !== "undefined") {
                            this.variations[variation_name].variations.forEach((variation) => {
                                variation.disabled = !variation_variations.includes(variation.value);
                            });
                        }
                    }
                }
            }
        },
        variationInputType(type) {
            return ["color", "colour"].includes(type) ? "select:colors" : "select:swatches";
        },
        extractVariationTypes(product) {
            if (product.products !== undefined && product.products.length > 0) {
                product.products.forEach((product_inner, product_inner_idx) => {
                    if (product_inner.attributes !== undefined && product_inner.attributes.length > 0) {
                        product_inner.attributes.forEach((attribute, attribute_idx) => {
                            let variation = {
                                disabled: false,
                                value: attribute.value,
                                label: attribute.value_label,
                                configuration: attribute.configuration !== undefined ? attribute.configuration : {}
                            };

                            if (product_inner_idx === 0) {
                                this.options[attribute.name] = null;

                                this.$watch(`options.${attribute.name}`, (new_val, old_val) => {
                                    this.evaluateVariationAvailability(new_val, old_val, attribute.name);
                                });

                                this.variations[attribute.name] = {
                                    name: attribute.name,
                                    label: attribute.name_label,
                                    variations: [variation]
                                };
                            } else {
                                let variationExists = this.variations[attribute.name].variations.some((_variation) => {
                                    return _variation.value == variation.value;
                                });
                                if (!variationExists) {
                                    this.variations[attribute.name].variations.push(variation);
                                }
                            }

                            if (attribute_idx === 0) {
                                if (this.variation_tiers[attribute.name] === undefined) {
                                    this.variation_tiers[attribute.name] = {};
                                }
                                if (this.variation_tiers[attribute.name][attribute.value] === undefined) {
                                    this.variation_tiers[attribute.name][attribute.value] = {};
                                }
                            } else {
                                if (this.variation_tiers[product_inner.attributes[0].name][product_inner.attributes[0].value][attribute.name] === undefined) {
                                    this.variation_tiers[product_inner.attributes[0].name][product_inner.attributes[0].value][attribute.name] = [];
                                }

                                if (!this.variation_tiers[product_inner.attributes[0].name][product_inner.attributes[0].value][attribute.name].includes(attribute.value)) {
                                    this.variation_tiers[product_inner.attributes[0].name][product_inner.attributes[0].value][attribute.name].push(attribute.value);
                                }
                            }
                        });
                    }
                });
            }
        },
        addCartItem() {
            if (this.is_disabled) {
                return;
            }
            this.clearMessages();
            this.is_loading = true;

            _storage.post(_config.get("api.cart.store"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    let message = _storage.getResponseMessage(_response);
                    this.showSuccessMessage(message);
                    this.$emit("update:cart");
                    if (this.is_modal_shown) {
                        this.is_modal_shown = false;
                        setTimeout(() => {
                            this.clearProductOptions();
                            this.is_loading = false;
                        }, 350);
                    } else {
                        this.clearProductOptions();
                        this.is_loading = false;
                    }
                }

                if (_storage.isError(_response)) {
                    let message = _storage.getResponseMessage(_response);
                    this.showErrorMessage(message);

                    if (this.is_modal_shown) {
                        this.is_modal_shown = false;
                        setTimeout(() => {
                            this.clearProductOptions();
                            this.is_loading = false;
                        }, 350);
                    } else {
                        this.clearProductOptions();
                        this.is_loading = false;
                    }
                }
            }, {
                data: {
                    id: this.id,
                    name: this.config.name_label,
                    qty: this.qty,
                    price: this.price,
                    weight: 0,
                    options: {
                        parent_id: this.config.id,
                        ...this.options
                    },
                    qty_per_order: this.config.qty_per_order,
                    qty_per_order_grouping: this.config.qty_per_order_grouping
                }
            });
        },
        openModal() {
            this.is_modal_shown = true;
        },
        closeModal() {
            this.is_modal_shown = false;
        },
        updateQty(qty) {
            this.qty = qty;
        }
    },
    computed: {
        id() {
            let property = this.fetchTieredProperty(this.config, this.options, "id");
            return (property !== undefined && property !== null) ? property : this.config.id;
        },
        display_price() {
            let property = this.fetchTieredProperty(this.config, this.options, "display_price");
            return (property !== undefined && property !== null) ? property : this.config.display_price;
        },
        price() {
            let property = this.fetchTieredProperty(this.config, this.options, "price");
            return (property !== undefined && property !== null) ? property : this.config.price;
        },
        weight() {
            let property = this.fetchTieredProperty(this.config, this.options, "weight");
            return (property !== undefined && property !== null) ? property : this.config.weight;
        },
        images() {
            let property = this.fetchTieredProperty(this.config, this.options, "images");
            return (property !== undefined && property !== null) ? property : this.config.images;
        },
        image() {
            if (this.images && this.images.length > 0) {
                return this.images[0];
            }
            return null;
        },
        is_disabled() {
            if (this.qty < 1) {
                return true;
            }
            for (const [option_key, option_value] of Object.entries(this.options)) {
                if (option_value == null) {
                    return true;
                }
            }
            return false;
        }
    },
    watch: {
        config: {
            handler(new_val, old_val) {
                if (old_val === undefined || old_val.id === undefined) {
                    this.extractVariationTypes(new_val);
                    this.is_ready = true;
                }
            },
            deep: true,
            immediate: true
        }
    },
    template: `
    <article class="product">
        <slot
        :config="config"
        :hide_pricing="hide_pricing"
        :qty="qty"
        :variations="variations"
        :options="options"
        :success_message="success_message"
        :error_message="error_message"
        :is_success_message_shown="is_success_message_shown"
        :is_error_message_shown="is_error_message_shown"
        :is_modal_shown="is_modal_shown"
        :variationInputType="variationInputType"
        :addCartItem="addCartItem"
        :formatPrice="formatPrice"
        :openModal="openModal"
        :closeModal="closeModal"
        :updateQty="updateQty"
        :display_price="display_price"
        :price="price"
        :weight="weight"
        :images="images"
        :image="image"
        :is_disabled="is_disabled"
        :is_ready="is_ready"
        :is_loading="is_loading"
        ></slot>
    </article>
    `
};

export default _product;