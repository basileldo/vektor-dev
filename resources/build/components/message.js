let _message = {
    name: "c-message",
    props: {
        autohide: {
            type: Boolean,
            default: false
        },
        trigger: {
            type: Boolean,
            default: false
        },
        content: {
            default: " "
        },
        level: {
            default: null
        },
        required: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        trigger: {
            handler(new_val, old_val) {
                if (new_val != old_val) {
                    if (new_val == true) {
                        this.showAction();
                    } else {
                        this.hideAction();
                    }
                }
            },
            immediate: true
        }
    },
    data() {
        return {
            is_shown: false
        }
    },
    methods: {
        showAction() {
            if (this.content) {
                this.is_shown = true;
                if (this.autohide == true) {
                    setTimeout(() => {
                        this.hideAction();
                    }, 5000);
                }
            }
        },
        hideAction() {
            this.is_shown = false;
        }
    },
    template: `
    <div class="message" :class="[{ is_hidden: !is_shown }]">
        <div class="message__inner">
            <div class="message__dismiss" @click="hideAction" v-if="required == false">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve"><polygon points="42.9,50 50,42.9 32.1,25 50,7.1 42.9,0 25,17.9 7.1,0 0,7.1 17.9,25 0,42.9 7.1,50 25,32.1 " class="message__dismiss__icon"></polygon></svg>
            </div>
            <div class="message__content" v-html="content"></div>
        </div>
    </div>
    `
};

export default _message;