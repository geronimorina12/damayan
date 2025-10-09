<script setup>
import { ref, onMounted, watch, nextTick, onUnmounted, defineProps } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
  records: {
    type: Array,
    default: () => [],
  },
});

const chartRef = ref(null);
let chartInstance = null;

const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

const updateChart = () => {
  if (!chartInstance) return;

  const monthlyData = Array(12).fill(0);
  props.records.forEach(r => {
    const month = new Date(r.date_recorded).getMonth();
    monthlyData[month]++;
  });

  chartInstance.setOption({
    title: { text: 'Deceased Recorded', left: 'center' },
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: monthLabels },
    yAxis: { type: 'value', minInterval: 1 },
    series: [
      {
        name: 'Recorded',
        type: 'bar',
        data: monthlyData,
        itemStyle: { color: '#FF7F50' },
      },
    ],
    grid: {
      left: '3%',
      right: '4%',
      bottom: '8%',
      containLabel: true,
    },
  });

  chartInstance.resize();
};

watch(
  () => props.records,
  () => updateChart(),
  { immediate: true }
);

onMounted(async () => {
  await nextTick();
  if (!chartRef.value) return;
  chartInstance = echarts.init(chartRef.value);
  updateChart();
  window.addEventListener('resize', chartInstance.resize);
});

onUnmounted(() => {
  if (chartInstance) {
    window.removeEventListener('resize', chartInstance.resize);
    chartInstance.dispose();
    chartInstance = null;
  }
});
</script>

<template>
  <div class="chart-card">
    <div ref="chartRef" class="chart"></div>
  </div>
</template>

<style scoped>
.chart-card {
  flex: 1;
  min-width: 300px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 1rem;
}
.chart {
  width: 100%;
  height: 280px;
}
</style>
