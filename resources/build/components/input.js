import { computed } from "vue";

import _config from "../utilities/config.js";
_config.init();

import { _storage } from "../utilities/api.js";

let _input = {
    name: "c-input",
    emits: [
        "update:model-value",
        "update:modelValue",
        "fetch",
        "select",
        "nosuggestions",
    ],
    props: {
        label: {
            type: String,
            default: ""
        },
        hint: {
            type: String,
            default: ""
        },
        select_label: {
            type: String,
            default: ""
        },
        name: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: "text"
        },
        placeholder: {
            type: String
        },
        pattern: {
            type: String
        },
        increment: {
            type: Number,
            default: 1
        },
        highest_value: {
            type: Number,
            default: 999999999
        },
        lowest_value: {
            type: Number,
            default: 0
        },
        value: {
            type: [Number, String]
        },
        value_tmp: {
            type: String
        },
        valuelabel: {
            type: String
        },
        maxlength: {
            type: Number
        },
        collection: {
            type: Boolean,
            default: false
        },
        modelValue: {
            required: true
        },
        validationrule: {
            type: Object
        },
        validationmsg: {
            type: Object
        },
        autocomplete: {
            type: String
        },
        options: {
            type: Array,
            default() {
                return [];
            }
        },
        suggestions: {
            type: Array,
            default() {
                return [];
            }
        },
        suggestions_model: {
            type: Boolean,
            default: true
        },
        nosuggestions: {
            type: Boolean,
            default: true
        },
        autotab: {
            type: Boolean,
            default: false
        },
        autofocus: {
            type: Boolean,
            default: false
        },
        endpoint: {
            type: String
        },
        preview: {
            type: Boolean,
            default: false
        },
        readonly: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
    },
    setup(props, context) {
        let publicProperties = {};

        const model = computed({
            get() {
                return props.modelValue;
            },
            set(value) {
                return context.emit("update:modelValue", value);
            }
        });

        publicProperties.model = model;

        return publicProperties;
    },
    data() {
        return {
            uid: Date.now().toString(36) + Math.random().toString(36).substring(2),
            suggestions_pending: true,
            suggestion_idx: -1,
            suggestions_open: false,
            suggestion_value: "",
            password_shown: false,
            file: null,
            file_name: null,
            file_path: null,
            file_progress: 0,
        };
    },
    watch: {
        modelValue: {
            handler(new_val, old_val) {
                if (this.isAutocomplete() && new_val != old_val) {
                    let suggestion_found = this.findMatchingSuggestion(new_val);
                    if (suggestion_found) {
                        this.suggestion_value = suggestion_found.text;
                    } else {
                        if (this.suggestions_model == false && this.suggestion_value != "") {
                            this.suggestion_value = new_val;
                        }
                    }
                }
            },
            immediate: true
        },
        value_tmp(new_val, old_val) {
            if (new_val != old_val) {
                if (new_val == null) {
                    new_val = "";
                }
                this.suggestion_value = new_val;
            }
        },
        suggestions: {
            handler(new_val, old_val) {
                if (this.isAutocomplete() && new_val != old_val) {
                    this.suggestions_pending = false;

                    if (new_val.length > 0 && this.modelValue != "" && this.suggestion_value == "") {
                        let suggestion_found = this.findMatchingSuggestion(this.modelValue);
                        if (suggestion_found) {
                            this.suggestion_value = suggestion_found.text;
                        } else {
                            if (this.suggestions_model == false && this.suggestion_value == "") {
                                this.suggestion_value = this.modelValue;
                            }
                        }
                    }
                }
            },
            immediate: true
        },
        options: {
            handler(new_val, old_val) {
                if ((this.isSelect() && (new_val && new_val.length == 1)) || this.isSelectColors()) {
                    this.updateValue(new_val[0].value);
                }
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        focusNextElement() {
            let focussableElements = "a:not([disabled]), button:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex='-1'])";
            if (document.activeElement) {
                let focussable = Array.prototype.filter.call(document.querySelectorAll(focussableElements), (element) => {
                    return element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement
                });
                let index = focussable.indexOf(document.activeElement);
                if (index > -1) {
                    let nextElement = focussable[index + 1] || focussable[0];
                    nextElement.focus();
                }
            }
        },
        triggerValidation() {
            if (typeof(this.validationrule) !== "undefined") {
                this.validationrule.$touch();
            }
        },
        clearFile() {
            this.file = null;
            this.file_progress = 0;
            this.file_name = null;
            this.file_path = null;
            this.updateValue(null);
        },
        handleFileUpload(event) {
            if (typeof(event.target.files[0]) !== "undefined") {
                this.file = event.target.files[0];
                this.file_name = this.file.name;
                let formData = new FormData();
                formData.append("file", this.file);

                let endpoint = _config.get(this.endpoint);

                _storage.post((endpoint !== null) ? endpoint : this.endpoint, (_response) => {
                    if (_storage.isSuccess(_response)) {
                        let response = _storage.getResponseData(_response);
                        this.updateValue(response.file_name);
                        this.file_path = response.file_path;
                    }
                }, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                    data: formData,
                    onUploadProgress(progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        this.file_progress = percentCompleted;
                    }
                });
            } else {
                this.clearFile();
            }
        },
        updateValue(value) {
            if (this.isNumber() || this.isNumberWithButtons()) {
                if (isNaN(value) || value == "") {
                    value = 0;
                }
                if (value < this.lowest_value) {
                    value = this.lowest_value;
                }
                if (value > this.highest_value) {
                    value = this.highest_value;
                }
                if (this.increment > 1) {
                    let old_value = parseInt(value);
                    let higher_value = Math.ceil(old_value / this.increment) * this.increment;
                    let lower_value = Math.floor(old_value / this.increment) * this.increment;

                    if (higher_value !== old_value && lower_value !== old_value) {

                        let distance_to_higher = Math.abs(higher_value - old_value);
                        let distance_to_lower = Math.abs(lower_value - old_value);

                        let shortest_distance = Math.min(distance_to_higher,distance_to_lower);
                        if (distance_to_higher == shortest_distance) {
                            value = higher_value;
                        }
                        if (distance_to_lower == shortest_distance) {
                            value = lower_value;
                        }
                    }
                }
            }
            this.$emit("update:modelValue", value);
            if (this.isNumberMaxLength()) {
                if (value.length >= this.maxlength && this.autotab == true) {
                    this.$nextTick(() => {
                        this.focusNextElement();
                    });
                }
            }
        },
        updateValueTmp(e) {
            let value = e.target.value;
            this.suggestion_idx = -1;
            this.suggestions_open = value != "";
            this.suggestion_value = value;
            this.$emit("fetch", value);
            if (this.suggestions_f.length == 0 && typeof(this.$attrs.onFetch) !== "undefined") {
                this.suggestions_pending = true;
            }
            if (this.suggestion_value != "") {
                let suggestion_found = this.suggestions_f.filter((suggestion) => {
                    return suggestion.text.toLowerCase().trim() == this.suggestion_value.toLowerCase().trim();
                });
                if (suggestion_found.length == 0) {
                    this.updateValue("");
                    this.triggerValidation();
                }
            }
        },
        selectValue(suggestion) {
            this.updateValue(suggestion.value);
            this.triggerValidation();
            this.suggestion_idx = -1;
            this.suggestions_open = false;
            this.suggestion_value = suggestion.text;
            this.$emit("select", suggestion);
        },
        clearValue() {
            this.updateValue("");
            this.triggerValidation();
            this.suggestion_value = "";
        },
        changeSuggestionIdx(idx) {
            this.suggestion_idx = idx;
        },
        scrollSuggestionIntoView() {
            setTimeout(() => {
                let selected_option = this.$el.querySelector(".field__suggestions li.js__hovered");
                if (selected_option) {
                    selected_option.scrollIntoView({behavior: "smooth", block: "center"});
                }
            }, 0);
        },
        onArrowDown() {
            if (this.suggestion_idx < this.suggestions_f.length - 1) {
                this.suggestion_idx = this.suggestion_idx + 1;
            } else {
                this.suggestion_idx = 0;
            }
            this.scrollSuggestionIntoView();
        },
        onArrowUp() {
            if (this.suggestion_idx > 0) {
                this.suggestion_idx = this.suggestion_idx - 1;
            } else {
                this.suggestion_idx = this.suggestions_f.length - 1;
            }
            this.scrollSuggestionIntoView();
        },
        findMatchingSuggestion(value) {
            let matching_suggestions = this.suggestions.filter((suggestion) => {
                let suggestion_value = suggestion.value;
                if (typeof(suggestion_value) == "string") {
                    suggestion_value = suggestion_value.trim();
                }
                let suggestion_text = suggestion.text;
                if (typeof(suggestion_text) == "string") {
                    suggestion_text = suggestion_text.trim();
                }
                if (typeof(value) == "string") {
                    value = value.trim();
                }
                return suggestion_value == value || suggestion_text == value;
            });

            if (matching_suggestions.length > 0) {
                return matching_suggestions[0];
            }
            return null;
        },
        selectSuggestion(e) {
            if (this.suggestion_value != "") {
                if (this.suggestion_idx == -1) {
                    if (this.suggestions_model == true) {
                        let suggestion_found = this.suggestions_f.filter((suggestion) => {
                            return suggestion.text.toLowerCase().trim() == this.suggestion_value.toLowerCase().trim();
                        });
                        if (suggestion_found.length > 0) {
                            if (this.suggestions_open == true && (this.model == null || this.model == "")) {
                                e.preventDefault();
                            }
                            this.selectValue(suggestion_found[0]);
                        }
                    } else {
                        this.selectValue({
                            value: this.suggestion_value,
                            text: this.suggestion_value,
                        });
                    }
                } else {
                    if (this.suggestions_open == true && (this.model == null || this.model == "")) {
                        e.preventDefault();
                    }
                    this.selectValue(this.suggestions_f[this.suggestion_idx]);
                }
            }
        },
        triggerInnerField(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                e.target.click();
            }
            if (this.isCheckbox() || this.isSwitch()) {
                if (e.keyCode == 37) {
                    this.model = false;
                }
                if (e.keyCode == 39) {
                    this.model = true;
                }
            }
        },
        labelIsEmpty() {
            return this.label === "";
        },
        hintIsEmpty() {
            return this.hint === "";
        },
        selectLabelIsEmpty() {
            return this.select_label === "";
        },
        errorIsActive() {
            if (typeof(this.validationrule) !== "undefined" && this.validationrule.$error === true) {
                return true;
            }
            return false;
        },
        minusNumber() {
            let value = isNaN(parseInt(this.model)) ? (this.lowest_value + this.increment) : parseInt(this.model);

            if (value > this.lowest_value) {
                this.updateValue(value - this.increment);
            }
        },
        plusNumber() {
            let value = isNaN(parseInt(this.model)) ? this.lowest_value : parseInt(this.model);

            if (value < this.highest_value) {
                this.updateValue(value + this.increment);
            }
        },
        isRequired() {
            if (typeof(this.validationrule) !== "undefined" && typeof(this.validationrule.required) !== "undefined") {
                return true;
            }
            return false;
        },
        isValidType() {
            let valid_types = ["email", "password", "search", "text", "number", "number:maxlength", "number:buttons", "tel", "radio", "checkbox", "switch", "select", "select:colors", "select:swatches", "textarea", "autocomplete", "file"];
            return valid_types.indexOf(this.type) !== -1;
        },
        isInput() {
            let valid_types = ["email", "search", "text"];
            return valid_types.indexOf(this.type) !== -1;
        },
        isPassword() {
            return this.type == "password";
        },
        isNumber() {
            return this.type == "number";
        },
        isNumberMaxLength() {
            return this.type == "number:maxlength";
        },
        isNumberWithButtons() {
            return this.type == "number:buttons";
        },
        isTel() {
            return this.type == "tel";
        },
        isRadio() {
            return this.type == "radio";
        },
        isCheckbox() {
            return this.type == "checkbox";
        },
        isSwitch() {
            return this.type == "switch";
        },
        isSelect() {
            return this.type == "select" && this.options.length > 0;
        },
        isSelectColors() {
            return this.type == "select:colors" && this.options.length > 0;
        },
        isSelectSwatches() {
            return this.type == "select:swatches" && this.options.length > 0;
        },
        isTextarea() {
            return this.type == "textarea";
        },
        isAutocomplete() {
            return this.type == "autocomplete";
        },
        isFile() {
            return this.type == "file";
        },
        isOptionDisabled(option) {
            if (typeof(option.disabled) !== "undefined" && option.disabled == true) {
                if (option.value == this.model) {
                    this.updateValue(null);
                }
                return true;
            }
            return false;
        },
        isOptionGroup(option) {
            if (typeof(option.group) !== "undefined" && option.group != "no") {
                return true;
            }
            return false;
        },
        hasSubOptions(option) {
            if (typeof(option.options) !== "undefined" && option.options.length > 0) {
                return true;
            }
            return false;
        },
        hasLongSwatchLabel(option) {
            if (option.label.length > 3) {
                return true;
            }
            return false;
        },
        getSwatchColor(option) {
            if (option.configuration !== undefined && option.configuration.color !== undefined) {
                return option.configuration.color;
            }
            return null;
        }
    },
    computed: {
        uid_name() {
            return this.uid + "_" + this.name;
        },
        error() {
            if (this.errorIsActive() === true) {
                let validation_params = this.validationrule.$errors;
                if (validation_params.length > 0) {
                    for (let i = 0; i < validation_params.length; i++) {
                        let validation_param = validation_params[i];
                        let validation_name = null;
                        if (typeof(validation_param.$params.type) !== "undefined") {
                            if (typeof(validation_param.$validator) !== "undefined") {
                                validation_name = validation_param.$validator;
                            }
                            let validation_type = validation_param.$params.type;
                            let message = "";
                            let verb = "";
                            let params = [this.label.toLowerCase()];
                            for (let param_property in validation_param.$params) {
                                if (validation_param.$params.hasOwnProperty(param_property) && param_property != "type") {
                                    params.push(validation_param.$params[param_property]);
                                }
                            }
                            switch (this.type) {
                                case "radio":
                                case "checkbox":
                                case "switch":
                                case "select":
                                    verb = "select";
                                    break;
                                default:
                                    verb = "enter";
                                    break;
                            }
                            switch (validation_type) {
                                case "required":
                                case "requiredIf":
                                    message = "Please " + verb + " " + ((params[0].match(/^(a|e|i|o|u)/gi) == null) ? "a" : "an") + " " + params[0];
                                    break;
                                case "minLength":
                                    message = "The " + params[0] + " must contain at least " + params[1] + " characters";
                                    break;
                                case "maxLength":
                                    message = "The " + params[0] + " must contain at most " + params[1] + " characters";
                                    break;
                                default:
                                    message = "The " + params[0] + " isn't valid";
                                    break;
                            }
                            if (typeof(this.validationmsg) !== "undefined") {
                                let message_override = null;
                                if (typeof(this.validationmsg[validation_type]) !== "undefined") {
                                    message_override = this.validationmsg[validation_type];
                                }
                                if (validation_name !== null && typeof(this.validationmsg[validation_name]) !== "undefined") {
                                    message_override = this.validationmsg[validation_name];
                                }
                                if (message_override !== null && message_override != "") {
                                    params.forEach((param, idx) => {
                                        let regex = new RegExp("{[\\s]{0,}" + idx + "[\\s]{0,}}", "g");
                                        let matches = message_override.match(regex);
                                        if (matches != null && matches.length > 0) {
                                            matches.forEach((match) => {
                                                message_override = message_override.replace(match, param);
                                            });
                                        }
                                    });
                                    message = message_override;
                                }
                            }
                            return message;
                        }
                    }
                }
            }
            return "";
        },
        suggestions_f() {
            if (this.suggestions.length > 0 && this.suggestion_value != "") {
                return this.suggestions.filter((suggestion) => {
                    let pattern = new RegExp("(" + this.suggestion_value.trim().split("").join(" ?") + ")", "i");
                    suggestion.highlighted = suggestion.text.replace(pattern, "<strong>$1</strong>");
                    return (suggestion.text.replace(/[\s]/g, "").toLowerCase().trim().indexOf(this.suggestion_value.replace(/[\s]/g, "").toLowerCase().trim()) !== -1);
                });
            }
            return [];
        },
        suggestions_available() {
            return this.suggestions_open == true &&
                this.suggestions_f.length > 0 &&
                this.suggestion_value != "" &&
                this.suggestions_pending == false;
        },
        suggestions_unavailable() {
            let suggestions_unavailable = this.suggestions_open == true &&
            this.suggestions_f.length == 0 &&
            this.suggestion_value != "" &&
            this.suggestions_pending == false;
            if (suggestions_unavailable) {
                this.$emit("nosuggestions", this.suggestion_value);
                if (typeof(this.$attrs.onNosuggestions) === "undefined") {
                    return true;
                }
            } else {
                if (this.suggestions_open == true || this.suggestion_value == "") {
                    this.$emit("nosuggestions", null);
                }
            }
            return false;
        },
        suggestions_fetching() {
            return this.suggestions_open == true &&
                this.suggestion_value != "" &&
                this.suggestions_pending == true;
        }
    },
    template: `
    <div class="field__wrapper" :class="[isNumberWithButtons() ? 'field__wrapper--number' : '']" v-if="isValidType()">
        <div v-if="!hintIsEmpty()" class="field__hint"><span class="icon"></span><span class="text" v-html="hint"></span></div>
        <label v-if="!labelIsEmpty() && (isInput() || isPassword() || isFile() || isTel() || isNumberWithButtons() || isSelect() || isSelectColors() || isSelectSwatches() || isTextarea() || isAutocomplete())" :class="{ is_required: isRequired() }" class="field__title" v-html="label" :for="uid"></label>
        <input v-if="isInput()" :aria-label="label" :name="uid_name" :type="type" :placeholder="placeholder" :pattern="pattern" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid" />
        <label class="field__inner" v-if="isPassword()">
            <div class="password--switch" @click="password_shown = !password_shown">{{ password_shown ? "Hide" : "Show" }}</div>
            <input :aria-label="label" :name="uid_name" :type="password_shown ? 'text' : 'password'" :placeholder="placeholder" :autocomplete="autocomplete" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid" />
        </label>
        <label class="field file" v-if="isFile()" tabindex="0" @keydown="triggerInnerField">
            {{ file_name ? file_name : "No file chosen" }}
            <input :aria-label="label" :name="uid_name" type="file" :placeholder="placeholder" :disabled="disabled" :pattern="pattern" @change="handleFileUpload($event)" @blur="triggerValidation" :id="uid" tabindex="-1" />
            <div class="file--progress" :style="{ width: file_progress + '%' }"></div>
        </label>
        <img class="preview file" v-if="isFile() && file_path" :src="file_path" />
        <div class="field__inner" v-if="isNumberWithButtons()">
            <div class="minus" @click="minusNumber()"></div>
            <input v-if="isNumberWithButtons()" :aria-label="label" :name="uid_name" type="text" :placeholder="placeholder" pattern="\\d*" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid" />
            <div class="plus" @click="plusNumber()"></div>
        </div>
        <input v-if="isNumber()" :aria-label="label" :name="uid_name" type="text" :placeholder="placeholder" pattern="\\d*" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid" />
        <input v-if="isNumberMaxLength()" :aria-label="label" :name="uid_name" type="text" :placeholder="placeholder" pattern="\\d*" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :maxlength="maxlength" :id="uid" />
        <input v-if="isTel()" :aria-label="label" :name="uid_name" type="tel" :placeholder="placeholder" pattern="\\d*" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid" />
        <label v-if="isRadio()" class="radio" tabindex="0" @keydown="triggerInnerField"><input :aria-label="label" :name="uid_name" type="radio" :disabled="disabled" :value="value" :checked="model == value" @click="updateValue($event.target.value)" @blur="triggerValidation" tabindex="-1" /><span class="text" v-html="valuelabel" v-if="valuelabel"></span></label>
        <label v-if="isCheckbox()" class="checkbox" tabindex="0" @keydown="triggerInnerField"><input :aria-label="label" :name="uid_name" type="checkbox" :disabled="disabled" :checked="model == true" @change="updateValue($event.target.checked)" @blur="triggerValidation" tabindex="-1" /><span class="text" v-html="valuelabel" v-if="valuelabel"></span></label>
        <label v-if="isSwitch()" class="switch" tabindex="0" @keydown="triggerInnerField"><input :aria-label="label" :name="uid_name" type="checkbox" :disabled="disabled" :checked="model == true" @change="updateValue($event.target.checked)" @blur="triggerValidation" tabindex="-1" /><span class="text" v-html="valuelabel" v-if="valuelabel"></span></label>
        <select v-if="isSelect()" :aria-label="label" :name="uid_name" :autocomplete="autocomplete" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @change="updateValue($event.target.value)" @blur="triggerValidation" :id="uid">
        <option value="" selected disabled>Select {{ selectLabelIsEmpty() ? label : select_label }}</option>
        <template v-for="option in options">
            <template v-if="isOptionGroup(option)">
                <optgroup :label="option.group">
                    <option v-for="option in option.options" :value="option.value">{{ option.text }}</option>
                </optgroup>
            </template>
            <template v-if="!isOptionGroup(option)">
                <template v-if="hasSubOptions(option)">
                    <option v-for="option in option.options" :value="option.value">{{ option.text }}</option>
                </template>
                <template v-if="!hasSubOptions(option)">
                    <option :value="option.value">{{ option.text }}</option>
                </template>
            </template>
        </template>
        </select>
        <div v-if="isSelectColors()" class="field__collection">
        <input v-for="option in options" :aria-label="label" :name="uid_name" type="radio" :disabled="isOptionDisabled(option)" :value="option.value" :checked="model == option.value" @click="updateValue($event.target.value)" @blur="triggerValidation" class="radio color" :style="{ 'background-color': getSwatchColor(option), 'border-color': getSwatchColor(option) }" />
        </div>
        <div v-if="isSelectSwatches()" class="field__collection">
        <input v-for="option in options" :aria-label="label" :name="uid_name" type="radio" :disabled="isOptionDisabled(option)" :value="option.value" :checked="model == option.value" @click="updateValue($event.target.value)" @blur="triggerValidation" class="radio swatch" :class="{ 'swatch:lg': hasLongSwatchLabel(option) }" :data-label="option.label" />
        </div>
        <textarea v-if="isTextarea()" :aria-label="label" :name="uid_name" :placeholder="placeholder" :readonly="readonly" :autofocus="autofocus" :disabled="disabled" :value="model" @input="updateValue($event.target.value)" @blur="triggerValidation" :id="uid"></textarea>
        <div v-if="isAutocomplete()" class="field__autocomplete">
        <input :name="uid_name" type="text" :placeholder="placeholder" autocomplete="off" :disabled="disabled" :value="suggestion_value" @input="updateValueTmp" @keydown.down="onArrowDown" @keydown.up="onArrowUp" @keydown.enter="selectSuggestion" @blur="selectSuggestion" :id="uid" />
        <div v-if="suggestions_available" class="field__suggestions">
            <ul>
                <li v-for="(suggestion, idx) in suggestions_f" @click.stop="selectValue(suggestion)" :class="{ 'js__hovered': idx == suggestion_idx }" @mouseover="changeSuggestionIdx(idx)">
                    <slot name="suggestion" :suggestion="suggestion"><span v-html="suggestion.highlighted"></span></slot>
                </li>
            </ul>
        </div>
        <div v-if="suggestions_unavailable" class="field__suggestions"><span>No results</span></div>
        <div v-if="suggestions_fetching" class="field__suggestions"><span>Loading...</span></div>
        </div>
        <span v-if="errorIsActive()" class="field__message--error" v-html="error"></span>
    </div>
    `,
    mounted() {
        if (this.isAutocomplete()) {
            let input = this.$el.querySelector("input");
            input.addEventListener("click", (e) => {
                e.stopPropagation();
            });
            input.addEventListener("focus", () => {
                if (this.suggestions_f.length > 0 && this.suggestion_value != "") {
                    this.suggestions_open = true;
                }
            });
            input.addEventListener("blur", () => {
                if (this.suggestion_value == "") {
                    this.clearValue();
                }
            });
            document.addEventListener("click", () => {
                this.suggestions_open = false;
            });
        }
    }
};

export default _input;