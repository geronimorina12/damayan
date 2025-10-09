<script setup>
import { ref, onMounted, watch, nextTick, onUnmounted, defineProps } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
});

const chartRef = ref(null);
let chartInstance = null;

const updateChart = () => {
  if (!chartInstance) return;

  const total = props.data.length;
  const current = total > 0 ? props.data.filter(d => d.status === 'current').length : 0;
  const others = total - current;

  chartInstance.setOption({
    title: { text: 'Current Deceased', left: 'center' },
    tooltip: { trigger: 'item' },
    legend: { bottom: 0 },
    series: [
      {
        name: 'Deceased Status',
        type: 'pie',
        radius: ['40%', '70%'],
        avoidLabelOverlap: false,
        label: { show: true, formatter: '{b}: {c}' },
        data: [
          { value: current, name: 'Current' },
          { value: others, name: 'Others' },
        ],
      },
    ],
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
