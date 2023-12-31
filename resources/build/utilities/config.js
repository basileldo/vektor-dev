function _config() {
    let data = {};
    return {
        get(name) {
            if (typeof(data[name]) !== "undefined") {
                return data[name];
            } else {
                return null;
            }
        },
        set(name, value) {
            data[name] = value;
        },
        setBaseUrl(value) {
            this.set("baseUrl", value);
        },
        getBaseUrl() {
            return this.get("baseUrl");
        },
        init() {
            if (typeof(_configParams) !== "undefined") {
                let configParams = Object.entries(_configParams);
                if (configParams.length > 0) {
                    for (let [name, value] of configParams) {
                        this.set(name, value);
                    }
                }
            }
        }
    };
}

export default _config();