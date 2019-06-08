<template>
    <div class="grid-container">
        <div class="grid-x grid-padding-x">
            <loader v-show="cafesLoadStatus == 1" :width="100" :height="100"></loader>
            <cafe-card v-for="cafe in cafes" :key="cafe.id" :cafe="cafe">
                <!--<router-link :to="{ name: 'cafe', params: { id: cafe.id} }">-->
            </cafe-card>
        </div>
    </div>
</template>