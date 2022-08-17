<template>
  <div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <router-link :to="{name: 'teacher.submit-review', params: {studentId : $route.params.studentId}}" class="btn btn-sm btn-primary"><i class="fas fa-star"></i> Submit Review</router-link>
            </div>
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
                        <p class="m-0"><b>Overall Rating </b>: &nbsp; <span class="text-warning">{{ totalPoint }}</span> ({{ overallReviewCount }} Reviews)</p>
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
          <h4 class="text-muted">Student ratings</h4>
        </div>
        <div class="card-body">
          <form @submit.prevent="filterRatings" class="row mb-3">
            <div class="col-md-3 col-6">
              <select class="form-control" v-model="filterYear">
                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
              </select>
            </div>
            <div class="col-md-3 col-6">
              <select class="form-control" v-model="filterMonth">
                <option value="">None</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>

            <div class="col-md-3 col-6">
              <button class="btn btn-success mt-1" type="submit">Filter</button>
            </div>
          </form>
          <div class="row" v-if="isRatingLoading">
            <div class="col-md-12 mb-3" v-for="n in 5" :key="n">
              <skeleton width="100%" height="300px" />
            </div>
          </div>

          <div class="row" v-if="!isRatingLoading && filterMode">
            <div class="col-12 mt-3">
              <hr>
              <h6 class="text-success">Reviews of - <strong class="text-muted">{{ selectedMonth }}, {{ selectedYear }}</strong> : <span class="text-warning">({{ monthlyRate }})</span></h6>
            </div>
            <div class="col-md-12 border-top pt-3" v-for="(rate,i) in filterRates" :key="i">
              <div class="d-flex justify-content-between mb-1">
                <p class="text-muted"><strong>{{ moment(rate.created_at).format("DD MMMM YYYY") }}</strong></p>
                <p class="text-muted text-end m-0"><vue-moments-ago class="time" prefix="" suffix="ago" :date="rate.created_at" lang="en"></vue-moments-ago></p>
              </div>
              <div class="d-flex">
                <div class="text-center">
                  <img v-if="rate.rater.photo == null" src="/image/portrait-placeholder.png" class="user-thumb-40 border" alt="">
                  <img v-else :src="rate.rater.photo_url" class="user-thumb-40 border" alt="">
                </div>
                <div class="ml-3">
                  <div class="d-flex justify-content-between">
                    <h6>{{ rate.rater.name }}</h6>
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
              <pagination :data="ratingData" @pagination-change-page="getStudentRating"></pagination>
            </div>
          </div>

          <div class="row" v-if="!isRatingLoading && !filterMode">
            <div class="col-md-12 border-top pt-3" v-for="(rate,i) in rates" :key="i">
              <div class="d-flex justify-content-between mb-1">
                <p class="text-muted"><strong>{{ moment(rate.created_at).format("DD MMMM YYYY") }}</strong></p>
                <p class="text-muted text-end m-0"><vue-moments-ago class="time" prefix="" suffix="ago" :date="rate.created_at" lang="en"></vue-moments-ago></p>
              </div>
              <div class="d-flex">
                <div class="text-center">
                  <img v-if="rate.rater.photo == null" src="/image/portrait-placeholder.png" class="user-thumb-40 border" alt="">
                  <img v-else :src="rate.rater.photo_url" class="user-thumb-40 border" alt="">
                </div>
                <div class="ml-3">
                  <div class="d-flex justify-content-between">
                    <h6>{{ rate.rater.name }}</h6>
                  </div>
                  <p class="m-0">{{ rate.feedback }}</p>
                </div>
              </div>
              <div class="row mt-3 justify-content-center">
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate1 }}</span>
                    <p class="text-muted">Attendance</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate2 }}</span>
                    <p class="text-muted">Fetch Syllabus</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate3 }}</span>
                    <p class="text-muted">Intercation with class</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate4 }}</span>
                    <p class="text-muted">Homework Solution</p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="rating-thumb">
                    <span>{{ rate.rate5 }}</span>
                    <p class="text-muted">Speed and accuracy of the solution</p>
                  </div>
                </div>
                <div class="col-md-2">
                    <div class="rating-thumb">
                        <span>{{ rate.rate6 }}</span>
                        <p class="text-muted">Test Results</p>
                    </div>
                </div>
                
                
              </div>
            </div>
            <div class="col-md-12 d-flex justify-content-center mt-2">
              <pagination :data="ratingData" @pagination-change-page="getStudentRating"></pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import VueMomentsAgo from 'vue-moments-ago'
export default {
    components: {
        VueMomentsAgo,    
    },
  data() {
    let start = 2022;
    let end = moment().format("YYYY");
    var years = [];
    while(start <= end)
    {
      years.push(start);
      start++;
    }

    return {
      isLoading: true,
      totalPoint: 0,
      isRatingLoading: true,
      studentData: null,
      ratingData: {},
      rates : [],
      overallReviewCount: 0,
      filterYear: moment().format("YYYY"),
      filterMonth: "",
      years: years,
      filterMode: false,
      filterRates: [],
      selectedMonth: "",
      selectedYear: "",
      monthlyRate: 0,
      moment: moment,
    }
  },
  methods: {
    getStudentRating(page = 1) {
      axios.get("/teacher/api/get-student-ratings",{
        params: {
          page: page,
          studentId : this.$route.params.studentId
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
          this.overallReviewCount = data.totalRatingCount;
          this.isRatingLoading = false;

        }
        else {
          this.$router.push({
            name: 'teacher.student-list'
          });
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    filterRatings() {
      if(this.filterYear!= "" && this.filterMonth != "")
      {

        this.filterMode = true;
        this.isRatingLoading = true;
        axios.get("/teacher/api/get-student-ratings",{
          params: {
            studentId : this.$route.params.studentId,
            year: this.filterYear,
            month: this.filterMonth
          }
        }).then(resp=>{
          return resp.data;
        }).then(data=>{
          this.filterRates = data.review;
          this.selectedMonth = data.selectedMonth;
          this.selectedYear = data.selectedYear;
          this.monthlyRate = data.monthlyPoint;
          this.isRatingLoading = false;
        }).catch(err=>{
          console.error(err.response.data);
        })
      }
      else
      {
        this.filterMode=false;
        this.isRatingLoading = false;
      }
    }
  },
  mounted() {
    this.getStudentRating();
  }
}
</script>

<style>

</style>