<script setup>
import { ref, onMounted, watch, nextTick, onUnmounted, defineProps } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
  data: {
    type: Array,
    default: () => [{}, []],
  },
});

const chartRef = ref(null);
let chartInstance = null;

const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

const updateChart = () => {
  if (!chartInstance) return;

  const collectedMap = props.data[0] || {};
  const disbursedMap = props.data[1] || {};

  const collected = [];
  const disbursed = [];

  for (let i = 1; i <= 12; i++) {
    collected.push(collectedMap[i]?.total_collected ?? 0);
    disbursed.push(disbursedMap[i]?.total_disbursed ?? 0);
  }

  chartInstance.setOption({
    title: { text: 'Monthly Overview', left: 'center' },
    tooltip: { trigger: 'axis' },
    legend: {
      data: ['Collected', 'Disbursed'],
      top: '10%',
    },
    xAxis: { type: 'category', data: monthLabels },
    yAxis: {
      type: 'value',
      minInterval: 1,
      splitLine: { show: true },
      axisLine: { show: true },
    },
    series: [
      {
        name: 'Collected',
        type: 'line',
        smooth: true,
        data: collected,
        itemStyle: { color: '#0000ff' },
        lineStyle: { width: 2, color: '#0000ff' },
        symbol: 'circle',
        symbolSize: 6,
      },
      {
        name: 'Disbursed',
        type: 'line',
        smooth: true,
        data: disbursed,
        itemStyle: { color: '#00cc00' },
        lineStyle: { width: 2, color: '#00cc00' },
        symbol: 'circle',
        symbolSize: 6,
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
  () => props.data,
  () => updateChart(),
  { immediate: true }
);

onMounted(async () => {
  await nextTick();
  if (!chartRef.value) {
    console.warn('Chart container not found');
    return;
  }

  chartInstance = echarts.init(chartRef.value);
  updateChart();

  // Resize on window resize
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
  <div
    ref="chartRef"
    class="bg-white rounded shadow-sm p-3 responsive-overview-chart"
  ></div>
</template>

<style scoped>
.responsive-overview-chart {
  width: 100%;
  height: 300px;
}
@media (max-width: 768px) {
  .responsive-overview-chart {
    height: 250px;
  }
}
@media (max-width: 480px) {
  .responsive-overview-chart {
    height: 220px;
  }
}
</style>
