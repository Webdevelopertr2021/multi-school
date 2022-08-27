<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Site Settings</h4>
            </div>
            <div class="card-body">
                <form class="row" @submit.prevent="updateSetting">

                  <div class="col-md-12 mb-4">
                    <label for="">Bonus money (Per Star)</label>
                    <input type="number" class="form-control" placeholder="Bonus per star..." v-model="form.per_star">
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Bonus money for no leave</label>
                    <input type="number" class="form-control" placeholder="Bonus for no leave..." v-model="form.no_leave">
                  </div>

                  <div class="col-md-12 mb-4">
                    <Button :form="form" class="btn btn-success">Save</Button>
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
        per_star: 0,
        no_leave: 0,
      })
    }
  },
  methods: {
    getSettingData() {
      axios.get("/admin/api/get-setting-data").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.form.per_star = data.bonus_per_star;
        this.form.no_leave = data.bonus_no_leave;
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    updateSetting() {
      this.form.post("/admin/api/update-setting").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Updated",data.msg,"success");
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getSettingData();
  }
}
</script>

<style>

</style>