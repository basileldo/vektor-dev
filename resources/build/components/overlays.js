import _overlay_mixin from "../mixins/overlays.js";

let _modal = {
    name: "c-modal",
    emits: [
        "open",
        "close",
    ],
    mixins: [_overlay_mixin],
    template: `
    <div class="modal__overlay" :aria-hidden="!is_open" :class="[{ is_open: is_open == true }]" @click.stop="attemptCloseAction">
        <div class= "modal__dialog">
            <div class="modal__inner" @click.stop>
                <div class="modal__dismiss" @click.stop="attemptCloseAction" v-if="required == false">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><polygon class="modal__dismiss__icon" points="42.9,50 50,42.9 32.1,25 50,7.1 42.9,0 25,17.9 7.1,0 0,7.1 17.9,25 0,42.9 7.1,50 25,32.1"></polygon>
                    </svg>
                </div>
                <div class="modal__content">
                    <slot></slot>
                </div>
                <div class="modal__content__after">
                    <slot name="after_content"></slot>
                </div>
            </div>
        </div>
    </div>
    `
};

let _confirmation = {
    name: "c-confirmation",
    emits: [
        "open",
        "close",
    ],
    mixins: [_overlay_mixin],
    props: {
        confirm: {
            type: String,
            default: ""
        },
        cancel: {
            type: String,
            default: ""
        }
    },
    methods: {
        confirmAction() {
            this.$emit("confirm");
            this.closeAction();
        },
        cancelAction() {
            this.$emit("cancel");
            this.closeAction();
        },
        escapeAction() {
            this.cancelAction();
        }
    },
    template: `
    <div class="modal__overlay" :aria-hidden="!is_open" :class="{ is_open: is_open == true }" @click.stop="cancelAction">
        <div class= "modal__dialog">
            <div class="modal__inner" @click.stop>
                <div class="modal__dismiss" @click.stop="cancelAction">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><polygon class="modal__dismiss__icon" points="42.9,50 50,42.9 32.1,25 50,7.1 42.9,0 25,17.9 7.1,0 0,7.1 17.9,25 0,42.9 7.1,50 25,32.1"></polygon>
                    </svg>
                </div>
                <div class="modal__content">
                    <slot></slot>
                </div>
                <div class="modal__action">
                    <div class="btn__collection btn__collection--toolbar"><a href="#" class="btn bg-secondary border-secondary text-secondary_contrasting rounded-none block flex-grow" @click.stop.prevent="cancelAction" v-html="cancel"></a><a href="#" class="btn bg-primary border-primary text-primary_contrasting rounded-none block flex-grow" @click.stop.prevent="confirmAction" v-html="confirm"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
};

export {
    _modal,
    _confirmation
};