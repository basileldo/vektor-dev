<footer class="document__footer" role="contentinfo" aria-label="Main Footer">
    <section class="bg-primary text-primary_contrasting">
        <div class="container:xl">
            <div class="footer__content__wrapper">
                <div class="footer__content">{{ config('app.company.name') }} &copy; @{{ year }}</div>
                <div class="footer__content">
                    <div class="metadata__collection">
                        @if (config('app.terms'))
                            <div class="metadata__item"><a href="{{ config('app.terms') }}" target="_blank" rel="noopener">Terms & Conditions</a></div>
                        @else
                            <div class="metadata__item"><a href="{{ route('terms') }}">Terms & Conditions</a></div>
                        @endif
                        @if (config('app.policy'))
                            <div class="metadata__item"><a href="{{ config('app.policy') }}" target="_blank" rel="noopener">Privacy Policy</a></div>
                        @else
                            <div class="metadata__item"><a href="{{ route('policy') }}">Privacy Policy</a></div>
                        @endif
                        <div class="metadata__item">website by <a href="https://vektor.co.uk" target="_blank" rel="noopener">vektor</a></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</footer>
@include('partials.cookies')
@if (config('checkout.only') === false)
    <c-search :trigger="search_trigger" @open="search_trigger = true" @close="search_trigger = false">
        <template v-slot:default="search">
            <c-input @select="search.select" name="s" v-model="search.s" :suggestions="countries" :suggestions_model="false" type="autocomplete" placeholder="Enter search terms..."></c-input><input @click.prevent="search.search" class="btn" type="submit" value="" />
        </template>
    </c-search>
@endif