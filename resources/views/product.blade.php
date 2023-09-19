@extends('layouts.default')
@section('title', $product['name_label'])

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <c-product :config="{{ $product }}" @update:cart="updateCart">
            <template v-slot:default="productScope">
                <div class="spinner__wrapper" :class="{ is_loading: productScope.is_loading == true }">
                    <div class="spinner"></div>
                </div>
                <c-message :content="productScope.success_message" class="message--positive message--top" :trigger="productScope.is_success_message_shown" :autohide="true"></c-message>
                <c-message :content="productScope.error_message" class="message--negative message--top" :trigger="productScope.is_error_message_shown" :autohide="true"></c-message>
                <div class="container:xl mb-4 mb-8:3 -mt-6:3">
                    <ul class="breadcrumbs" v-show="productScope.is_ready">
                        <li><a href="{{ route('checkout.product.index') }}">Back to Shop</a></li>
                        <li v-html="productScope.config.name_label"></li>
                    </ul>
                </div>
                <div class="container:xl">
                    <div class="grid grid-cols-2:3 gap-4 gap-8:2 gap-12:3 product" v-show="productScope.is_ready">
                        <div class="max-w-full max-w-xs:1t2e mx-auto">
                            <swiper
                                {{-- v-if="productScope.images.length > 1" --}}
                                :modules="swiper_modules"
                                :slides-per-view="1"
                                :space-between="16"
                                :pagination="{ clickable: true, type: 'bullets' }"
                                @swiper="onSwiper"
                                :thumbs="{ swiper: thumbs_swiper }"
                            >
                                <swiper-slide v-for="image in productScope.images">
                                    <img width="800" height="800" :src="image" :alt="productScope.config.name_label" />
                                </swiper-slide>
                            </swiper>
                            <swiper
                                class="mt-4"
                                :modules="swiper_modules"
                                @swiper="onThumbsSwiper"
                                slides-per-view="auto"
                                :center-insufficient-slides="true"
                                space-between="10"
                            >
                                <swiper-slide v-for="image in productScope.images" v-if="productScope.images.length > 1">
                                    <img width="100" height="100" :src="image" :alt="productScope.config.name_label" />
                                </swiper-slide>
                            </swiper>
                            {{-- <img v-else width="800" height="800" :src="image" :alt="productScope.config.name_label" v-for="image in productScope.images" /> --}}
                        </div>
                        <div>
                            <h1 class="text-gradient text-center:1t2e" v-html="productScope.config.name_label"></h1>
                            <div class="variations variations-center:1t2e py-4 max-w-xs:1t2e mx-auto">
                                <c-input v-for="variation in productScope.variations" :name="variation.name" :label="variation.label" v-model="productScope.options[variation.name]" :type="productScope.variationInputType(variation.name)" :options="variation.variations"></c-input>
                            </div>
                            <span v-if="!productScope.hide_pricing" style="font-size: 2rem;" class="price font-bold block mt-1 mb-4 text-center:1t2e">@{{ productScope.formatPrice(productScope.display_price) }}</span>
                            <div class="flex items-end mt-4 mb-8">
                                <c-input v-if="productScope.config.qty_per_order != 1 && (productScope.config.qty_per_order_grouping != 'id' && productScope.config.qty_per_order_grouping != 'sku')" name="qty" label="Qty" v-model="productScope.qty" @update:model-value="productScope.updateQty" type="number:buttons" class="mb-0 mr-8"></c-input>
                                <a class="btn block w-full bg-primary border-primary text-primary_contrasting" @click.stop="productScope.addCartItem" :disabled="productScope.is_disabled">Add to Cart</a>
                            </div>

                            <c-tabs class="mb-4" :active_tab="active_tab" @active-tab="setActiveTab" v-if="productScope.config.description || productScope.config.size_guide || productScope.config.shipping">
                                <template v-slot:default="tabs">
                                    <c-tab name="1" label="Details">
                                        <div class="text-sm my-3" v-html="productScope.config.description"></div>
                                    </c-tab>
                                    <c-tab name="2" label="Size Guide">
                                        <div class="text-sm mt-3" v-if="productScope.config.size_guide">
                                            <table>
                                                <tbody>
                                                    <tr v-for="row in productScope.config.size_guide">
                                                        <td v-for="cell in row">@{{cell}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-sm my-3" v-if="productScope.config.size_guide_note">
                                            <span class="block font-bold text-rose-600">Please note</span>
                                            <p>@{{ productScope.config.size_guide_note }}</p>
                                        </div>
                                    </c-tab>
                                    <c-tab name="3" label="Shipping">
                                        <div class="text-sm my-3" v-html="productScope.config.shipping"></div>
                                    </c-tab>
                                </template>
                            </c-tabs>
                        </div>
                    </div>
                </div>
            </template>
        </c-product>
    </div>
@endsection