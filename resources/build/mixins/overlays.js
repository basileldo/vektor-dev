let _overlay_mixin = {
    props: {
        trigger: {
            type: Boolean,
            default: false
        },
        required: {
            type: Boolean,
            default: false
        }
    },
    emits: [
        "open",
        "close",
    ],
    data() {
        return {
            is_open: false,
            body: document.getElementsByTagName("body").length > 0 ? document.getElementsByTagName("body")[0] : null,
            body_open_class: "overlay--is_open",
            body_scroll_position: 0,
            tabbable_elements: "[contenteditable], a, audio, button, details, input, select, summary, textarea, video"
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
        openAction() {
            this.is_open = true;
            if (this.body != null) {
                this.$nextTick(() => {
                    let has_body_open_class = this.body.classList.contains(
                        this.body_open_class
                    );
                    if (has_body_open_class == false) {
                        this.body_scroll_position = window.scrollY;
                        this.body.classList.add(this.body_open_class);
                        this.body.style.marginTop = (this.body_scroll_position * -1) + "px";
                    }
                    this.toggleTabbableChildren(true);
                });
            }
            this.$emit("open");
        },
        closeAction() {
            this.is_open = false;
            if (this.body != null) {
                this.$nextTick(() => {
                    let has_body_open_class = this.body.classList.contains(
                        this.body_open_class
                    );
                    let open_modals = document.querySelectorAll(".modal__overlay.is_open");
                    if (has_body_open_class == true && open_modals.length == 0) {
                        this.body.classList.remove(this.body_open_class);
                        this.body.style.marginTop = "";
                        window.scrollTo(0, this.body_scroll_position);
                        this.toggleTabbableChildren(false);
                    }
                });
            }
            this.$emit("close");
        },
        attemptCloseAction() {
            if (!this.required) {
                this.closeAction();
            }
        },
        onKeyPress(e) {
            if (this.is_open && e.keyCode === 27) {
                this.attemptCloseAction();
            }
        },
        toggleTabbableChildren(on) {
            this.$nextTick(() => {
                if (this.$el) {
                    let body_tabbable_children = this.body.querySelectorAll(this.tabbable_elements);
                    if (body_tabbable_children.length > 0) {
                        for (var a = 0; a < body_tabbable_children.length; a++) {
                            if (on == true) {
                                body_tabbable_children[a].setAttribute("tabindex", "-1");
                            } else {
                                body_tabbable_children[a].removeAttribute("tabindex");
                            }
                        }
                    }
                    let overlay_tabbable_children = this.$el.querySelectorAll(this.tabbable_elements);
                    if (overlay_tabbable_children.length > 0) {
                        for (var b = 0; b < overlay_tabbable_children.length; b++) {
                            if (on == true) {
                                overlay_tabbable_children[b].removeAttribute("tabindex");
                            } else {
                                overlay_tabbable_children[b].setAttribute("tabindex", "-1");
                            }
                        }
                    }
                }
            });
        }
    },
    created() {
        document.addEventListener("keydown", this.onKeyPress);
    },
    mounted() {
        this.$nextTick(() => {
            if (this.$el) {
                let overlay_tabbable_children = this.$el.querySelectorAll(this.tabbable_elements);
                if (overlay_tabbable_children.length > 0) {
                    for (var b = 0; b < overlay_tabbable_children.length; b++) {
                        overlay_tabbable_children[b].setAttribute("tabindex", "-1");
                    }
                }
            }
        });
    },
    unmounted() {
        document.removeEventListener("keydown", this.onKeyPress);
    }
};

export default _overlay_mixin;