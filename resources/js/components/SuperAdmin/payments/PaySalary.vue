<template>
  <div class="row justify-content-center">
    <div class="col-md-5 mb-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h4>Salary Summary</h4>
          <p>Status : &nbsp;<strong>{{ salaryData.paid==true?'Paid':'Unpaid' }}</strong></p>
        </div>
        <div class="card-body">
          <div class="row" v-if="isLoading">
            <div class="col-12 mb-3">
              <skeleton width="100%" height="30px" />
            </div>
            <div class="col-12 mb-3">
              <skeleton width="100%" height="30px" />
            </div>
            <div class="col-12 mb-3">
              <skeleton width="100%" height="30px" />
            </div>
            <div class="col-12 mb-3">
              <skeleton width="100%" height="30px" />
            </div>
          </div>
          <div class="row" v-else>
            <div class="col-12">
              <h6><strong>Month : {{ salaryData.month }}</strong></h6>
              <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Base Salary :
                  <span class="text-success"><strong>{{ salaryData.base_salary }} $</strong></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>Bonus for rating - {{ salaryData.extra.star }} <i class="fas fa-star text-warning"></i> ({{ salaryData.extra.rating_count }} Rating)</span>
                  <span class="text-success"><strong>{{ salaryData.extra.salary_rating }} $</strong></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  No leave in last 4 month Bonus ({{ salaryData.extra.total_leaves }} Leave)
                  <span class="text-success"><strong>{{ salaryData.extra.salary_no_leave }} $</strong></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <strong>Total</strong>
                  <span><strong>{{ salaryData.base_salary+salaryData.extra.salary_rating+salaryData.extra.salary_no_leave  }} $</strong></span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Pay salary</h4>
            </div>
            <div class="card-body">
                <form class="row" @submit.prevent="submitPayment">
                
                  <div class="col-md-12 mb-4">
                    <label for="">Salary Month</label>
                    <select class="form-control" v-model="form.month" @change="filterSalaryData">
                      <option :value="moment().format('MM')">{{ moment().format("MMMM") }}</option>
                      <option :value="moment().subtract(1,'month').format('MM')">{{ moment().subtract(1,"month").format("MMMM") }}</option>
                    </select>
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Bank name <small>(optional)</small></label>
                    <input type="text" class="form-control" v-model="form.bankName" placeholder="Bank name...">
                  </div>
                  <div class="col-md-12 mb-4">
                    <label for="">Reciept Number</label>
                    <input type="text" class="form-control" v-model="form.number" placeholder="Reciept number...">
                  </div>
                  <div class="col-md-12 mb-4">
                    <label for="">Amount</label>
                    <input type="text" class="form-control" v-model="form.amount" placeholder="Amount..." readonly>
                  </div>
                  <div class="col-md-12 mb-4">
                    <label for="">Note</label>
                    <input type="text" class="form-control" v-model="form.note" placeholder="Note...">
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Attachments</label>
                    <input type="file" class="form-control-file" @change="fileChange">
                  </div>

                  <div class="col-md-12 mb-4">
                    <Button v-if="!salaryData.paid" class="btn btn-success" :form="form">Pay</Button>
                  </div>
                  
                </form>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: new Form({
        teacherId: this.$route.params.userId,
        bankName: "",
        number: "",
        amount: "",
        note: "",
        photo: null,
        month : moment().format('MM'),
      }),
      moment: moment,
      salaryData: {},
      isLoading: true,
    }
  },
  methods: {
    getSalaryData() {
      axios.get("/admin/api/get-salary-info",{
        params: {
          userId: this.$route.params.userId
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.salaryData = data.salary_data;
          this.form.amount = data.salary_data.base_salary+data.salary_data.extra.salary_rating+data.salary_data.extra.salary_no_leave;
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: "admin.teacher-ratings",
            params: {
              teacherId: this.$route.params.userId,
            }
          });
        }
      }).catch(err=>{
        this.$router.push({
          name: "admin.teacher-ratings",
          params: {
            teacherId: this.$route.params.userId,
          }
        });
        console.error(err.response.data);
      });
    },
    fileChange(e) {
      let file = e.target.files[0];
      if(file) {
        this.form.photo = file;
      }
      else {
        this.form.photo = null;
      }
    },
    async submitPayment() {
      await this.form.post("/admin/api/submit-payment").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == 'ok'){
          swal.fire("Success",data.msg,"success").then(()=>{
            window.location.reload();
          });
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    filterSalaryData() {
      this.isLoading = true;
      axios.get("/admin/api/filter-salary-data",{
        params : {
          userId : this.$route.params.userId,
          month: this.form.month
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        if(data.status == "ok") {
          this.salaryData = data.salary_data;
        }
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getSalaryData();
  }
}
</script>

<style>

</style>