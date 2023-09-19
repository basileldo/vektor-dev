import loadGoogleMapsApi from "load-google-maps-api";

import _config from "../utilities/config.js";
_config.init();

import { _api, _storage } from "../utilities/api.js";

let _map = {
    name: "c-map",
    props: {
        api_key: {
            default: null
        },
        endpoint: {
            default: null
        },
        address_search: {
            default: null
        },
        markers_displayed_qty: {
            default: 12
        }
    },
    data() {
        return {
            intersection_observer: null,
            needs_to_scroll: false,
            googleMaps: null,
            map: null,
            map_bounds: null,
            map_geocoder: null,
            map_infowindow: null,
            markers: [],
            markers_displayed: [],
            latitude: null,
            longitude: null,
        };
    },
    watch: {
        address_search: {
            handler(new_val, old_val) {
                if (
                    new_val != old_val
                    && new_val != null
                    && new_val != ""
                ) {
                    this.map_geocoder.geocode({
                        address: new_val + ", UK"
                    }, (result, status) => {
                        if (status == "OK") {
                            this.map_bounds = new this.googleMaps.LatLngBounds();
                            this.latitude = result[0].geometry.location.lat();
                            this.longitude = result[0].geometry.location.lng();
                            this.map.setCenter(new this.googleMaps.LatLng(this.latitude, this.longitude));
                            this.filterMarkers();
                        }
                    });
                }
            },
            immediate: true
        }
    },
    methods: {
        rad(x) {
            return (x * Math.PI / 180);
        },
        applyDistance(marker) {
            let earthRadius = 3961;
            if (marker.latitude != null && marker.longitude != null) {
                let mlat = parseFloat(marker.latitude);
                let mlng = parseFloat(marker.longitude);
                let dLat = this.rad(mlat - parseFloat(this.latitude));
                let dLong = this.rad(mlng - parseFloat(this.longitude));
                let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(this.rad(this.latitude)) * Math.cos(this.rad(this.latitude)) * Math.sin(dLong / 2) * Math.sin(dLong / 2);
                let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                let d = earthRadius * c;
                marker.distance = d;
            } else {
                marker.distance = null;
            }
        },
        removedMarkersDisplayed() {
            if (this.markers_displayed.length > 0) {
                for (let marker_idx = 0; marker_idx < this.markers_displayed.length; marker_idx++) {
                    this.markers_displayed[marker_idx].setMap(null);
                    this.markers_displayed[marker_idx].setVisible(false);
                }
            }
            this.markers_displayed = [];
            this.map_infowindow.marker_idx = null;
        },
        filterMarkers() {
            if (this.markers.length > 0) {
                this.markers.forEach((marker) => {
                    this.applyDistance(marker);
                });

                this.markers.sort((a, b) => {
                    if (a.distance > b.distance) { return 1; }
                    if (a.distance < b.distance) { return -1; }
                    return 0;
                });

                this.removedMarkersDisplayed();

                for (let marker_idx = 0; marker_idx < this.markers_displayed_qty && marker_idx < this.markers.length; marker_idx++) {
                    this.markers_displayed[marker_idx] = new this.googleMaps.Marker({
                        map: this.map,
                        position: new this.googleMaps.LatLng(this.markers[marker_idx].latitude, this.markers[marker_idx].longitude),
                        title: this.markers[marker_idx].name,
                        address_line_1: this.markers[marker_idx].address_line_1,
                        address_line_2: this.markers[marker_idx].address_line_2,
                        town: this.markers[marker_idx].town,
                        county: this.markers[marker_idx].county,
                        postcode: this.markers[marker_idx].postcode,
                        phone: this.markers[marker_idx].phone,
                        distance: Math.round(this.markers[marker_idx].distance * 100) / 100
                    });

                    this.map_bounds.extend(this.markers_displayed[marker_idx].position);

                    this.markers_displayed[marker_idx].addListener("click", () => {
                        this.map_infowindow.marker_idx = marker_idx;
                        this.map_infowindow.setContent("<div style='font-weight: bold;'>" + this.markers[marker_idx].name + "</div>");
                        this.map_infowindow.open(this.map, this);
                        setTimeout(() => {
                            let map_locations = this.$el.querySelectorAll(".map__location");
                            if (map_locations.length > 0) {
                                map_locations.forEach((map_location) => {
                                    this.intersection_observer.unobserve(map_location);
                                });
                            }
                            let selected_map_location = this.$el.querySelector(".map__location.is_selected");
                            if (selected_map_location) {
                                this.intersection_observer.observe(selected_map_location);
                                setTimeout(() => {
                                    if (this.needs_to_scroll == true) {
                                        selected_map_location.scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
                                        setTimeout(() => {
                                            this.needs_to_scroll = false;
                                        }, 300);
                                    }
                                }, 600);
                            }
                        }, 0);
                    });
                }

                this.map.fitBounds(this.map_bounds);
            }

            return;
        },
        getMarkers() {
            if (this.endpoint) {
                return _api.request({
                    url: this.endpoint,
                    method: "get",
                    data: {}
                })
                .then((_response) => {
                    if (_storage.isSuccess(_response)) {
                        let response = _storage.getResponseData(_response);
                        this.markers = response.markers;
                    } else {
                    }
                })
                .catch((error) => {
                    this.$el.remove();
                });
            } else {
                return new Promise((resolve, reject) => {
                    this.$el.remove();
                    resolve();
                });
            }
        },
        selectMarker(marker_idx) {
            new this.googleMaps.event.trigger(this.markers_displayed[marker_idx], "click");
        },
        handle() {
            this.intersection_observer = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.intersectionRatio < 1) {
                        this.needs_to_scroll = true;
                    }
                });
            }, {
                root: null,
                rootMargin: "0px",
                threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1],
            });

            this.map_bounds = new this.googleMaps.LatLngBounds();
            this.map_geocoder = new this.googleMaps.Geocoder();
            this.map_infowindow = new this.googleMaps.InfoWindow({
                content: ""
            });

            this.getMarkers().then((response) => {
                if (this.markers.length > 0) {
                    this.map = new this.googleMaps.Map(this.$el.querySelector(".map"), {
                        center: {
                            lat: 54.136696,
                            lng: -2.988281
                        },
                        zoom: 5,
                        mapTypeId: this.googleMaps.MapTypeId.ROADMAP,
                        scrollwheel: false,
                        mapTypeControlOptions: {
                            mapTypeIds: [this.googleMaps.MapTypeId.ROADMAP, "map_style"]
                        },
                        panControl: true,
                        zoomControl: true,
                        mapTypeControl: true,
                        scaleControl: true,
                        streetViewControl: true,
                        overviewMapControl: false,
                        mapTypeControl: false
                    });

                    let styles = [{
                        featureType: "water",
                        stylers: [{
                            color: "#ABCCFB"
                        }]
                    }];

                    let styledMap = new this.googleMaps.StyledMapType(styles, {
                        name: "Map"
                    });

                    this.map.mapTypes.set("map_style", styledMap);
                    this.map.setMapTypeId("map_style");

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            if (position) {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                this.map.setCenter(new this.googleMaps.LatLng(this.latitude, this.longitude));
                                this.filterMarkers();
                                window.addEventListener("resize", () => {
                                    this.map.fitBounds(this.map_bounds);
                                });
                            }
                        });
                    } else {
                        window.addEventListener("resize", () => {
                            this.map.fitBounds(this.map_bounds);
                        });
                    }
                }
            }).catch((error) => { });
        }
    },
    mounted() {
        if (this.api_key) {
            loadGoogleMapsApi({
                key: this.api_key
            }).then((googleMaps) => {
                this.googleMaps = googleMaps;
                this.handle();
            }).catch((error) => {
                this.$el.remove();
            });
        } else {
            this.$el.remove();
        }
    },
    template: `
    <div class="map__wrapper">
        <div class="map"></div>
        <div class="container:xl">
            <ul class="map__locations">
                <li class="map__location" v-for="(marker, marker_idx) in markers_displayed" :class="{ is_selected: map_infowindow.marker_idx == marker_idx }" @click="selectMarker(marker_idx)">
                    <h3>{{ marker.title }}</h3>
                    <p>
                        {{ marker.address_line_1 }}
                        <template v-if="marker.address_line_2 != ''"><br />{{ marker.address_line_2 }}</template>
                        <template v-if="marker.town != ''"><br />{{ marker.town }}</template>
                        <template v-if="marker.postcode != ''"><br />{{ marker.postcode }}</template>
                        <template v-if="marker.phone != ''"><br />{{ marker.phone }}</template>
                    </p>
                    <p><em>{{ marker.distance }} miles away</em></p>
                </li>
            </ul>
        </div>
    </div>
    `
};

export default _map;