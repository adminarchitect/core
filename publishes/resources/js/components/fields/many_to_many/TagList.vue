<template>
    <div>
        <section>
            <!-- v-if duplicates the .sync in order to force instant-search re-render -->
            <el-dialog title="Attach Item" :visible.sync="attachMode" v-if="attachMode">
                <instant-search :data-url="searchUrl" @select="onItemSelected"></instant-search>

                <div slot="footer" class="dialog-footer">
                    <el-button @click="attachMode = false">Cancel</el-button>
                    <el-button type="primary" @click="attach">Attach</el-button>
                </div>
            </el-dialog>

            <el-button size="small" @click="attachMode = true">+ Attach new</el-button>
            &nbsp;

            <el-tag closable
                    type="info"
                    v-for="(tag, index) in values"
                    :key="'tag-'+index"
                    @close="remove(tag)"
            >
                <input type="hidden" :name="name+'[]'" :value="tag[keyName]">
                {{ tag[labelName] }}
            </el-tag>
        </section>

        <div v-if="queuedToRemove.length">
            <h5>Trash</h5>
            <el-tag closable
                    type="warning"
                    v-for="(tag, index) in queuedToRemove"
                    :key="'tag-'+index"
                    @close="restore(tag)"
            >
                {{ tag[labelName] }}
            </el-tag>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                default: []
            },
            searchUrl: String,
            name: String,
            keyName: {
                type: String,
                default: 'id'
            },
            labelName: {
                type: String,
                default: 'name'
            }
        },
        data() {
            return {
                collection: [],
                current: null,
                attachMode: false,
                queuedToRemove: [],
            }
        },
        mounted() {
            this.collection = this.items || [];
        },
        computed: {
            values() {
                return this.collection.filter((item) => -1 === this.queuedToRemove.indexOf(item));
            }
        },
        methods: {
            remove(tag) {
                this.queuedToRemove.push(tag);
            },
            restore(tag) {
                this.queuedToRemove = this.queuedToRemove.filter(item => tag[this.keyName] !== item[this.keyName]);
            },
            onItemSelected($event) {
                this.current = {
                    [this.keyName]: $event.id,
                    [this.labelName]: $event.name,
                };
            },
            attach() {
                if (this.current) {
                    const exists = this.collection.filter(item => item[this.keyName] === this.current.id);
                    if (!exists.length) {
                        this.collection.push(this.current);
                    }

                    this.current = null;
                }
                this.attachMode = false;
            }
        }
    }
</script>

<style lang="scss">
    .el-tag {
        margin-right: 10px;
        margin-bottom: 10px;
    }
</style>