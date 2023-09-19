import _config from "../utilities/config.js";
_config.init();

import _overlay_mixin from "../mixins/overlays.js";

let _search = {
    name: "c-search",
    emits: [
        "open",
        "close",
        "change-search",
    ],
    data() {
        return {
            search_touched: false,
            s: ""
        };
    },
    mixins: [_overlay_mixin],
    methods: {
        handleHistoryChange() {
            const _url_params = new URLSearchParams(window.location.search);
            let s = this.s;

            if (_url_params.get("s")) {
                s = _url_params.get("s");
                this.openAction();
            } else {
                this.closeAction();
            }

            this.s = s;
            this.emitChanges();
        },
        updateFromUrl() {
            const _url =  new URL(window.location.origin + window.location.pathname);
            const _url_params = new URLSearchParams(window.location.search);
            const url_params = new URLSearchParams(window.location.search);
            let s = this.s;

            if (_url_params.get("s")) {
                s = _url_params.get("s");
                url_params.delete("s");
                url_params.set("s", s);
                this.openAction();
            } else {
                this.closeAction();
            }

            url_params.sort();
            _url.search = url_params.toString();

            this.s = s;
            window.history.replaceState({}, "", _url.toString());
            this.emitChanges();
        },
        updateToUrl() {
            const _url =  new URL(window.location.origin + window.location.pathname);
            const url_params = new URLSearchParams(window.location.search);
            if (this.search_touched) {
                url_params.delete("s");
                if (this.s != '') {
                    url_params.set("s", this.s);
                }
            }

            url_params.sort();
            _url.search = url_params.toString();

            window.history.pushState({}, "", _url.toString());
        },
        emitChanges() {
            this.$emit("change-search", {
                s: this.s
            });
        },
        select(suggestion) {
            this.s = suggestion.value;

            this.search();
        },
        search() {
            this.search_touched = true;
            this.updateToUrl();
            this.closeAction();
        }
    },
    mounted() {
        window.addEventListener("popstate", this.handleHistoryChange);
        this.updateFromUrl();
    },
    template: `
    <div class="search__overlay" :aria-hidden="!is_open" :class="{ is_open: is_open == true }" @click.stop="closeAction">
        <div class= "search__dialog">
            <div class="search__inner" @click.stop>
                <header class="search__header">
                    <h3>Search</h3>
                    <div class="search__dismiss" @click.stop="closeAction">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><polygon class="search__dismiss__icon" points="42.9,50 50,42.9 32.1,25 50,7.1 42.9,0 25,17.9 7.1,0 0,7.1 17.9,25 0,42.9 7.1,50 25,32.1 "></polygon>
                        </svg>
                    </div>
                </header>
                <div class="search__content">
                    <slot
                    :s="s"
                    :select="select"
                    :search="search"
                    ></slot>
                </div>
            </div>
        </div>
    </div>
    `
};

export default _search;