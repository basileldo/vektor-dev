import _config from "../utilities/config.js";
_config.init();

import { _storage } from "../utilities/api.js";

let _products = {
    name: "c-products",
    props: {
        paginate: {
            type: Boolean,
            default: _config.get('checkout.pagination.enabled')
        },
        per_pages: {
            type: Array,
            default() {
                return _config.get('checkout.pagination.per_pages');
            }
        }
    },
    data() {
        return {
            is_loading: false,
            products_fetched: false,
            products: [],
            pagination: {},
        };
    },
    methods: {
        getProducts(data) {
            let payload = {};
            if (typeof(data) !== "undefined") {
                payload.data = data;
                if (this.paginate === true) {
                    payload.data.paginate = true;
                }
            }

            return _storage.post(_config.get("api.products.index"), (_response) => {
                if (_storage.isSuccess(_response)) {
                    let response = _storage.getResponseData(_response);
                    if (_storage.hasPaginationData(response.products)) {
                        let paginated_products = _storage.getPaginationData(response.products);
                        this.products = paginated_products.data;
                        delete paginated_products.data;
                        this.pagination = paginated_products;
                    } else {
                        this.products = response.products;
                    }
                    this.products_fetched = true;
                    this.is_loading = false;
                }
            }, payload);

            // return new Promise((resolve, reject) => {
            //     resolve();
            // });
        },
    },
    created() {
        this.is_loading = true;

        if (this.paginate == false && this.products != null && this.products.length == 0) {
            let products = _config.get("checkout.products.index");
            if (products.length > 0) {
                this.products = products;
                this.products_fetched = true;
                this.is_loading = false;
            } else {
                this.getProducts();
            }
        }
    },
    template: `
    <slot
        :is_loading="is_loading"
        :per_pages="per_pages"
        :paginate="paginate"

        :products_fetched="products_fetched"
        :products="products"
        :pagination="pagination"

        :getProducts="getProducts"
    ></slot>
    `
};

export default _products;