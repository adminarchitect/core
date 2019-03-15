<template>
    <span>
        <input type="hidden" :name="name" :value="(value || null)">
        <el-select
                v-model="value"
                filterable
                clearable
                remote
                :remote-method="lookup"
                :loading="loading"
                :style="{width: width +'px'}"
                @change="onValueChanged"
                placeholder="Search"
        >
            <el-option v-for="(item, index) in items" :key="'item-'+index" :label="item.name"
                       :value="item.id"></el-option>
        </el-select>
    </span>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true
            },
            dataUrl: String,
            defaultValue: Number | String,
            width: {
                type: Number,
                default: 400
            },
        },
        data() {
            return {
                value: null,
                loading: false,
                items: []
            }
        },
        methods: {
            lookup(query, callback) {
                if (query !== '') {
                    this.loading = true;
                    let url = this.dataUrl + (-1 !== this.dataUrl.indexOf('?') ? '&' : '?') + 'query=' + decodeURIComponent(query);
                    $.get(url).then((r) => {
                        this.items = r.items;
                        this.loading = false;

                        callback.apply(this);
                    });
                } else {
                    this.items = [];
                }
            },
            onValueChanged(id) {
                let $item = this.items.filter(item => parseInt(item.id) === parseInt(id));

                this.$emit('select', $item[0]);
            }
        },

        mounted() {
            if (this.defaultValue) {
                this.lookup(this.defaultValue, () => {
                    if (this.items.length) {
                        this.value = this.items[0].id;
                    }
                });
            }
        }
    }
</script>