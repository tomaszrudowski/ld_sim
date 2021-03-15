<script>
    import { Bar, mixins } from 'vue-chartjs'
    const { reactiveProp } = mixins
    export default {
        extends: Bar,
        mixins: [reactiveProp],
        name: "bar-chart",
        props: ['options'],
        mounted () {
            console.log("chart mounted");
            this.renderChart(this.chartData, this.options)
        }
    }
</script>

<style scoped>

</style>
