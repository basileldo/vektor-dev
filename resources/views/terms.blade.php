@extends('layouts.default')
@section('title', 'Terms & Conditions')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:md">
            <article class="article article--overflow container:xl:1t2e edgeless:1t2e">
                <h1 class="text-gradient">Terms & Conditions</h1>
                <p>Welcome to the terms and conditions of the company. These terms and conditions govern the utilization of the services and website provided by the company. By accessing or using the company's services and website, you hereby acknowledge and agree to abide by these terms and conditions.</p>
                <ul class="pl-6 list-outside list-decimal">
                    <li><strong>Acceptance of Terms:</strong> By accessing or using the services and website of the company, you explicitly acknowledge that you have carefully read, comprehended, and willingly consent to be bound by these terms and conditions.</li>
                    <li><strong>Use of Services:</strong> The company offers diverse services through its website, subject to your compliance with the stipulations set forth in these terms and conditions. You undertake to employ the services solely for lawful purposes and in strict accordance with applicable laws and regulations.</li>
                    <li><strong>Intellectual Property:</strong> All intellectual property rights pertaining to the company's services and website, including trademarks, copyrights, and patents, are the exclusive property of the company. You are granted a restricted, non-exclusive, non-transferable license to utilize the services and access the website solely for personal and non-commercial purposes.</li>
                    <li><strong>User Accounts:</strong> To access specific features or services, the creation of a user account may be necessary. You assume responsibility for maintaining the confidentiality of your account credentials and for any activities carried out under your account.</li>
                    <li><strong>Privacy:</strong> The company values your privacy and handles your personal information in compliance with its Privacy Policy. By utilizing the services and website, you expressly consent to the collection, use, and disclosure of your personal information as outlined in the <a href="{{ route('policy') }}">Privacy Policy</a>.</li>
                    <li><strong>Prohibited Activities:</strong> You agree not to engage in any activities that may disrupt or impede the functionality of the services or website provided by the company, including, but not limited to, unauthorized access, distribution of malware, or any unlawful activities.</li>
                    <li><strong>Disclaimer of Warranties:</strong> The company makes no warranties or representations concerning the accuracy, reliability, or completeness of the services or website. The utilization of the services and website is entirely at your own risk.</li>
                    <li><strong>Limitation of Liability:</strong> The company shall not be held liable for any direct, indirect, incidental, consequential, or punitive damages arising from or in connection with the use of the services or website.</li>
                    <li><strong>Modifications:</strong> The company reserves the right to modify or terminate the services or website, or to make amendments to these terms and conditions, at any time without prior notice.</li>
                    <li><strong>Governing Law:</strong> These terms and conditions shall be governed by and construed in accordance with the laws of the jurisdiction in which the company is located.</li>
                </ul>
                <p>If you have any inquiries or concerns regarding these terms and conditions, please do not hesitate to contact us at <a href="mailto:{{ config('app.company.email') }}">{{ config('app.company.email') }}</a>.</p>
            </article>
        </div>
    </div>
@endsection
