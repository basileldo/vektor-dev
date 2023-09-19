<header class="document__header document__header--fixed document__header--absolute2" role="banner" aria-label="Document Header">
    <div class="header__bar">
        <div class="container:xl">
            <div class="header__content__wrapper">
                <div class="header__content">
                    <!-- <div class="document__logo">
                        <a href="{{ route('base') }}" aria-label="Homepage Link">
                            <img src="{{ route('logo') }}" alt="{{ config('app.company.name') }}" />
                        </a>
                    </div> -->
                </div>
                <div class="header__content">
                    <c-slider :trigger="slider_trigger" :mode="navigation_mode" v-if="navigation">
                        <c-navigation ref="navigation" :mode="navigation_mode" @close="closeNavigation" :items="navigation_items"></c-navigation>
                    </c-slider>
                    <div class="document__navigation__action">
                        @if (config('checkout.enabled') === true)
                            <a href="{{ route('checkout.cart.index') }}" class="document__navigation__cart" aria-label="Cart Link">
                                <span class="document__navigation__cart__count" v-html="count" v-show="count && count > 0"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19.9" viewBox="0 0 20 19.9"><path class="fill-current" d="M16.4,3.9H15V2.2C15,1,14,0,12.8,0H7.2C6,0,5,1,5,2.2v1.6H3.6C1.6,3.9,0,5.5,0,7.5v8.9c0,2,1.6,3.6,3.6,3.6h12.9c2,0,3.6-1.6,3.6-3.6V7.5C20,5.5,18.4,3.9,16.4,3.9z M7.2,2.2h5.5v1.6H7.2V2.2z M17.6,16.3c0,0.7-0.5,1.2-1.2,1.2H3.6c-0.7,0-1.2-0.5-1.2-1.2V7.5c0-0.7,0.5-1.2,1.2-1.2h12.9c0.7,0,1.2,0.5,1.2,1.2V16.3z"/></svg>
                            </a>
                        @endif
                        <div class="document__navigation__account" :class="{'document__navigation__account--logged_in': is_logged_in == true}">
                            <component class="document__navigation__account__icon" :is="is_logged_in == true ? 'div' : 'a'" :href="is_logged_in == true ? null : '{{ route('dashboard') }}'" :aria-label="is_logged_in == true ? 'Account Area' : 'Account Link'">
                                <div class="document__navigation__account__icon--tick" v-if="is_logged_in == true">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="60px" height="60px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve"><polygon class="fill-current" points="51.483,5.936 20.39,37.029 8.517,25.157 0,33.674 20.39,54.064 60,14.453 "/></svg>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30px" height="30px" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve"><path class="fill-current" d="M21.6,17.2c2-1.8,3.3-4.4,3.3-7.3C24.9,4.4,20.5,0,15,0S5.1,4.4,5.1,9.9c0,2.9,1.3,5.5,3.3,7.3c-3.1,0.8-5.6,2.2-7,4 C3.7,26.4,8.9,30,15,30s11.3-3.6,13.7-8.8C27.2,19.4,24.7,18,21.6,17.2z"/></svg>
                            </component>
                            <ul v-if="is_logged_in == true">
                                @include('partials.dashboard_navigation')
                            </ul>
                        </div>
                        @if (config('checkout.only') === false)
                            <div class="document__navigation__search" @click.stop="search_trigger = !search_trigger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19.89" viewBox="0 0 20 19.89"><path class="fill-current" d="M20,18.19l-6.34-6.34A7.54,7.54,0,1,0,12,13.56l6.33,6.33ZM3.9,11.13a5.11,5.11,0,1,1,7.23,0A5.12,5.12,0,0,1,3.9,11.13Z"></path></svg>
                            </div>
                        @endif
                        <div class="document__navigation__icon" :class="{
                            'is_open': slider_trigger,
                            'document__navigation__icon--r': navigation_mode == 'r', 'document__navigation__icon--sm': navigation_mode == 'sm', 'document__navigation__icon--lg': navigation_mode == 'lg'
                        }" @click.stop="toggleNavigation" v-if="navigation">
                            <div class="navigation_icon">
                                <span class="navigation_icon__el navigation_icon__el--top"></span>
                                <span class="navigation_icon__el navigation_icon__el--middle"></span>
                                <span class="navigation_icon__el navigation_icon__el--bottom"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>