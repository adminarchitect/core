<template>
    <div>
        <file-pond
                :name="'_media_[' + collection + ']'"
                ref="pond"
                label-idle="Drop files here..."
                :allow-multiple="true"
                accepted-file-types="image/jpeg, image/png"
                @init="handleFilePondInit"
                :imagePreviewHeight="150"
        />

        <media-pagination :page="page" :pages="pages"></media-pagination>

        <div class="filepond--list" style="position: relative;">
            <div v-for="file in media" class="filepond--item" style="height:150px; position: relative; float: left;">
                <fieldset class="filepond--file-wrapper">
                    <div class="filepond--file">
                        <button @click.prevent="detachMedia(file)" class="filepond--file-action-button filepond--action-revert-item-processing" data-align="right">
                            <svg width="26" height="26" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"></path>
                            </svg>
                            <span>Remove</span>
                        </button>
                        <div class="filepond--file-info">
                            <span class="filepond--file-info-main">
                                {{ file.name }}
                            </span>
                            <span class="filepond--file-info-sub">{{ file.size }}</span>
                        </div>
                        <div class="filepond--image-preview-wrapper">
                            <div class="filepond--image-preview-overlay filepond--image-preview-overlay-success" style="opacity:0.4;">
                                <svg width="500" height="90" viewBox="0 0 500 90" preserveAspectRatio="none">
                                    <rect x="0" width="500" height="90" fill="#fff" mask="url(#mask-2)"></rect>
                                </svg>
                            </div>

                            <div class="filepond--image-preview">
                                <div class="filepond--image-clip">
                                    <img :src="file.url" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>

        <media-pagination :page="page" :pages="pages"></media-pagination>
    </div>
</template>

<script>
    import $http from '../media/axios';
    import 'filepond/dist/filepond.min.css';
    import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
    import _debounce from 'lodash/debounce';
    import vueFilePond from 'vue-filepond';

    const FilePond = vueFilePond(
        require('filepond-plugin-file-validate-type')
    );

    export default {
        components: {
            FilePond
        },
        props: {
            id: {
                type: String,
                required: true,
            },
            collection: {
                type: String,
                default: 'default',
            },
            conversion: {
                type: String,
                default: 'default',
            },
            endpoint: {
                type: String,
                required: true
            },
        },
        data() {
            return {
                page: 1,
                media: [],
                pages: {},
                queued: 0
            }
        },
        methods: {
            async fetchMedia() {
                const response = await $http().get(this.endpoint, {
                    params: {
                        collection: this.collection,
                        page: this.page,
                    }
                })
                const {data, ...pages} = response.data;
                this.media = data;
                this.pages = pages;
            },
            async detachMedia(file) {
                if (!window.confirm('Are you sure?')) return false;
                await $http().delete(`${this.endpoint}/${file.id}`);
                if (this.media.length === 1 && this.page > 1) {
                    this.page--;
                }
                this.fetchMedia();
            },
            goNext() {
                this.page++;
                this.fetchMedia();
            },
            goPrev() {
                this.page--;
                this.fetchMedia();
            },
            handleFilePondInit() {
                const $this = this
                this.$refs.pond._pond.setOptions({
                    onaddfile(_, info) {
                        $this.queued++;
                    },
                    server: {
                        process: {
                            url: `${this.endpoint}/${this.collection}`,
                            ondata(formData) {
                                let token = document.head.querySelector('meta[name="csrf-token"]');
                                formData.append('_token', token.content);

                                return formData
                            },
                            onload(file) {
                                if (file && JSON.parse(file)) {
                                    $this.queued--;

                                    $this.clearQueue();
                                }
                            }
                        },
                        fetch: null,
                        revert: null,
                        restore: null,
                        load: null,
                    },
                });
            },
            clearQueue() {
                if (0 === this.queued) {
                    this.page = 1
                    this.fetchMedia()
                    this.$refs.pond._pond.removeFiles()
                }
            }
        },
        created() {
            this.$nextTick(this.fetchMedia)
            this.clearQueue = _debounce(this.clearQueue.bind(this), 500)
        },
    }
</script>

<style lang="scss">
    .filepond--item {
        width: calc(20% - .5em) !important;
    }

    .media-navigation, .media-navigation {
        font-size: 18px !important;
    }
</style>