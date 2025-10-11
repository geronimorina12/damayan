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
    const month = new Date(r.date_of_death).getMonth();
    monthlyData[month]++;
  });

  chartInstance.setOption({
    title: { text: 'Count Of Deceased Members Recorded In This System', left: 'center' },
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: monthLabels },
    yAxis: { type: 'value', minInterval: 1 },
    series: [
      {
        name: 'Recorded',
        type: 'bar',
        data: monthlyData,
        itemStyle: { color: '#0000ff' },
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
  <div>
    <div class="d-flex justify-content-end view-all-btn me-2">
      <button type="button" class="btn btn-primary view-all-btn" data-bs-toggle="modal" data-bs-target="#viewAll">
        View All
      </button>
    </div>

    <div class="chart-card">
      <div ref="chartRef" class="chart"></div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewAll" tabindex="-1" aria-labelledby="viewAllLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="viewAllLabel">All Deceased Records</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div v-if="props.records.length > 0">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Deceased Name</th>
                    <th>Date of Death</th>
                    <th>Member ID</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(record, index) in props.records" :key="record.report_id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ record.deceased_name }}</td>
                    <td>{{ new Date(record.date_of_death).toLocaleDateString() }}</td>
                    <td>{{ record.member_id }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="text-center text-muted">
              No records found.
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
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
.view-all-btn {
  transform: translateY(50%);
  z-index: 6;
}
</style>
