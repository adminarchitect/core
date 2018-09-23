<template>
    <div class="media-carousel">
        <p v-if="enabled && !readonly">
            <input class="drop-zone__file"
                   type="file"
                   multiple
                   :accept="acceptedTypes"
                   :name="'_media_[' + id + '][]'"
                   :ref="ref"
                   @change="onFilesSelected"
            />
        </p>
        <input v-for="media in pendingRemoval" type="hidden" name="_media_[_trash_][]" :value="media">

        <div style="position: relative">
            <div v-if="!readonly && (editable || !media.length)"
                 class="drop-zone__area"
                 :class="{'placeholder': (!media.length && !editable)}"
                 @dragover.stop.prevent.self=""
                 @dragleave.stop.prevent.self="onDragLeave"
                 @drop.stop.prevent.self="addToQueue"
                 @dragenter.stop.prevent.self="onDragEnter"
            >
                Drop files here
            </div>

            <div :class="{'preloading': preloading}">
                <img v-if="preloading" src="/admin/editors/skins/lightgray/img/loader.gif" alt="">

                <div v-if="loaded === media.length && !preloading"
                     :id="'media-' + id"
                     class="carousel slide"
                     data-ride="carousel"
                     data-interval="0"
                     style="max-width: 360px;"
                     @dragover.stop.prevent=""
                     @dragenter.stop.prevent="onDragEnter"
                >
                    <!-- Indicators -->
                    <ol class="carousel-indicators" v-if="media.length > 1">
                        <li v-for="(item, index) in media"
                            :data-target="'#media-' + id"
                            :data-slide-to="index"
                            :class="{'active': active === index}"
                            @click.prevent="setActive(index)"
                        ></li>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">
                        <div class="item active" v-if="!media.length">
                            <img src="/images/vendor/fancybox/dist/blank.gif" style="width: 480px; height: 240px;" alt="">
                        </div>

                        <div v-for="(item, index) in media" class="item"
                             :class="{'active': active === index, 'removed': pending = pendingForRemoval(item)}"
                        >
                            <a class="media-carousel__toolbar" v-if="!readonly">
                                <i v-if="!pending"
                                   @click.prevent="removeMedia(item)"
                                   title="Queue for removal"
                                   class="media-carousel__toolbar__action fa fa-trash"></i>

                                <i v-else
                                   @click.prevent="restoreMedia(item)"
                                   title="Un-queue removal"
                                   class="media-carousel__toolbar__action fa fa-random"></i>
                            </a>

                            <a class="fancybox" :href="mediaUrl(item)" :rel="'media_' + id">
                                <img :src="mediaUrl(item)"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul v-if="pendingRemoval.length" class="drop-zone__queue">
            <li v-text="pendingCount"></li>
        </ul>

        <ul v-if="queue.length" class="drop-zone__queue">
            <li>
                <a class="pull-right" @click.prevent="clearQueue"><i class="fa fa-trash"></i></a>
                <strong>Upload queue:</strong>
                <div class="clearfix"></div>
            </li>
            <li v-for="file in queue" v-text="file.name"></li>
        </ul>
    </div>
</template>

<script>
  export default {
    props: {
      id: {
        type: String,
      },
      media: {
        type: Array,
        default: [],
      },
      conversion: {
        type: String,
        default: '',
      },
      readonly: {
        type: Boolean,
        default: false,
      },
    },

    data() {
      return {
        editable: false,
        queue: [],
        pendingRemoval: [],
        enabled: true,
        active: 0,
        loaded: 0,
        preloading: false,
      };
    },

    computed: {
      ref() {
        return 'scaffold_' + this.id;
      },

      acceptedTypes() {
        return (this.mimeTypes || []).join(', ');
      },

      pendingCount() {
        return `${this.pendingRemoval.length} item(s) queued for removal.`;
      },
    },

    methods: {
      pendingForRemoval(item) {
        return -1 !== this.pendingRemoval.indexOf(
            parseInt(item.id),
        );
      },

      addToQueue(event) {
        this.input().files = event.dataTransfer.files;

        this.editable = false;
      },

      clearQueue() {
        if (!confirm('Are you sure?')) {
          return false;
        }

        this.enabled = false;

        this.$nextTick(() => {
          this.enabled = true;
          this.queue = [];
        });
      },

      onDragEnter() {
        this.editable = true;
      },

      onDragLeave() {
        this.editable = false;
      },

      mediaUrl(item) {
        return this.conversion ? item.conversions[this.conversion] : item.url;
      },

      removeMedia(item) {
        this.pendingRemoval.push(
            parseInt(item.id),
        );
      },

      restoreMedia(item) {
        this.pendingRemoval.splice(
            this.pendingRemoval.indexOf(item.id),
            1,
        );
      },

      onFilesSelected(event) {
        this.queue = event.target.files;
      },

      input() {
        return this.$refs[this.ref];
      },

      setActive(index) {
        setTimeout(() => this.active = index, 500);
      },
    },

    mounted() {
      if ((this.media || []).length) {
        this.preloading = true;
        this.media.forEach(item => {
          let image = new Image;
          image.onload = () => {
            ++this.loaded;
            if (this.loaded === this.media.length) {
              this.preloading = false;
              setTimeout(function() {
                $('.media-carousel .fancybox').fancybox();
              }, 50);
            }
          };
          image.src = item.url;
        });
      }
    },
  };
</script>

<style lang="scss">
    .preloading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 300px;
        width: 400px;
        border: 1px solid #d8dce3;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    .media-carousel__toolbar {
        opacity: 0;
        padding: 10px;
        width: 100%;
        background-color: rgba(100, 100, 100, 0.7);
        position: absolute;
        text-align: right;
    }

    .item:hover .media-carousel__toolbar {
        -webkit-transition: opacity 0.25s ease-in-out;
        -moz-transition: opacity 0.25s ease-in-out;
        transition: opacity 0.25s ease-in-out;
        opacity: 1;
        display: block;
    }

    .media-carousel__toolbar__action {
        cursor: pointer;
        color: white;
        opacity: 1;
    }

    .carousel-indicators {
        position: relative;
        margin-top: 60px;

        li, li.active {
            background-color: #22629d;
        }

        li.active {
            width: 14px;
            height: 14px;
        }
    }

    .drop-zone__area {
        position: absolute;
        border: 3px dashed lightslategray;
        color: black;
        font-size: 1.2em;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 100%;
        width: 100%;
        opacity: 0.8;
        z-index: 100;
    }

    .drop-zone__area.placeholder {
        opacity: 0.3;
    }

    .drop-zone__queue {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .drop-zone__queue li {
        text-align: left;
        list-style: none;
        padding: 8px;
        border-bottom: 1px solid #ccc;
    }

    .drop-zone__queue li:last-child {
        border-bottom: none;
    }

    .removed {
        opacity: 0.3;
    }
</style>