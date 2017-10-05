<template>
    <div :class="{'panel-warning': !collection.length, 'panel-default': collection.length}">
        <div class="panel-heading" v-if="!collection.length">
            <h3 class="panel-title">No files found!</h3>
        </div>

        <div v-if="collection.length" class="media-list" id="media-library">
            <div class="row filemanager">
                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2" v-for="file in collection">
                    <div class="thmb" :class="{'checked': checked(file)}">
                        <label class="ckbox">
                            <input type="checkbox" v-model="selected" :value="file">
                            <span></span>
                        </label>

                        <div class="thmb-prev text-center" v-if="!file.isImage">
                            <i :class="file.icon"></i>
                        </div>

                        <div class="thmb-prev text-center image-container">
                            <div class="image"
                                 v-if="file.isImage"
                                 :style="{ backgroundImage: 'url('+ file.url +')' }">
                            </div>
                        </div>

                        <h5 class="fm-title">{{ file.basename | truncate(25) }}</h5>
                        <small class="text-muted">{{ file.createdAt }}</small>
                        <br/>
                        <small class="text-muted text-primary">{{ file.size }} Bytes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            collection: {
                type: Array,
                default: () => [],
            }
        },

        computed: {
            selected: {
                get() {
                    return this.$store.state.selection.selected;
                },
                set(collection) {
                    this.$store.dispatch('selection/set', collection);
                },
            }
        },

        methods: {
            checked(file) {
                return this.$store.getters['selection/has'](file);
            }
        }
    };
</script>

<style scoped>
    .image-container {
        padding: 0 !important;
    }

    .image-container .image {
        width: 100%;
        height: 176px;
        background-size: cover !important;
        background-repeat: no-repeat;
        background-position: 50% 50% !important;
    }

    .thmb-prev {
        padding: 40px;
        background: #d4d9e3
    }

    .thmb-prev i {
        font-size: 96px;
    }

    .thmb:hover .ckbox,
    .thmb.checked .ckbox {
        display: inline-block;
    }
</style>