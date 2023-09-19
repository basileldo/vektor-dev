@extends('layouts.default')
@section('title', 'Shop')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        {{-- <div class="container:xl mb-4 mb-8:3 -mt-6:3">
            <ul class="breadcrumbs">
                <li><a href="{{ route('base') }}">Back to Homepage</a></li>
            </ul>
        </div> --}}
        <div class="container:lg">
            <h1 class="text-gradient">Shop</h1>
            <c-message :content="success_message" class="message--positive message--top" :trigger="is_success_message_shown" :autohide="true"></c-message>
            <c-message :content="error_message" class="message--negative message--top" :trigger="is_error_message_shown" :autohide="true"></c-message>
            <c-products>
                <template v-slot:default="productsScope">
                    <div class="spinner__wrapper" :class="{ is_loading: productsScope.is_loading == true }">
                        <div class="spinner"></div>
                    </div>
                    <section class="products" v-if="productsScope.products_fetched == true && productsScope.products.length > 0">
                        <c-product :config="product" :key="product.id" v-for="(product, product_idx) in productsScope.products" @update:cart="updateCart"
                        @message:hide="hideMessage"
                        @message:success="successMessage"
                        @message:error="errorMessage" class="p-3 p-5:3 text-center flex flex-col">
                            <template v-slot:default="productScope">
                                <a :href="'{{ url('product') }}/' + product.slug">
                                    <img :src="productScope.image" class="mb-4" />
                                </a>
                                <div class="mt-auto">
                                    <span class="block font-bold mb-3" v-html="productScope.config.name_label"></span>
                                    <span v-if="!productScope.hide_pricing" class="block mb-4" v-html="productScope.formatPrice(productScope.display_price)"></span>
                                    <a :href="'{{ url('product') }}/' + product.slug" class="btn block mb-2 text-xs uppercase py-3 font-bold bg-background border-gray-300 hover:bg-primary hover:border-primary hover:text-primary_contrasting">View Product</a>
                                    <a @click.stop.prevent="productScope.openModal" class="text-sm text-gray-400">Quick View</a>
                                </div>
                                <c-modal :trigger="productScope.is_modal_shown" class="from_bottom" @open="productScope.openModal" @close="productScope.closeModal">
                                    <div class="spinner__wrapper spinner--absolute" :class="{ is_loading: productScope.is_loading == true }">
                                        <div class="spinner"></div>
                                    </div>
                                    <div class="product text-left mb-6 grid:2 grid-cols-3:2 gap-x-5:2">
                                        <div>
                                            <a :href="'{{ url('product') }}/' + product.slug">
                                                <img class="w-1/4:1e mx-auto:1e mb-4:1e" :src="productScope.image" />
                                            </a>
                                        </div>
                                        <div class="col-span-2:2">
                                            <div class="h2 font-bold" v-html="productScope.config.name_label"></div>
                                            <template v-if="productScope.config.products.length > 0">
                                                <hr />
                                                <div class="variations py-4">
                                                    <c-input v-for="variation in productScope.variations" :name="variation.name" :label="variation.label" v-model="productScope.options[variation.name]" :type="productScope.variationInputType(variation.name)" :options="variation.variations"></c-input>
                                                </div>
                                                <hr />
                                            </template>
                                            <div class="mt-4 flex justify-between items-center">
                                                <c-input v-if="productScope.config.qty_per_order != 1 && (productScope.config.qty_per_order_grouping != null && productScope.config.qty_per_order_grouping != 'id' && productScope.config.qty_per_order_grouping != 'sku')" name="qty" label="Qty" v-model="productScope.qty" @update:model-value="productScope.updateQty" type="number:buttons" class="sm"></c-input>
                                                <span v-if="!productScope.hide_pricing" style="font-size: 1.6rem;" class="price font-bold">@{{ productScope.formatPrice(productScope.display_price) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="btn block bg-primary border-primary text-primary_contrasting" @click.stop="productScope.addCartItem" :disabled="productScope.is_disabled">Add to Cart</a>
                                </c-modal>
                            </template>
                        </c-product>
                    </section>
                    <c-message v-if="productsScope.products_fetched == true && productsScope.products.length == 0" content="There are no products yet" :trigger="true"></c-message>
                    <c-pagination v-show="productsScope.products_fetched == true && productsScope.products.length > 0 && productsScope.paginate === true" :properties="productsScope.pagination" :per_pages="productsScope.per_pages" @change-pagination="productsScope.getProducts"></c-pagination>
                </template>
            </c-products>
        </div>
    </div>
@endsection

@section('config')
'checkout.products.index': {!! $products !!},
@endsection