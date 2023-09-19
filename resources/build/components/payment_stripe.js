import _config from "../utilities/config.js";
_config.init();

import { _api } from "../utilities/api.js";
import { _confirmation as c_confirmation } from "./overlays.js";

let _payment_stripe = {
    name: "c-payment_stripe",
    components: {
        "c-confirmation": c_confirmation,
    },
    props: {
        can_save_cards: {
            default: false
        },
        is_valid: {
            default: true
        },
        customer_id: {
            default: null
        },
        billing_address_city: {
            default: null
        },
        billing_address_country: {
            default: null
        },
        billing_address_line1: {
            default: null
        },
        billing_address_line2: {
            default: null
        },
        billing_address_postal_code: {
            default: null
        },
        billing_address_state: {
            default: null
        },
        billing_email: {
            default: null
        },
        billing_name: {
            default: null
        },
        billing_phone: {
            default: null
        },
        amount: {
            required: true
        },
        additional_data: {
            default: null
        }
    },
    data() {
        return {
            can_off_session: false,
            customer_cards: [],
            is_disabled: false,
            stripe: null,
            stripe_elements: null,
            stripe_card: null,
            stripe_card_element: null,
            is_card_valid: false,
            empty_amount: "0.00",
            save_card: false,
            off_session: false,
            selected_customer_card: null
        };
    },
    watch: {
        billing_address_postal_code: {
            handler(new_val, old_val) {
                if (this.stripe_card && new_val != old_val && new_val != null) {
                    let stripe_elements_options = {
                        hidePostalCode: new_val != "",
                    };
                    stripe_elements_options.value = {
                        postalCode: (new_val != "") ? new_val : ""
                    };
                    this.stripe_card.update(stripe_elements_options);
                }
            },
            immediate: true
        },
    },
    methods: {
        transformErrorResponse(response) {
            return {
                success: false,
                success_message: null,
                error: true,
                error_message: response.error.message,
                http_code: 401,
                http_message: response.error.code,
                data: response.error
            };
        },
        handlePaymentResponse(response) {
            if (response.data.data != null && response.data.error == true) {
                this.is_disabled = false;
                this.selected_customer_card = null;
                this.$emit("fail", response);
            } else if (response.data.data != null && response.data.data.requires_action) {
                this.stripe
                    .handleCardAction(response.data.data.intent_client_secret)
                    .then((result) => {
                        if (result.error) {
                            this.is_disabled = false;
                            this.selected_customer_card = null;
                            this.$emit("fail", this.transformErrorResponse(result));
                        } else {
                            let data = {
                                save_card: this.save_card,
                                off_session: this.off_session,
                                amount: this.amount_pence,
                                amount_display: this.amount_display,
                                amount_pence: this.amount_pence,
                                amount_pounds: this.amount_pounds,
                                payment_intent_id: result.paymentIntent.id,
                                customer_id: response.data.data.customer
                            };

                            if (this.additional_data) {
                                Object.entries(this.additional_data).forEach(additional_data_item => {
                                    const [prop, value] = additional_data_item;
                                    if (typeof(data[prop]) === "undefined") {
                                        data[prop] = value;
                                    }
                                });
                            }

                            _api.request({
                                url: _config.get("api.payment_stripe.payment_intent"),
                                method: "post",
                                data: data
                            })
                            .then((response) => {
                                this.is_disabled = false;
                                this.selected_customer_card = null;
                                this.handlePaymentResponse(response);
                            })
                            .catch((error) => {
                                this.handlePaymentResponse(error.response);
                            });
                        }
                    });
            } else {
                this.stripe_card.clear();
                this.save_card = false;
                this.off_session = false;
                this.is_disabled = false;
                this.selected_customer_card = null;
                this.$emit("success", response);
            }
        },
        getCustomerCards() {
            if (this.can_save_cards && this.customer_id) {
                let data = {
                    customer_id: this.customer_id
                };

                _api.request({
                    url: _config.get("api.payment_stripe.get_customers_cards"),
                    method: "post",
                    data: data
                })
                .then((response) => {
                    response.data.data.customer_cards.forEach((customer_card) => {
                        customer_card.is_loading = false;
                        customer_card.confirm_deletion = false;
                    });
                    this.customer_cards = response.data.data.customer_cards;
                    this.$emit("unload");
                })
            }
        },
        selectCustomerCard(customer_card) {
            if (this.selected_customer_card != null && this.selected_customer_card.id == customer_card.id) {
                this.selected_customer_card = null;
                this.stripe_card.update({ disabled: false });
            } else {
                this.selected_customer_card = customer_card;
                this.stripe_card.update({ disabled: true });
                this.save_card = false;
            }
        },
        deleteCustomerCard(selected_customer_card) {
            selected_customer_card.confirm_deletion = false;
            selected_customer_card.is_loading = true;

            let data = {
                id: selected_customer_card.id
            };

            _api.request({
                url: _config.get("api.payment_stripe.delete_customers_cards"),
                method: "delete",
                data: data
            })
            .then((response) => {
                selected_customer_card.is_loading = false;
                if (response.data.success == true) {
                    this.customer_cards.forEach((customer_card, customer_card_index) => {
                        if (selected_customer_card.id == customer_card.id) {
                            this.customer_cards.splice(customer_card_index, 1);
                        }
                    });
                }
            });
        },
        submit() {
            this.$emit("validate");

            if (this.is_disabled || !this.is_valid) {
                return;
            }

            this.is_disabled = true;
            this.$emit("load");

            let stripe_payment_method_options = {
                type: "card",
                card: this.stripe_card
            };

            stripe_payment_method_options.billing_details = {
                address: {
                    city: null,
                    country: null,
                    line1: null,
                    line2: null,
                    postal_code: null,
                    state: null
                },
                email: null,
                name: null,
                phone: null
            };

            Object.entries(stripe_payment_method_options.billing_details).forEach(billing_detail => {
                const [billing_detail_property, billing_detail_value] = billing_detail;
                if (billing_detail_property == "address") {
                    Object.entries(billing_detail_value).forEach(billing_address_detail => {
                        const [billing_address_detail_property, billing_address_detail_value] = billing_address_detail;
                        if (this["billing_address_" + billing_address_detail_property]) {
                            stripe_payment_method_options.billing_details.address[billing_address_detail_property] = this["billing_address_" + billing_address_detail_property];
                        }
                    });
                } else {
                    if (this["billing_" + billing_detail_property]) {
                        stripe_payment_method_options.billing_details[billing_detail_property] = this["billing_" + billing_detail_property];
                    }
                }
            });

            if (this.selected_customer_card) {

                let data = {
                    save_card: false,
                    off_session: this.off_session,
                    amount: this.amount_pence,
                    amount_display: this.amount_display,
                    amount_pence: this.amount_pence,
                    amount_pounds: this.amount_pounds,
                    payment_method_id: this.selected_customer_card.id,
                    customer_id: this.customer_id
                };

                if (this.additional_data) {
                    Object.entries(this.additional_data).forEach(additional_data_item => {
                        const [prop, value] = additional_data_item;
                        if (typeof(data[prop]) === "undefined") {
                            data[prop] = value;
                        }
                    });
                }

                _api.request({
                    url: _config.get("api.payment_stripe.payment_intent"),
                    method: "post",
                    data: data
                })
                .then((response) => {
                    this.is_disabled = false;
                    this.handlePaymentResponse(response);
                })
                .catch((error) => {
                    this.handlePaymentResponse(error.response);
                });

            } else {

                this.stripe
                    .createPaymentMethod(stripe_payment_method_options)
                    .then((result) => {
                        if (result.error) {
                            this.is_disabled = false;
                            this.$emit("fail", this.transformErrorResponse(result));
                        } else {
                            let data = {
                                save_card: this.save_card,
                                off_session: this.off_session,
                                amount: this.amount_pence,
                                amount_display: this.amount_display,
                                amount_pence: this.amount_pence,
                                amount_pounds: this.amount_pounds,
                                payment_method_id: result.paymentMethod.id
                            };

                            if (this.customer_id) {
                                data.customer_id = this.customer_id;
                            }

                            if (this.additional_data) {
                                Object.entries(this.additional_data).forEach(additional_data_item => {
                                    const [prop, value] = additional_data_item;
                                    if (typeof(data[prop]) === "undefined") {
                                        data[prop] = value;
                                    }
                                });
                            }

                            _api.request({
                                url: _config.get("api.payment_stripe.payment_intent"),
                                method: "post",
                                data: data
                            })
                            .then((response) => {
                                this.is_disabled = false;
                                this.handlePaymentResponse(response);
                            })
                            .catch((error) => {
                                this.handlePaymentResponse(error.response);
                            });
                        }
                    });
            }
        }
    },
    computed: {
        amount_display() {
            let amount_display = this.empty_amount;
            let amount = this.amount;
            amount = amount.toString().replace(/[^0-9\.]/g, "");
            if (amount != "") {
                amount_display = parseFloat(amount).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            }
            return amount_display;
        },
        amount_pence() {
            return this.amount_display == this.empty_amount ? 0 : this.amount_display.toString().replace(/[^0-9]/g, "");
        },
        amount_pounds() {
            return this.amount_display == this.empty_amount ? 0 : this.amount_display.toString().replace(/[^0-9\.]/g, "");
        }
    },
    mounted() {
        this.stripe_card_element = this.$el.querySelector(".stripe_card_element");

        if (typeof(this.stripe_card_element) !== "undefined") {
            let stripe_public_key = _config.get("payments.stripe.public_key");

            this.stripe = Stripe(stripe_public_key);

            this.stripe_elements = this.stripe.elements();

            let stripe_elements_options = {
                hidePostalCode: this.billing_address_postal_code != "",
                postalCode: (this.billing_address_postal_code != "") ? this.billing_address_postal_code : "",
                style: {
                    base: {
                        fontFamily: "Lato, sans-serif",
                        fontSize: "16px",
                        fontVariant: "normal",
                        color: "#00374b",
                        lineHeight: "30px",
                        fontSmoothing: "antialiased"
                    },
                    invalid: {
                        color: "#cc0000"
                    }
                }
            };

            this.stripe_card = this.stripe_elements.create("card", stripe_elements_options);

            this.stripe_card.mount(this.stripe_card_element);

            this.stripe_card.on("ready", (e) => {
                this.getCustomerCards();
            });

            this.stripe_card.on("change", (e) => {
                if (e.complete == true) {
                    this.is_card_valid = true;
                } else {
                    this.is_card_valid = false;
                }
            });
        }
    },
    template: `
    <div class="field__wrapper--card">
        <label class="field__title" v-if="customer_cards.length > 0">Select A Saved Card</label>
        <div class="customer_cards_wrapper amex diners discover jcb maestro mastercard unionpay visa" v-if="customer_cards.length > 0">
            <div class="customer_card" :class="[customer_card.brand, { is_selected: selected_customer_card && selected_customer_card.id == customer_card.id }]" v-for="customer_card in customer_cards" @click.stop="selectCustomerCard(customer_card)">
            <div class="customer_card__inner">
            <c-confirmation :trigger="customer_card.confirm_deletion" @open="customer_card.confirm_deletion = true" @close="customer_card.confirm_deletion = false" confirm="Delete" cancel="Cancel" @confirm="deleteCustomerCard(customer_card)">
                <h3>Delete Saved Card</h3><p>Are you sure you"d like to delete your saved card?</p>
            </c-confirmation>
            <div class="spinner__wrapper spinner--absolute" :class="{ is_loading: customer_card.is_loading == true }">
                <div class="spinner"></div>
            </div>
            <div class="customer_card__dismiss" @click.stop="customer_card.confirm_deletion = true"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve"><polygon points="42.9,50 50,42.9 32.1,25 50,7.1 42.9,0 25,17.9 7.1,0 0,7.1 17.9,25 0,42.9 7.1,50 25,32.1" class="customer_card__dismiss__icon"></polygon></svg></div><span class="customer_card__expiry"><small>Expiry Date:</small> {{ customer_card.expiry }}</span><span class="customer_card__number">{{ customer_card.number }}</span></div></div>
        </div>
        <div class="field__wrapper">
            <label class="field__title">Enter {{ (customer_cards.length > 0) ? "New " : "" }}Card Details</label>
            <div class="field stripe_card_element"></div>
        </div>
        <a class="btn bg-primary border-primary text-primary_contrasting block" :class="{ is_disabled: is_disabled || !is_valid || (!is_card_valid && selected_customer_card == null) }" @click.stop="submit">Pay £{{ amount_pounds }}</a>
        <div class="field__collection mt-4" v-if="can_save_cards">
            <div class="field__wrapper">
                <label class="checkbox"><input aria-label="Save card for later?" name="save_card" v-model="save_card" type="checkbox" :disabled="selected_customer_card != null"><span class="text" :disabled="selected_customer_card != null">Save card for later?</span></label>
            </div>
            <div class="field__wrapper" v-if="can_off_session">
                <label class="checkbox"><input aria-label="Charge Off Session?" name="off_session" v-model="off_session" type="checkbox"><span class="text">Charge Off Session?</span></label>
            </div>
        </div>
    </div>
    `
};

export default _payment_stripe;