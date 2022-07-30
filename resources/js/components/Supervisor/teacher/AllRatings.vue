<template>
  <div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-12">
                    <div class="d-flex">
                      <skeleton class="user-thumb-100" width="100px" height="100px" />
                      <div class="ml-3 pt-2">
                        <skeleton width="200px" height="30px" />
                        <br>
                        <br>
                        <skeleton width="200px" height="20px" />
                        <br>
                        <skeleton width="200px" height="20px" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-12">
                    <div class="d-flex">
                      <div class="text-center">
                        <img v-if="teacherData.photo == null" src="/image/portrait-placeholder.png" alt="" class="user-thumb-100">
                        <img v-else :src="teacherData.photo_url" alt="" class="user-thumb-100">
                      </div>
                      <div class="ml-3 pt-2">
                        <h5>{{ teacherData.name }}</h5>
                        <p class="m-0"><strong>School</strong> : {{ teacherData.school.name }}</p>
                        <p class="m-0"><b>Total Rating </b>: &nbsp; <span class="text-warning">{{ totalPoint }}</span></p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h4 class="text-muted">Teacher ratings</h4>
        </div>
        <div class="card-body">
          <div class="row" v-if="isRatingLoading">
            <div class="col-md-12 mb-3" v-for="n in 5" :key="n">
              <skeleton width="100%" height="300px" />
            </div>
          </div>
          <div class="row" v-else>
            <div class="col-md-12 border-top pt-3" v-for="(rate,i) in rates" :key="i">
              <div class="d-flex">
                <div class="text-center">
                  <img src="/image/portrait-placeholder.png" class="user-thumb-40" alt="">
                </div>
                <div class="ml-3">
                  <div class="d-flex justify-content-between">
                    <h6>{{ rate.rater.name }}</h6>
                    <p class="text-muted m-0"><vue-moments-ago class="time" prefix="" suffix="ago" :date="rate.created_at" lang="en"></vue-moments-ago></p>
                  </div>
                  <p class="m-0">{{ rate.feedback }}</p>
                </div>
              </div>
              <div class="row mt-3 justify-content-center">
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate1 }}</span>
                    <p class="text-muted">Performance</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate2 }}</span>
                    <p class="text-muted">Behaviour</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate3 }}</span>
                    <p class="text-muted">Subject knowledge</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate4 }}</span>
                    <p class="text-muted">Attitude</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate5 }}</span>
                    <p class="text-muted">Personality</p>
                  </div>
                </div>
                
                
              </div>
            </div>
            <div class="col-md-12 d-flex justify-content-center mt-2">
              <pagination :data="ratingData" @pagination-change-page="getTeacherRating"></pagination>
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
      totalPoint: 0,
      isRatingLoading: true,
      teacherData: null,
      ratingData: {},
      rates : [],
    }
  },
  methods: {
    getTeacherRating(page = 1) {
      axios.get("/supervisor/api/get-teacher-ratings",{
        params: {
          page: page,
          userId : this.$route.params.userId
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {

          this.teacherData = data.teacherData;
          this.totalPoint = data.totalPoint;
          this.isLoading = false;
          this.ratingData = data.ratings;
          this.rates = data.ratings.data;
          this.isRatingLoading = false;

        }
        else {
          this.$router.push({
            name: 'superv.teacher-list'
          });
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
  },
  mounted() {
    this.getTeacherRating();
  }
}
</script>

<style>

</style>