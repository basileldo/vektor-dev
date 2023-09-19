let _panel_expand = {
    name: "c-panel_expand",
    props: {
        is_expanded: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        recalculateInnerHeight() {
            let inner_el = this.$el.querySelector(".expand__panel--inner");
            let elHeight = inner_el.offsetHeight;
            elHeight += parseInt(window.getComputedStyle(inner_el).getPropertyValue("margin-top"));
            elHeight += parseInt(window.getComputedStyle(inner_el).getPropertyValue("margin-bottom"));

            this.$el.style.maxHeight = elHeight + "px";
        }
    },
    mounted() {
        setTimeout(() => {
            this.recalculateInnerHeight();
        }, 10);

        setInterval(() => {
            this.recalculateInnerHeight();
        }, 1000);

        document.addEventListener("click", () => {
            setTimeout(() => {
                this.recalculateInnerHeight();
            }, 10);
        });

        window.addEventListener("resize", () => {
            this.recalculateInnerHeight();
        });
    },
    template: `
    <div class="expand__panel" :class="{ is_collapsed: !is_expanded }">
        <div class="expand__panel--inner">
            <slot></slot>
        </div>
    </div>
    `
};

export default _panel_expand;