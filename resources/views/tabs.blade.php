@extends('layouts.default')
@section('title', 'Tabs')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:xl">
            <div class="sidebar__wrapper sidebar__wrapper--sticky gap-12:3">
                <c-contents :items="[
                    { name: 'area_1', label: 'Area 1'},
                    { name: 'area_2', label: 'Area 2'}
                ]"></c-contents>
                <section>
                    <h1 class="text-gradient">Tabs</h1>
                    <c-tabs class="mb-10" :active_tab="active_tab" @active-tab="setActiveTab">
                        <template v-slot:default="tabs">
                            <c-tab name="1" label="Home">
                                <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.</p><p>Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            </c-tab>
                            <c-tab name="2" label="About">
                                <p>Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                                <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.</p>
                            </c-tab>
                            <c-tab name="3" label="Contact">
                                <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.</p><p>Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            </c-tab>
                        </template>
                    </c-tabs>
                    <article class="media media__flex:2">
                        <figure class="media__image">
                            <div class="media__image__content">
                                <img src="/assets/img/media__thumbnail.jpg" alt="Avatar" width="256" height="256" />
                            </div>
                        </figure>
                        <div class="media__content">
                            <p><strong>David Dawes</strong> <small>@mynameisdawes</small>
                                <br />But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            <footer>
                                <article class="media media__flex:2">
                                    <figure class="media__image">
                                        <div class="media__image__content">
                                            <img src="/assets/img/media__thumbnail.jpg" alt="Avatar" width="256" height="256" />
                                        </div>
                                    </figure>
                                    <div class="media__content">
                                        <p><strong>David Dawes</strong> <small>@mynameisdawes</small>
                                            <br />But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                                    </div>
                                </article>
                            </footer>
                        </div>
                    </article>
                    <article class="media media__flex:2">
                        <figure class="media__image">
                            <div class="media__image__content">
                                <img src="/assets/img/media__thumbnail.jpg" alt="Avatar" width="256" height="256" />
                            </div>
                        </figure>
                        <div class="media__content">
                            <p><strong>David Dawes</strong> <small>@mynameisdawes</small>
                                <br />But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            <footer>
                                <article class="media media__flex:2">
                                    <figure class="media__image">
                                        <div class="media__image__content">
                                            <img src="/assets/img/media__thumbnail.jpg" alt="Avatar" width="256" height="256" />
                                        </div>
                                    </figure>
                                    <div class="media__content">
                                        <p><strong>David Dawes</strong> <small>@mynameisdawes</small>
                                            <br />But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                                    </div>
                                </article>
                            </footer>
                        </div>
                    </article>
                </section>
            </div>
        </div>
    </div>
@endsection
