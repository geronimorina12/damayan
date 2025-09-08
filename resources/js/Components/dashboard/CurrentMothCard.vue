<script setup>
import { nextTick, onMounted, onUnmounted, watch, ref } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  }
});

let getData = ref([]);
let barChartInstance = null;

watch(
  () => props.data,
  (newData) => {
    getData.value = newData;
    if (barChartInstance) updateChart();
  },
  { immediate: true }
);

const updateChart = () => {
  if (!barChartInstance) return;

  barChartInstance.setOption({
    title: { text: 'Current Month', left: 'center' },
    tooltip: {},
    xAxis: { data: ['Total', 'Disbursed', 'Balance'] },
    yAxis: {},
    series: [
      {
        name: 'Amount',
        type: 'bar',
        data: [
          getData.value[0] || 0,
          getData.value[1] || 0,
          getData.value[2] || 0
        ],
        itemStyle: {
          color: function (params) {
            const colors = ['#0000ff', '#ffa500', '#00cc00'];
            return colors[params.dataIndex];
          }
        }
      }
    ]
  });

  barChartInstance.resize();
};

onMounted(async () => {
  await nextTick();
  const chartDom = document.getElementById('barChart');
  if (chartDom) {
    barChartInstance = echarts.init(chartDom);
    updateChart();
    window.addEventListener('resize', barChartInstance.resize);
  }
});

onUnmounted(() => {
  if (barChartInstance) {
    window.removeEventListener('resize', barChartInstance.resize);
    barChartInstance.dispose();
    barChartInstance = null;
  }
});
</script>

<template>
  <div id="barChart" class="bar-chart-container bg-white rounded shadow-sm p-3"></div>
</template>

<style scoped>
.bar-chart-container {
  width: 100%;
  height: 300px;
}

@media (max-width: 768px) {
  .bar-chart-container {
    height: 250px;
  }
}

@media (max-width: 480px) {
  .bar-chart-container {
    height: 220px;
  }
}
</style>
