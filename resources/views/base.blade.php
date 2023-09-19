@extends('layouts.default')
{{-- @section('title', 'Homepage') --}}

@section('content')
    <div class="hero py-40 bg-image text-center" style="background-image: url('/assets/img/backgrounds/background__her.jpg');">
        <div class="container:xl">
            <h1>Independent Family Business</h1>
            <h3>Lorem ipsum dolor sit amet</h3>
            <div class="btn__collection"><a href="#" class="btn bg-secondary border-secondary text-secondary_contrasting">Open Modal</a></div>
        </div>
    </div>
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:xl">
            <div mode="right" class="sidebar__wrapper sidebar__wrapper--sticky gap-12:3">
                <c-contents :items="[ { name: 'area_1', label: 'Area 1'}, { name: 'area_2', label: 'Area 2'} ]"></c-contents>
                <section>
                    <h2>Heading</h2>
                    <div class="grid grid-cols-3:4 gap-6 mb-4 mb-8:2">
                        <div class="shadow-box p-8 border-0 bg-secondary text-secondary_contrasting" style="background-image: url('/assets/img/asset--drinks.jpg');background-repeat: no-repeat;">
                            <h3 class="h4">Card heading</h3>
                            <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, <a href="#" @click.prevent="">because it is pleasure</a>, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            <a href="#" @click.prevent="" class="btn bg-secondary border-secondary text-secondary_contrasting">Click here</a>
                        </div>
                        <div class="shadow-box p-8 border-0 bg-secondary text-secondary_contrasting" style="background-image: url('/assets/img/asset--food.jpg');">
                            <h3 class="h4">Card heading</h3>
                            <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, <a href="#" @click.prevent="">because it is pleasure</a>, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            <a href="#" @click.prevent="" class="btn bg-white border-white text-black">Click here</a>
                        </div>
                        <div class="shadow-box p-8 border-0 bg-secondary text-secondary_contrasting" style="background-image: url('/assets/img/asset--events.jpg');">
                            <h3 class="h4">Card heading</h3>
                            <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, <a href="#" @click.prevent="">because it is pleasure</a>, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
                            <a href="#" @click.prevent="" class="btn bg-white border-white text-black">Click here</a>
                        </div>
                    </div>
                    <div class="section__scroll" id="area_1"></div>
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
                    <div class="section__scroll" id="area_2"></div>
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
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:xl">
            <table class="table--responsive:1t2e">
                <thead>
                    <tr>
                        <th>Header</th>
                        <th>Header</th>
                        <th>Header</th>
                        <th>Header</th>
                        <th>Header</th>
                        <th>Header</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                    <tr>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                        <td data-header="Header">Table Content</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <c-modal :trigger="modal_trigger" @open="modal_trigger = true" @close="modal_trigger = false">
        <h3>Modal heading</h3>
        <p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.</p>
        <p>Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure.</p>
    </c-modal>
    <c-confirmation :trigger="confirmation_trigger" @open="confirmation_trigger = true" @close="confirmation_trigger = false" confirm="Yes" cancel="No">
        <h3>Crucial choices</h3>
        <p>Are yo sure?!?!?</p>
    </c-confirmation>
@endsection
