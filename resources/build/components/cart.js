import _config from "../utilities/config.js";
_config.init();

import _utilities from "../utilities/utilities.js";
import { _storage } from "../utilities/api.js";

let _cart = {
    name: "c-cart",
    data() {
        return {
            hide_pricing: _config.get("checkout.hide_pricing"),
            is_loading: false,
            cart_fetched: false,
            items: [],
            product_items: [],
            shipping_items: [],
            count: 0,
            subtotal: 0,
            shipping: 0,
            tax: 0,
            total: 0,
            formatted: {
                subtotal: null,
                shipping: null,
                tax: null,
                total: null,
            },
            success_message: "",
            error_message: "",
            is_success_message_shown: false,
            is_error_message_shown: false,
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
            if (config.products && config.products.length > 0 && property !== undefined) {
                let options_array = Object.entries(options);

                for (const product of config.products) {
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

                for (const product of config.products) {
                    if (product.match_count == options_array.length) {
                        return product[property];
                    }
                }

                for (const product of config.products) {
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
        formatOptions(options) {
            const value = [];
            for (const [option_key, option_value] of Object.entries(options)) {
                if (option_key != 'parent_id') {
                    const formattedOptionKey = _utilities.ucfirst(option_key);
                    const formattedOptionValue = option_key === "size" ? option_value.toUpperCase() : _utilities.ucfirst(option_value);
                    value.push(`<strong>${formattedOptionKey}:</strong>&nbsp;${formattedOptionValue}`);
                }
            }
            return value.join(" | ");
        },
        formatPrice(price) {
            return `£${parseFloat(price).toFixed(2)}`;
        },
        updateCartItem(product) {
            this.clearMessages();
            _storage.put(`cart/${product.rowId}`, (_response) => {
                const message = _storage.getResponseMessage(_response);
                if (_storage.isSuccess(_response)) {
                    this.showSuccessMessage(message);
                } else {
                    this.showErrorMessage(message);
                }
                this.updateCart();
            }, {
                data: {
                    qty: product.qty,
                    qty_per_order: product.qty_per_order,
                    qty_per_order_grouping: product.qty_per_order_grouping
                }
            });
        },
        deleteCartItem(product) {
            this.clearMessages();
            _storage.delete(`${_config.get("api.cart.remove")}/${product.rowId}`, (_response) => {
                this.updateCart();
            });
        },
        updateCart() {
            this.is_loading = true;
            _storage.get(_config.get("api.cart.index"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    const response = _storage.getResponseData(_response);
                    const {
                        items,
                        product_items,
                        shipping_items,
                        count,
                        subtotal,
                        shipping,
                        tax,
                        total,
                        formatted,
                    } = response;
                    this.items = items;
                    this.product_items = product_items;
                    this.shipping_items = shipping_items;
                    this.count = count;
                    this.subtotal = subtotal;
                    this.shipping = shipping;
                    this.tax = tax;
                    this.total = total;
                    this.formatted = formatted;
                    if (this.product_items.length > 0) {
                        this.product_items.forEach((item) => {
                            if (item.product) {
                                item.image = null;
                                item.images = this.fetchTieredProperty(item.product, item.options, "images");
                                if (item.images && item.images.length > 0) {
                                    item.image = item.images[0];
                                }
                                item.display_price = item.product.display_price;
                                item.slug = item.product.slug;
                                item.sku = item.product.sku;
                                item._id = item.product.id;
                                item.qty_per_order = item.product.qty_per_order;
                                item.qty_per_order_grouping = item.product.qty_per_order_grouping;
                            } else {
                                item = null;
                            }
                        });
                    }
                    this.$emit("update:cart");
                }
                this.cart_fetched = true;
                this.is_loading = false;
            });
        },
        clearCart() {
            this.clearMessages();
            _storage.delete("cart", (_response) => {
                this.updateCart();
            });
        },
    },
    computed: {
        product_items_grouped() {
            if (this.product_items.length > 0) {
                return this.product_items.reduce((accumulator, item) => {
                    accumulator[item.id] = accumulator[item.id] || [];
                    accumulator[item.id].push(item);
                    return accumulator;
                }, {});
            }
            return [];
        },
    },
    created() {
        this.updateCart();
    },
    template: `
    <div class="cart">
        <slot
        :hide_pricing="hide_pricing"
        :is_loading="is_loading"
        :cart_fetched="cart_fetched"
        :items="items"
        :product_items="product_items"
        :shipping_items="shipping_items"
        :count="count"
        :subtotal="subtotal"
        :shipping="shipping"
        :tax="tax"
        :total="total"
        :formatted="formatted"
        :formatPrice="formatPrice"
        :formatOptions="formatOptions"
        :updateCartItem="updateCartItem"
        :deleteCartItem="deleteCartItem"
        :clearCart="clearCart"
        :product_items_grouped="product_items_grouped"
        ></slot>
    </div>
    `
};

export default _cart;