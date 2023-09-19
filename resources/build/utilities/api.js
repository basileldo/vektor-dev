import axios from "axios";

import _config from "./config.js";
_config.init();

let _api = axios.create({
    headers: { "X-Requested-With": "XMLHttpRequest" },
    baseURL: _config.get("api.base").replace(/\/+$/, "") + "/",
});

_api.interceptors.request.use((config) => {
    return axios.request({
        url: _config.get("api.token"),
        method: "post",
        headers: { "Request-Action": config.url },
        data: { request_action: config.url }
    }).then((response) => {
        config.headers["CSRFP-Request"] = config.url;
        config.headers["CSRFP-Token"] = response.data;
        return config;
    });
}, (error) => {
    return Promise.reject(error);
});

const _pagination_properties = [
    "data",
    "per_page",
    "current_page",
    "last_page",
    "from",
    "to",
    "total",
];

let _storage = {
    isSuccess(response) {
        if (
            response
            && typeof(response.data) !== "undefined"
            && typeof(response.data.success) !== "undefined"
            && response.data.success == true
        ) {
            return true;
        }
        return false;
    },
    isError(response) {
        if (
            response
            && typeof(response.data) !== "undefined"
            && typeof(response.data.error) !== "undefined"
            && response.data.error == true
        ) {
            return true;
        }
        return false;
    },
    hasPaginationData(response) {
        let has_pagination_data = true;
        if (!response) {
            return false;
        }
        _pagination_properties.forEach((_pagination_property) => {
            if (typeof(response[_pagination_property]) === "undefined") {
                has_pagination_data = false;
            }
        });
        return has_pagination_data;
    },
    getPaginationData(response) {
        let pagination_data = {};
        _pagination_properties.forEach((_pagination_property) => {
            if (typeof(response[_pagination_property]) !== "undefined") {
                pagination_data[_pagination_property] = response[_pagination_property];
            }
        });
        return pagination_data;
    },
    getResponseData(response) {
        if (
            response
            && typeof(response.data) !== "undefined"
            && typeof(response.data.data) !== "undefined"
            && response.data.data != null
        ) {
            return response.data.data;
        }
        return null;
    },
    getResponseMessage(response) {
        if (
            response
            && typeof(response.data) !== "undefined"
        ) {
            if (this.isSuccess(response)) {
                return response.data.success_message;
            }

            if (this.isError(response)) {
                return response.data.error_message;
            }
        }
        return null;
    },
    request(method, url, callback, data) {
        if (typeof(url) !== "undefined" && typeof(method) !== "undefined") {
            let payload = {};
            if (typeof(data) !== "undefined" && typeof(data) === "object" && data) {
                payload = data;
            }
            payload.url = url;
            payload.method = method;

            return _api.request(payload).then((response) => {
                callback(response);
            }).catch((error) => {
                callback(error.response);
            });
        } else {
            return new Promise((resolve, reject) => {
                callback({
                    data: { data: {
                        success: false,
                        success_message: null,
                        error: true,
                        error_message: "Please include a url/method in your request",
                        http_code: null,
                        http_message: null,
                        data: null
                    }}
                });
                resolve();
            });
        }
    },
    get(url, callback, data) {
        return this.request("get", url, callback, data);
    },
    post(url, callback, data) {
        return this.request("post", url, callback, data);
    },
    put(url, callback, data) {
        return this.request("put", url, callback, data);
    },
    delete(url, callback, data) {
        return this.request("delete", url, callback, data);
    }
};

export {
    _api,
    _storage
};