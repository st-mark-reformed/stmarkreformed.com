// Make sure FAB is defined
window.FAB = window.FAB || {};

function runGoogleMap(F) {
    'use strict';

    if (! window.jQuery || ! F.controller) {
        setTimeout(function() {
            runGoogleMap(F);
        }, 10);
        return;
    }

    F.controller.make('GoogleMap', {
        map: null,
        markers: [],

        init: function() {
            var self = this;

            if (F.GlobalModel.get('googleMapsApiLoaded')) {
                self.initMap();
                return;
            }

            F.assets.load({
                root: 'https://maps.googleapis.com/',
                js: '/maps/api/js?key=' + F.GlobalModel.get('googleApiKey'),
                success: function() {
                    F.GlobalModel.set('googleMapsApiLoaded', true);
                    self.initMap();
                }
            });
        },

        initMap: function() {
            var self = this;
            // Default to the church
            var initialCenterCoords = {
                latitude: '35.9930301',
                longitude: '-86.8140445'
            };
            var elInitialCenter = self.$el.data('initialCenter');

            if (typeof elInitialCenter === 'object') {
                if (elInitialCenter.latitude) {
                    initialCenterCoords.latitude = elInitialCenter.latitude;
                }

                if (elInitialCenter.longitude) {
                    initialCenterCoords.longitude = elInitialCenter.longitude;
                }
            }

            self.map = new window.google.maps.Map(self.el, {
                // Center on church
                center: new window.google.maps.LatLng(
                    initialCenterCoords.latitude,
                    initialCenterCoords.longitude
                ),
                zoom: 7
            });

            self.setMarkersFromEl();
            self.setMarkersFromVarId();
        },

        setMarkersFromEl: function() {
            var self = this;
            var markers = self.$el.data('markers');

            if (typeof markers !== 'object' ||
                markers.constructor !== Array ||
                ! markers.length
            ) {
                return;
            }

            for (var i in markers) {
                self.setMarker(markers[i]);
            }

            self.fitMapToMarkers();
        },

        setMarkersFromVarId: function() {
            var self = this;
            var varId = self.$el.data('markersVarId');
            var markers;

            if (! window.googleMapMarkers || ! window.googleMapMarkers[varId]) {
                return;
            }

            markers = window.googleMapMarkers[varId];

            for (var i in markers) {
                self.setMarker(markers[i]);
            }

            self.fitMapToMarkers();
        },

        setMarker: function(markerJson) {
            var self = this;
            var infoWindow;
            var marker;

            if (! markerJson.latitude || ! markerJson.longitude) {
                return;
            }

            marker = new window.google.maps.Marker({
                position: {
                    lat: parseFloat(markerJson.latitude),
                    lng: parseFloat(markerJson.longitude)
                },
                map: self.map
            });

            self.markers.push(marker);

            if (! markerJson.markerContent) {
                return;
            }

            infoWindow = new window.google.maps.InfoWindow({
                content: markerJson.markerContent
            });

            marker.addListener('click', function() {
                infoWindow.open(self.map, marker);
            });
        },

        fitMapToMarkers: function() {
            var self = this;
            var markers = self.markers;
            var bounds = new window.google.maps.LatLngBounds();
            var extendPoint1;
            var extendPoint2;

            if (! markers.length) {
                return;
            }

            for (var i in markers) {
                if (markers.hasOwnProperty(i)) {
                    bounds.extend(markers[i].getPosition());
                }
            }

            // Don't zoom in too far
            extendPoint1 = new window.google.maps.LatLng(
                bounds.getNorthEast().lat() + 0.001,
                bounds.getNorthEast().lng() + 0.001
            );
            extendPoint2 = new window.google.maps.LatLng(
                bounds.getNorthEast().lat() - 0.001,
                bounds.getNorthEast().lng() - 0.001
            );
            bounds.extend(extendPoint1);
            bounds.extend(extendPoint2);

            self.map.fitBounds(bounds);
        }
    });
}

runGoogleMap(window.FAB);
