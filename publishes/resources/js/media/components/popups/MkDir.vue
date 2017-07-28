<template>
    <div class="modal fade" id="mkdir">
        <form @submit.prevent="mkDir()">
            <div class="modal-dialog">
                <div class="modal-content">
                    <modal-header text="Make Directory"></modal-header>

                    <div class="modal-body">
                        <input class="form-control" v-model="name" placeholder="Images"/>
                    </div>

                    <modal-footer></modal-footer>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import ModalHeader from '../partials/ModalHeader.vue';
    import ModalFooter from '../partials/ModalFooter.vue';

    export default {
        components: {
            'modalHeader': ModalHeader,
            'modalFooter': ModalFooter,
        },
        data() {
            return {
                name: '',
            };
        },
        methods: {
            mkDir() {
                this.$emit('mkdir', this.name);

                this.name = '';
            }
        }
    };
</script>