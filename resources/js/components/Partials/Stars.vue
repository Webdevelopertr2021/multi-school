<template>
<ul class="stars">
    <template v-if="star == 0">
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>
    <template v-if="star == 0.5">
        <li><i class="fas fa-star-half-alt"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 1">
        <li><i class="fas fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 1.5">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star-half-alt"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 2">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 2.5">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star-half-alt"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 3">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 3.5">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star-half-alt"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 4">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="far fa-star"></i></li>
    </template>

    <template v-if="star == 4.5">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star-half-alt"></i></li>
    </template>

    <template v-if="star == 5">
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
        <li><i class="fas fa-star"></i></li>
    </template>
    
</ul>
</template>

<script>
export default {
    props: ['star'],
    data() {
        return {

        }
    }
}
</script>

<style>

</style>