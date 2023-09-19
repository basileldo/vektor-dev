@extends('layouts.default')
@section('title', 'Reset password')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:sm">
            <div class="bg-background shadow-box p-8 p-10:3">
                <h1 class="text-gradient">Reset password</h1>
                <p>Enter the email address associated with your account and we'll send an email with instructions to reset your password.</p>
                <c-form :name="forms.password_email.ref" :ref="forms.password_email.ref" method="post" :action="forms.password_email.action" :field_values="forms.password_email.field_values" :field_storage="forms.password_email.field_storage" :field_validation_rules="forms.password_email.validation_rules" :field_validation_messages="forms.password_email.validation_messages">
                    <template v-slot:fields="form">
                        <c-input name="email" type="email" label="Email address" placeholder="Enter email" v-model="form.field_values.email" :validationrule="form.validation_rules.email" :validationmsg="form.validation_messages.email" autocomplete="email"></c-input>
                        <c-message :content="form.response.error_message" class="message--negative" :trigger="form.response.error"></c-message>
                        <c-message :content="form.response.success_message" class="message--positive" :trigger="form.response.success"></c-message>

                        <button type="submit" class="btn bg-secondary border-secondary text-secondary_contrasting" :class="{ is_disabled: form.validation_rules.$invalid == true }">Send instructions</button>
                    </template>
                </c-form>
            </div>
        </div>
    </div>
@endsection