@extends('layouts.default')
@section('title', 'Login')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:sm">
            <div class="bg-background shadow-box p-8 p-10:3 relative mb-2">
                <h1 class="text-gradient">Login</h1>
                <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
                <c-form :name="forms.login.ref" :ref="forms.login.ref" method="post" :action="forms.login.action" :field_values="forms.login.field_values" :field_storage="forms.login.field_storage" :field_validation_rules="forms.login.validation_rules" :field_validation_messages="forms.login.validation_messages">
                    <template v-slot:fields="form">
                        <c-input name="email" type="email" label="Email address" placeholder="Enter email" v-model="form.field_values.email" :validationrule="form.validation_rules.email" :validationmsg="form.validation_messages.email" autocomplete="email"></c-input>
                        <c-input name="password" type="password" label="Password" placeholder="Enter password" v-model="form.field_values.password" :validationrule="form.validation_rules.password" :validationmsg="form.validation_messages.password"></c-input>
                        <c-input name="remember_me" type="checkbox" valuelabel="Remember Me" v-model="form.field_values.remember_me" :validationrule="form.validation_rules.remember_me" :validationmsg="form.validation_messages.remember_me"></c-input>
                        <c-message :content="form.response.error_message" class="message--negative" :trigger="form.response.error"></c-message>
                        <c-message :content="form.response.success_message" class="message--positive" :trigger="form.response.success"></c-message>

                        <button type="submit" class="btn bg-secondary border-secondary text-secondary_contrasting" :class="{ is_disabled: form.validation_rules.$invalid == true }">Login</button>
                    </template>
                </c-form>
                <div class="mt-14 absolute text-center w-full left-0 text-sm">
                    <a class="forgot_password" href="{{ route('password.request') }}">Forgot Your Password?</a>
                </div>
            </div>
        </div>
    </div>
@endsection