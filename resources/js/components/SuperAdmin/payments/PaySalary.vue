<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Pay salary</h4>
            </div>
            <div class="card-body">
                <form class="row" @submit.prevent="submitPayment" v-if="!salaryPaid">
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
                    <input type="text" class="form-control" v-model="form.amount" placeholder="Amount...">
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
                    <Button class="btn btn-success" :form="form">Pay</Button>
                  </div>
                  
                </form>
                <div class="text-center" v-else>
                  <h3>Salary Already Paid</h3>
                  <h6 class="text-success">{{ moment().format("MMMM") }}</h6>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      salaryPaid: false,
      form: new Form({
        teacherId: this.$route.params.userId,
        bankName: "",
        number: "",
        amount: "",
        note: "",
        photo: null,
      }),
      moment: moment,

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
        console.log(data);
        if(data.status == "ok") {
          this.salaryPaid = data.paid;
          this.salaryPaid = true;
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
          swal.fire("Success",data.msg,"success");
        }
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