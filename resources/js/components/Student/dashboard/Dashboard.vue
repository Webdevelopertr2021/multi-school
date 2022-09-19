<template>
  <div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-md-12 text-center">
                    <skeleton width="100px" height="100px" class="user-thumb-100" /><br>
                    <skeleton width="200px" height="20px" class="mt-2" />
                  </div>
                  <div class="col-md-12 mt-5">
                    <ul>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                      <li><skeleton width="300px" height="20px"></skeleton></li>
                    </ul>
                  </div>
                </div>

                <div class="row" v-else>
                  <div class="col-md-12 text-center">
                    <img v-if="profile.photo_url != null" class="user-thumb-100" :src="profile.photo_url" alt="">
                    <img v-else class="user-thumb-100" src="/image/portrait-placeholder.png" alt="">
                    <h5 class="mt-3">{{ profile.name }}</h5>
                  </div>
                  <div class="col-md-12 mt-5">
                    <ul>
                      <li>Phone : <strong>{{ profile.phone }}</strong></li>
                      <li>Email : <strong>{{ profile.email }}</strong></li>
                      <li>Address : <strong>{{ profile.address }}</strong></li>
                      <li>School : <strong>{{ profile.school.name }}</strong></li>
                      <li>Class : <strong>{{ profile.class.name }}</strong></li>
                      <li>Section : <strong>{{ profile.section.name }}</strong></li>
                    </ul>
                  </div>
                  <div class="col-md-12 mt-4">
                    <router-link :to="{name: 'student.my-profile'}" class="btn btn-primary">Edit Profile</router-link>
                  </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      isLoading: true,
      profile: {},
    }
  },
  methods : {
    getDashboardData() {
      axios.get("/student/api/get-dashboard-data").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.profile = data.profile;
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