<template>
    <div>
        <el-time-picker
                v-if="'time' === type"
                type="time"
                v-model="value"
                :name="name"
                :value-format="format"
        ></el-time-picker>

        <el-date-picker
                v-else
                v-model="value"
                :type="type"
                :name="name"
                :value-format="format"
        ></el-date-picker>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: [String, Array],
                required: true,
            },
            type: {
                type: String,
                default: 'datetime',
                validator: (value) => {
                    return ['date', 'datetime', 'time', 'daterange'].indexOf(value) !== -1
                }
            },
            defaultValue: String,
        },
        data() {
            return {
                value: null
            }
        },
        computed: {
            format() {
                const formats = {
                    time: 'HH:mm:ss',
                    date: 'yyyy-MM-dd',
                    datetime: 'yyyy-MM-dd HH:mm:ss'
                };

                return formats.hasOwnProperty(this.type) ? formats[this.type] : '';
            }
        },
        mounted() {
            this.value = this.defaultValue || null;
        }
    }
</script>
