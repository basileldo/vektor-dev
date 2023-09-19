@extends('layouts.default')
@section('title', 'Map')

@section('content')
    <div class="pb-4 pb-8:2 pb-12:3">
        <div class="map_field__wrapper">
            <div class="field_btn__wrapper">
                <c-input name="map_address_search" v-model="map_address_search_tmp" @keyup.enter="mapAdressSearch" placeholder="Type city/postcode"></c-input>
                <a @click="mapAdressSearch" class="btn bg-primary border-primary text-primary_contrasting">Search</a>
            </div>
            <c-map :address_search="map_address_search" api_key="{{ config('map.api_key') }}" endpoint="{{ config('map.endpoint') }}"></c-map>
        </div>
    </div>
@endsection