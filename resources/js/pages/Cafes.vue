<template>
  <div id="cafes" class="page">
    <cafe-map></cafe-map>
  </div>
</template>

<script>

    import CafeMap from '../components/cafes/CafeMap.vue';

    export default {
        components: {
            CafeMap
        }
    }
</script>

<style>
  div#cafe-map{
    position: absolute;
    top: 50px;
    left: 0px;
    right: 0px;
    bottom: 100px;
  }
</style>