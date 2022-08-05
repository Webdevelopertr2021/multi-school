<template>
  <div class="row justify-content-center">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Total Students</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalStudent }}</h2>
                  <router-link :to="{name:'admin.student-list'}" class="btn btn-sm btn-primary">See students</router-link>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="/backend/img/banner/1.png" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15"> Total Teachers</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalTeacher }}</h2>
                  <router-link :to="{name:'admin.all-teacher'}" class="btn btn-sm btn-primary">See Teachers</router-link>
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
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Total Supervisors</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalSuper }}</h2>
                  <router-link :to="{name:'admin.supervisor-list'}" class="btn btn-sm btn-primary">See supervisors</router-link>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="/backend/img/banner/3.png" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Total Admins</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalAdmin }}</h2>
                  <router-link :to="{name:'admin.admin-list'}" class="btn btn-sm btn-primary">See supervisors</router-link>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="/backend/img/banner/4.png" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Total Schools</h5>
                  <skeleton class="d-block" v-if="isLoading" width="50px" height="25px" />
                  <h2 v-else class="mb-3 font-18">{{ dashboardData.totalSchool }}</h2>
                  <router-link :to="{name:'admin.school-list'}" class="btn btn-sm btn-primary">See schools</router-link>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="/backend/img/banner/4.png" alt="">
                </div>
              </div>
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
  methods: {
    getDashboardData() {
      axios.get("/admin/api/get-dashboard-data").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.dashboardData = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getDashboardData();
  }
}
</script>

<style>

</style>