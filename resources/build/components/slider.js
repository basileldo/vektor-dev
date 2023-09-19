import { h } from "vue";

let _slider = {
    name: "c-slider",
    props: {
        trigger: {
            type: Boolean,
            default: false
        },
        position: {
            type: String,
            default: "right"
        },
        mode: {
            type: String,
            default: "r"
        }
    },
    data() {
        return {
            is_open: false,
        };
    },
    watch: {
        trigger: {
            handler(new_val, old_val) {
                if (new_val != old_val) {
                    if (new_val == true) {
                        this.openAction();
                    } else {
                        this.closeAction();
                    }
                }
            },
            immediate: true
        }
    },
    methods: {
        getMode() {
            switch (this.mode) {
                case "sm":
                case "lg":
                    return this.mode;
                default:
                    return "r";
            }
        },
        getModeR() {
            return getComputedStyle(this.$el).getPropertyValue("--mode").trim();
        },
        openAction() {
            this.$emit("open");
            this.is_open = true;
        },
        closeAction() {
            this.$emit("close");
            this.is_open = false;
        },
    },
    render() {
        var mode_class = null;
        switch (this.getMode()) {
            case "sm":
                mode_class = "document__slider--sm";
                break;
            case "lg":
                mode_class = "document__slider--lg";
                break;
            default:
                mode_class = "document__slider--r";
        }

        return h("div", {
            onClick(e) {
                e.stopPropagation();
            },
            class: "document__slider " + mode_class +
            ((this.position == "left") ? " document__slider--left" : "") +
            ((this.is_open == true) ? " document__slider--open" : "")
        }, this.$slots.default());
    }
};

export default _slider;