<template>
    <div v-if="dataReady == true">

        <GoogleMap id="map" ref="map_ref" :api-key="mapKey"
            :center="center" :zoom="zoom"  style="width: 100%; height: 280px">

            <Marker :options="markerOptions" :draggable="true" ref="marker" @dragend="dragMarker"/>

            <Circle :options="circle" />

        </GoogleMap>

    </div>
</template>

<script lang='ts'>
import { defineComponent, ref, onMounted } from 'vue';
import { GoogleMap , Marker, Circle  } from 'vue3-google-map';

export default defineComponent({
    name : "GoogleMapWithMakers",
    components: {
        GoogleMap,
        Marker,
        Circle,
    },
    emit: ['onChange'],
    props : {
        lat :{
            type: Number,
            default :0
            } ,
        lng: {
            type: Number,
            default :0
            } ,
        radius: {
            type: Number,
            default: 1
        },
        // onChange : Function,
        draggable: {
            type: Boolean,
            default : true
            },
        mapKey: {
            type: String,
            default: '000'
        }
    },
    setup(props, { emit }) {
        const map_ref = ref();
        const marker = ref();
        const m_to_mi = 1610;

        const center = ref({
            lat: props.lat,
            lng: props.lng,
        });

        const markerOptions = ref({
            position : center.value,
            draggable: true,
        })

        const circle = ref();

        let zoom = 17;
        const km = props.radius / 0.621371;

        if (km == 0) {
            zoom = 13;
        }
        else if (km <= 0.5) {
            zoom = 15;
        }
        else if (km <= 1) {
            zoom = 14;
        }
        else if (km <= 2.5) {
            zoom = 13;
        }
        else if (km <= 5) {
            zoom = 12;
        }
        else if (km <= 10) {
            zoom = 11;
        }
        else if (km <= 25) {
            zoom = 9;
        }
        else if (km <= 50) {
            zoom = 8;
        }
        else if (km <= 100) {
            zoom = 7;
        }
        else if (km <= 200) {
            zoom = 6;
        }
        else if (km <= 500) {
            zoom = 5;
        }
        else {
            zoom = 13;
        }

        const dataReady = ref(false);

        function loadMap(){
            markerOptions.value.position = center.value;
            circle.value = {
                center: center.value,
                radius: props.radius * m_to_mi,
                strokeColor: "#FF0000",
                strokeOpacity: 0.5,
                strokeWeight: 2,
                fillColor: "#0000FF",
                fillOpacity: 0.2,
            };
            dataReady.value = true;
        }

        function dragMarker(v) {
            center.value = {
                lat: v.latLng.lat(),
                lng: v.latLng.lng()
            }
            emit('onChange', center.value);
            loadMap();
        }

        onMounted(() => {
            loadMap();
        });

        return {
            dragMarker,
            dataReady,
            map_ref,
            marker,
            circle,
            center,
            zoom,
            markerOptions,
         }
    },
})
</script>
