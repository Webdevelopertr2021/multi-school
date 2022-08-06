<template>
  <div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Total Teachers</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalTeacher }}</h2>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="/backend/img/banner/2.png" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="row" v-if="isLoading">
            <div class="col-md-6 mb-4">
              <skeleton class="d-block mb-3" width="100%" height="40px" />
              <skeleton class="d-block mb-3" width="100%" height="40px" />
              <skeleton class="d-block mb-3" width="100%" height="40px" />
              <skeleton class="d-block mb-3" width="100%" height="40px" />
            </div>
            <div class="col-md-6 mb-4">
              <skeleton class="d-block mb-3" width="100%" height="40px" />
              <skeleton class="d-block mb-3" width="100%" height="40px" />
              <skeleton class="d-block mb-3" width="100%" height="40px" />
            </div>
          </div>
          <div class="row" v-else>
            <div class="col-md-6 mb-4">
              <h4><strong>User info</strong></h4>
              <ul class="list-group mb-4">
                <li class="list-group-item"><b>Name : {{ dashboardData.user.name }}</b></li>
                <li class="list-group-item"><b>Phone : {{ dashboardData.user.phone }}</b></li>
                <li class="list-group-item"><b>Email : {{ dashboardData.user.email }}</b></li>
                <li class="list-group-item"><b>School : {{ dashboardData.school.name }}</b> </li>
              </ul>
              <router-link :to="{name: 'superv.my-profile'}" class="btn btn-sm btn-warning text-white">Edit profile</router-link>
            </div>

            <div class="col-md-6 mb-4">
              <h4><strong>Assigned School</strong></h4>
              <ul class="list-group">
                <li class="list-group-item"><b>School Name : {{ dashboardData.school.name }}</b> </li>
                <li class="list-group-item"><b>Address : {{ dashboardData.school.address }}</b></li>
                <li class="list-group-item"><b>School CODE : {{ dashboardData.school.code }}</b></li>
              </ul>
            </div>

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
      isLoading: true,
      dashboardData: {},
    }
  },
  methods : {
    getData() {
      axios.get("/supervisor/api/get-dashboard-data").then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        this.dashboardData = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getData();
  }
}
</script>

<style>

</style>