let first_page = 1;

let _pagination = {
    name: "c-pagination",
    emits: [
        "change-pagination"
    ],
    props: {
        per_pages: {
            type: Array,
            default: [3, 6]
        },
        properties: {
            type: Object,
            default: {
                current_page: first_page,
                per_page: 1,
                last_page: null,
                from: null,
                to: null,
                total: null
            }
        }
    },
    data() {
        return {
            current_page_touched: false,
            per_page_touched: false,
            current_page: first_page,
            per_page: null,
            last_page: null,
            from: null,
            to: null,
            total: null
        };
    },
    watch: {
        "properties"(new_val) {
            const {
                current_page,
                per_page,
                last_page,
                from,
                to,
                total
            } = new_val;
            this.current_page = current_page;
            this.per_page = per_page;
            this.last_page = last_page;
            this.from = from;
            this.to = to;
            this.total = total;
        }
    },
    methods: {
        handleHistoryChange() {
            const _url_params = new URLSearchParams(window.location.search);
            let current_page = first_page;
            let per_page = this.per_pages[0];

            if (_url_params.get("page")) {
                current_page = parseInt(_url_params.get("page"));
            }

            if (_url_params.get("per_page")) {
                per_page = parseInt(_url_params.get("per_page"));
            }

            this.current_page = current_page;
            this.per_page = per_page;
            this.emitChanges();
        },
        updateFromUrl() {
            const _url =  new URL(window.location.origin + window.location.pathname);
            const _url_params = new URLSearchParams(window.location.search);
            const url_params = new URLSearchParams(window.location.search);
            let current_page = this.current_page;
            let per_page = this.per_page;

            if (_url_params.get("page")) {
                current_page = parseInt(_url_params.get("page"));
                if (current_page < first_page) {
                    current_page = first_page;
                }
                url_params.delete("page");
                url_params.set("page", current_page);
            }

            if (_url_params.get("per_page")) {
                per_page = parseInt(_url_params.get("per_page"));
                if (!this.per_pages.includes(per_page)) {
                    per_page = this.per_pages[0];
                }
                url_params.delete("per_page");
                url_params.set("per_page", per_page);
            }

            url_params.sort();
            _url.search = url_params.toString();

            this.current_page = current_page;
            this.per_page = per_page;
            window.history.replaceState({}, "", _url.toString());
            this.emitChanges();
        },
        updateToUrl() {
            const _url =  new URL(window.location.origin + window.location.pathname);
            const url_params = new URLSearchParams(window.location.search);
            if (this.current_page_touched) {
                url_params.delete("page");
                url_params.set("page", this.current_page);
            }
            if (this.per_page_touched) {
                url_params.delete("per_page");
                url_params.set("per_page", this.per_page);
            }

            url_params.sort();
            _url.search = url_params.toString();

            window.history.pushState({}, "", _url.toString());
        },
        emitChanges() {
            this.$emit("change-pagination", {
                page: this.current_page,
                per_page: this.per_page
            });
        },
        changePage() {
            this.current_page_touched = true;
            this.updateToUrl();
            this.emitChanges();
        },
        previousPage() {
            if (this.last_page !== first_page) {
                if (this.current_page === first_page) {
                    this.current_page = this.last_page;
                } else {
                    this.current_page--;
                }
                this.changePage();
            }
        },
        nextPage() {
            if (this.last_page !== first_page) {
                if (this.current_page === this.last_page) {
                    this.current_page = first_page;
                } else {
                    this.current_page++;
                }
                this.changePage();
            }
        },
        selectPage(value) {
            this.current_page = value;
            this.changePage();
        },
        selectPerPage(value) {
            this.per_page = value;
            const last_page = Math.ceil(this.total / this.per_page);
            if (this.current_page > last_page) {
                this.current_page = last_page;
            }
            this.per_page_touched = true;
            this.updateToUrl();
            this.emitChanges();
        }
    },
    created() {
        this.per_page = this.per_pages[0];
    },
    mounted() {
        window.addEventListener("popstate", this.handleHistoryChange);
        this.updateFromUrl();
    },
    template: `
    <div class="pagination">
        <div class="pagination__pages">
            <ul>
                <li>
                    <a @click="previousPage" rel="prev">Previous</a>
                </li>
                <li>
                    <select :value="current_page" @change="selectPage($event.target.value)">
                        <option v-for="page in last_page" :key="page" :value="page">{{ page }}</option>
                    </select>
                    <span>of {{ last_page }}</span>
                </li>
                <li>
                    <a @click="nextPage" rel="next">Next</a>
                </li>
            </ul>
        </div>
        <div class="pagination__per_page">
            <select :value="per_page" @change="selectPerPage($event.target.value)">
                <option :value="per_page" v-for="per_page in per_pages">{{ per_page }}</option>
            </select>
            <span>per page</span>
        </div>
    </div>
    `
};

export default _pagination;