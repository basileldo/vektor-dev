@extends('layouts.default')
@section('title', 'Register')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:sm">
            <div class="bg-background shadow-box p-8 p-10:3">
                <h1 class="text-gradient">Register</h1>
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
                <c-form :name="forms.register.ref" :ref="forms.register.ref" method="post" :action="forms.register.action" :field_values="forms.register.field_values" :field_storage="forms.register.field_storage" :field_validation_rules="forms.register.validation_rules" :field_validation_messages="forms.register.validation_messages">
                    <template v-slot:fields="form">
                        <c-input name="first_name" v-model="form.field_values.first_name" :validationrule="form.validation_rules.first_name" :validationmsg="form.validation_messages.first_name" label="First Name" autocomplete="given-name"></c-input>
                        <c-input name="last_name" v-model="form.field_values.last_name" :validationrule="form.validation_rules.last_name" :validationmsg="form.validation_messages.last_name" label="Last Name" autocomplete="family-name"></c-input>
                        <c-input name="email" type="email" label="Email address" placeholder="Enter email" v-model="form.field_values.email" :validationrule="form.validation_rules.email" :validationmsg="form.validation_messages.email" autocomplete="email"></c-input>
                        <c-input name="password" type="password" label="Password" placeholder="Enter password" v-model="form.field_values.password" :validationrule="form.validation_rules.password" :validationmsg="form.validation_messages.password" autocomplete="new-password"></c-input>
                        <c-input name="password_confirmation" type="password" label="Confirmation Password" placeholder="Confirmation password" v-model="form.field_values.password_confirmation" :validationrule="form.validation_rules.password_confirmation" :validationmsg="form.validation_messages.password_confirmation" autocomplete="new-password"></c-input>

                        <c-message :content="form.response.error_message" class="message--negative" :trigger="form.response.error"></c-message>
                        <c-message :content="form.response.success_message" class="message--positive" :trigger="form.response.success"></c-message>

                        <button type="submit" class="btn bg-secondary border-secondary text-secondary_contrasting" :class="{ is_disabled: form.validation_rules.$invalid == true }">Register</button>
                    </template>
                </c-form>
            </div>
        </div>
    </div>
@endsection