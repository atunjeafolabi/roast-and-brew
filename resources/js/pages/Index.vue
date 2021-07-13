<style lang="scss">

    div#landing-page{
        position: absolute;
        top:75px;
        bottom: 0;
        left: 0;
        right: 0;
        div#bg-image{
            height: 100%;
            background: #7F5F2A url("../assets/images/coffee1.jpeg") no-repeat;
            /*filter: blur(2px);*/
            background-size: 100%;
        }
        div#welcome-text{
            position: absolute;
            top: 150px;
            left: 100px;
            z-index: 2;
            padding: 40px;
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0, 0.3); /* Black w/opacity/see-through */
            color: white;
            border: 1px solid 1px solid #251a17;
            box-shadow: rgb(255 173 89 / 40%) -6px 2px 16px 0px;
        }
    }
</style>
<template>
    <div id="landing-page">
        <div id="bg-image"></div>
        <div id="welcome-text">
            <h2>Welcome to ROAST</h2>
            <h4>Locate coffee shops available nationwide</h4>
            <p>Browse nearby cofee shops and cafes</p>

            <router-link to="cafes" class="button warning">Search Cafes</router-link>
        </div>
    </div>
</template>
