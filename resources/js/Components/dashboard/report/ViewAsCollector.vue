<script setup>
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import html2pdf from "html2pdf.js";

const props = defineProps({
  deceased: {
    type: Object,
    default: () => ({}),
  },
  purok: String,
});

const contributions = ref([]);
const pdfContent = ref(null);
const getDeceased = ref({});
const getPurok = ref("");
const isLoading = ref(false);
let isMounted = false;

const fetchContributions = async (id, purok) => {
  if (!id || !purok) return;
  try {
    console.log("id: ", id);
    console.log("purok: ", purok);

    isLoading.value = true;
    const res = await axios.get(`/collector/contributions/${id}/${purok}`);
    contributions.value = res.data.contributions || [];
  } catch (error) {
    console.error(" Error fetching contributions:", error);
  } finally {
    isLoading.value = false;
  }
};

watch(
  [ () => props.purok],
  ([ newPurok]) => {
    getPurok.value = newPurok;
  },
  { immediate: false }
);
watch(() => props.deceased,
 (data) => {
  getDeceased.value = data
 }, {immediate: true}
)
onMounted(() => {
  isMounted = true;
  if (props.deceased?.member_id && props.purok) {
    fetchContributions(props.deceased.member_id, props.purok);
  }
});

const totalAmount = computed(() =>
  contributions.value.reduce(
    (sum, item) => sum + parseFloat(item.amount || 0),
    0
  )
);

const downloadAsPDF = () => {
  const element = pdfContent.value;
  const options = {
    filename: `Contributions_Report.pdf`,
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
  };

  html2pdf().from(element).set(options).save();
};
</script>


<template>
  <button @click="downloadAsPDF" class="download-btn btn btn-dark px-3 ms-2" title="Download PDF">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="22"
      height="22"
      fill="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        d="M12 3a1 1 0 011 1v9.586l3.293-3.293a1 1 0 011.414 1.414l-5 
        5a1 1 0 01-1.414 0l-5-5a1 1 0 
        011.414-1.414L11 13.586V4a1 1 0 011-1zm-7 
        14a1 1 0 100 2h14a1 1 0 100-2H5z"
      />
    </svg>
  </button>

  <!-- PDF Content only -->
  <div ref="pdfContent" class="document-container">
    <div class="header">
      <div class="organization-name">BULAN P.R.O.T.E.C DAMAYAN FEDERATION</div>
      <div class="organization-subtitle ">(People's Responsible Organization Transforming Community)</div>
      <div class="chapter-info fst-italic">Affiliated Bulan, Sorsogon Chapter Brgy. ______ C.Y 2021</div>
      <div class="established fst-italic">Since 2016</div>

      <div class="deceased-name">
        Deceased Name: <br>
        <p class="fw-light">{{ getDeceased?.deceased_name || '' }}</p>
      </div>
    </div>

    <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2">
      <div>
        Collector Name: <span class="fw-bold">{{ contributions[0]?.collector || '' }}</span>
      </div>
      <div>
        Purok: <span class="fw-bold">{{ contributions[0]?.purok?.charAt(contributions[0]?.purok?.length - 1) || 'all' }}</span>
      </div>
    </div>
    <div class="table-container">
      <div class="mb-3"><strong>MEMBERS</strong></div>
      <table class="members-table">
        <thead>
          <tr>
            <th>No.</th>
            <th>NAME</th>
            <th>STATUS</th>
            <th>AMOUNT</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(member, index) in contributions" :key="index">
            <td>{{ index + 1 }}</td>
            <td>
              {{ member?.member_contribution?.first_name || 'Member' }}
              {{ member?.member_contribution?.last_name || '' }}
            </td>
            <td>{{ member.status || '' }}</td>
            <td>₱{{ Math.floor(member.amount).toLocaleString()  || '' }}</td>
          </tr>
        </tbody>
      </table>
      <div class="total-amount">
        TOTAL AMOUNT: ₱{{ totalAmount.toLocaleString() }}
      </div>
    </div>

    <div class="signature">
      <div>Noted By:</div>
      <div class="president-name">Melva Canale Delariarte</div>
      <div>DAMAYAN PRESIDENT</div>
    </div>

  </div>
</template>

<style scoped>
@media print {
  .download-btn {
    display: none !important;
  }
}

.download-btn {
  background: #111;
  color: #fff;
  border: none;
  padding: 10px;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.2s ease-in-out;
  position: fixed;
  top: 20px;
  right: 20px;
}

.download-btn:hover {
  opacity: 0.8;
}
</style>


<style scoped>
.document-container {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  padding: 30px;
}
.header {
  text-align: center;
  margin-bottom: 30px;
}
.organization-name {
  font-weight: bold;
  font-size: 24px;
  margin-bottom: 5px;
}
.organization-subtitle {
  font-style: italic;
  margin-bottom: 5px;
}
.chapter-info {
  margin-bottom: 5px;
}
.established {
  margin-bottom: 20px;
}
.deceased-name {
  margin-bottom: 20px;
}
.table-container {
  margin-bottom: 30px;
}
.members-table {
  width: 100%;
  border-collapse: collapse;
}
.members-table th, .members-table td {
  border: 1px solid #000;
  padding: 8px;
  text-align: left;
}
.members-table th {
  background-color: #f0f0f0;
}
.total-amount {
  margin-top: 10px;
  font-weight: bold;
}
.signature {
  text-align: right;
  margin-top: 40px;
}
.president-name {
  font-weight: bold;
  margin-top: 40px;
  border-top: 1px solid #000;
  padding-top: 5px;
  display: inline-block;
}
/* Modern Select Section */
.select-container {
  max-width: 350px;
  margin: 20px auto;
  display: flex;
  flex-direction: column;
}

.select-label {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 6px;
  color: #333;
}
.select-wrapper {
  max-width: 420px;
  margin: 40px auto;
  display: flex;
  flex-direction: column;
  text-align: left;
}

.select-label {
  font-size: 17px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #1E293B;
}

/* Neumorphic Modern Select Box */
.select-box {
  position: relative;
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 
    4px 4px 12px rgba(0, 0, 0, 0.08),
    -4px -4px 10px rgba(255, 255, 255, 0.9);
  transition: box-shadow 0.3s ease;
}

.select-box:hover {
  box-shadow: 
    6px 6px 18px rgba(0, 0, 0, 0.12),
    -5px -5px 12px rgba(255, 255, 255, 0.95);
}

.select-box select {
  width: 100%;
  padding: 14px 45px 14px 14px;
  border: none;
  border-radius: 14px;
  background: transparent;
  font-size: 16px;
  font-weight: 500;
  color: #334155;
  outline: none;
  cursor: pointer;
  appearance: none;
}

/* Stylish dropdown icon */
.select-box::after {
  content: "▾";
  position: absolute;
  top: 50%;
  right: 18px;
  transform: translateY(-50%);
  font-size: 18px;
  color: #2563EB;
  pointer-events: none;
  transition: transform 0.2s ease;
}

/* Rotate arrow when active */
.select-box select:focus + .select-box::after,
.select-box:focus-within::after {
  transform: translateY(-50%) rotate(180deg);
}

/* Dropdown option style */
select option {
  font-size: 16px;
  padding: 12px;
  background: #ffffff;
  color: #1E293B;
}
.download-btn {
  position: relative;
  transform: translateY(-80%);
  background: #35383b;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.25s ease, transform 0.25s ease;
  z-index: 999;
}
</style>