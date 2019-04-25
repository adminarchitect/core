<template>
    <div class="row" v-if="pages.last_page && pages.last_page > 1">
        <div class="col-6 col-md-6 text-right mr-2">
            <span v-if="!pages.prev_page_url" class="media-navigation">&laquo;</span>
            <a v-else href="#" @click.prevent="goPrev" class="media-navigation">&laquo;</a>
        </div>
        <div class="col-6 col-md-6 text-left ml-2">
            <span v-if="!pages.next_page_url" class="media-navigation">&raquo;</span>
            <a v-else href="#" @click.prevent="goNext" class="media-navigation">&raquo;</a>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            page: {type: Number, default: 1},
            pages: {},
        }
    }
</script>