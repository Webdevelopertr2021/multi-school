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
          <h4 class="text-muted">Teacher ratings</h4>
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
              <h6 class="text-success">Review in month : <strong class="text-muted">{{ selectedMonth }}</strong> <span class="text-warning">({{ monthlyRate }})</span></h6>
              <!-- Stars -->
              <rating-star :star="filteredStar"></rating-star>
              <!-- end -->
              <p><strong>{{ filteredStar }} <i class="fas fa-star"></i></strong></p>
            </div>
            <div class="col-md-12 border-top pt-3" v-for="(rate,i) in filteredReview" :key="i">
              <div class="d-flex justify-content-between mb-1">
                <p class="text-muted"><strong>{{ moment(rate.created_at).format("DD MMMM YYYY") }}</strong></p>
                <p class="text-muted text-end m-0"><vue-moments-ago class="time" prefix="" suffix="ago" :date="rate.created_at" lang="en"></vue-moments-ago></p>
              </div>
              <div class="d-flex">
                <div class="text-center">
                  <img v-if="rate.photo == null" src="/image/portrait-placeholder.png" class="user-thumb-40" alt="">
                  <img v-else :src="rate.photo_url" class="user-thumb-40" alt="">
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
          </div>

          <div class="row" v-if="!isRatingLoading && !filterMode">
            <div class="col-md-12 border-top pt-3" v-for="(rate,i) in rates" :key="i">
              <div class="d-flex justify-content-between mb-1">
                <p class="text-muted"><strong>{{ moment(rate.created_at).format("DD MMMM YYYY") }}</strong></p>
                <p class="text-muted text-end m-0"><vue-moments-ago class="time" prefix="" suffix="ago" :date="rate.created_at" lang="en"></vue-moments-ago></p>
              </div>
              <div class="d-flex">
                <div class="text-center">
                  <img v-if="rate.photo == null" src="/image/portrait-placeholder.png" class="user-thumb-40" alt="">
                  <img v-else :src="rate.photo_url" class="user-thumb-40" alt="">
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
              <pagination :data="ratingData" @pagination-change-page="getTeacherRating"></pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueMomentsAgo from 'vue-moments-ago'
import RatingStar from "../../Partials/Stars.vue";

export default {
  components: {
    VueMomentsAgo,
    "rating-star" : RatingStar,
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
      teacherData: null,
      ratingData: {},
      rates : [],
      filterMode: false,
      filteredReview: [],
      filterYear: moment().format("YYYY"),
      filterMonth: "",
      filteredStar: 0,
      years: years,
      monthlyRate: 0,
      selectedMonth: "",
      overallReviewCount: 0,
    }
  },
  methods: {
    getTeacherRating(page = 1) {
      axios.get("/manager/api/get-teacher-ratings",{
        params: {
          page: page,
          userId : this.$route.params.teacherId
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
          this.overallReviewCount = data.reviewCount;
          this.isRatingLoading = false;

        }
        else {
          this.$router.push({
            name: 'admin.all-teacher'
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
        axios.get("/manager/api/get-teacher-ratings",{
          params: {
            userId : this.$route.params.teacherId,
            year: this.filterYear,
            month: this.filterMonth
          }
        }).then(resp=>{
          return resp.data;
        }).then(data=>{
          console.log(data);
          if(data.status == "ok") {
            this.filteredReview = data.ratings;
            this.monthlyRate = data.monthlyRate;
            this.selectedMonth = data.month;
            this.filteredStar = data.star;
            this.isRatingLoading = false;
          }
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
    this.getTeacherRating();
  }
}
</script>