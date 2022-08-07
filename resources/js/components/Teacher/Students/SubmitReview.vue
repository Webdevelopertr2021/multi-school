<template>
  <div class="row justify-content-center">
    <div class="col-md-7 mb-4">
        <router-link class="btn btn-sm btn-primary" :to="{name: 'teacher.student-ratings',params: {studentId: $route.params.studentId}}"><i class="fas fa-arrow-left"></i> Go back</router-link>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-start">
                <template v-if="isLoading">
                    <skeleton width="40px" height="40px" class="user-thumb-40" />
                    <skeleton class="ml-2" width="200px" height="20px"/>
                </template>
                <template v-else>
                    <img v-if="userData.photo != null" :src="userData.photo_url" alt="" class="user-thumb-40">
                    <img v-else src="/image/portrait-placeholder.png" alt="" class="user-thumb-40">
                    <h4 class="ml-3">{{ userData.name }}</h4>
                    <span>(My feedback)</span>
                </template>

            </div>
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <h6 class="mb-3">Current month : <strong class="text-muted">{{ moment().format("MMMM") }}</strong></h6>
                    <h6>This month rating points : <strong class="text-warning">{{ monthlyPoint }}</strong></h6>
                </div>

                <form v-if="!reviewFound" @submit.prevent="submitReview" class="row">
                    <div class="col-12 mb-4">
                        <h6>{{ totalReview }} Reviews <i class="fas fa-star text-warning"></i></h6>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-4">
                        <h5>Performance</h5>
                        <div class="selectgroup selectgroup-pills mt-4">
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="1" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">1</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="2" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">2</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="3" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">3</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="4" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">4</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="5" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">5</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="6" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">6</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="7" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">7</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="8" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">8</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="9" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">9</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate1" value="10" class="selectgroup-input" v-model="form.rate1">
                                <span class="selectgroup-button selectgroup-button-icon">10</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Behaviour</h5>
                        <div class="selectgroup selectgroup-pills mt-4">
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="1" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">1</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="2" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">2</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="3" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">3</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="4" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">4</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="5" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">5</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="6" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">6</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="7" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">7</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="8" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">8</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="9" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">9</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate2" value="10" class="selectgroup-input" v-model="form.rate2">
                                <span class="selectgroup-button selectgroup-button-icon">10</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Subject Knowledge</h5>
                        <div class="selectgroup selectgroup-pills mt-4">
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="1" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">1</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="2" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">2</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="3" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">3</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="4" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">4</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="5" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">5</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="6" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">6</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="7" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">7</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="8" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">8</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="9" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">9</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate3" value="10" class="selectgroup-input" v-model="form.rate3">
                                <span class="selectgroup-button selectgroup-button-icon">10</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Attitude</h5>
                        <div class="selectgroup selectgroup-pills mt-4">
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="1" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">1</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="2" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">2</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="3" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">3</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="4" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">4</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="5" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">5</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="6" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">6</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="7" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">7</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="8" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">8</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="9" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">9</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate4" value="10" class="selectgroup-input" v-model="form.rate4">
                                <span class="selectgroup-button selectgroup-button-icon">10</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Personality</h5>
                        <div class="selectgroup selectgroup-pills mt-4">
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="1" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">1</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="2" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">2</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="3" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">3</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="4" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">4</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="5" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">5</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="6" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">6</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="7" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">7</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="8" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">8</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="9" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">9</span>
                            </label>
                            <label class="selectgroup-item">
                                <input type="radio" name="rate5" value="10" class="selectgroup-input" v-model="form.rate5">
                                <span class="selectgroup-button selectgroup-button-icon">10</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for=""><b>Write your feedback</b></label>
                        <textarea class="form-control" placeholder="Feedback..." v-model="form.feedback"></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <Button :form="form" class="btn btn-primary">Submit</Button>
                    </div>

                </form>

                <div class="row">
                    <div class="col-md-12 border-top pt-3" v-for="(rev,i) in review" :key="i">
                        <p class="text-end text-muted"><strong>{{ moment(review.created_at).format("DD MMMM YYYY") }}</strong></p>
                        <div class="mb-4 text-center">
                            <label for=""><b>Feedback</b></label>
                            <p>{{ rev.feedback }}</p>
                        </div>
                        <p class="mb-3 text-center"></p>
                        <div class="row mb-5 mb-4 justify-content-center">
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate1 }}</span>
                                <p class="text-muted">Performance</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate2 }}</span>
                                <p class="text-muted">Behaviour</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate3 }}</span>
                                <p class="text-muted">Subject knowledge</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate4 }}</span>
                                <p class="text-muted">Attitude</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate5 }}</span>
                                <p class="text-muted">Personality</p>
                            </div>
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
            userData: {},
            reviewFound: false,
            form: new Form({
                rate1: 1,
                rate2: 1,
                rate3: 1,
                rate4: 1,
                rate5: 1,
                studentId: this.$route.params.studentId,
                feedback: "",
            }),
            totalReview: 0,
            monthlyPoint: 0,
            review: [],
        }
    },
    methods: {
        checkStudentReview() {
            axios.get("/teacher/api/check-student-review",{
                params: {
                    studentId: this.$route.params.studentId,
                }
            }).then(resp=>{
                return resp.data;
            }).then(data=>{
                console.log(data);
                if(data.status == "ok"){
                    this.userData = data.student;
                    this.reviewData = data.review;
                    this.reviewFound = data.reviewFound;
                    this.totalReview = data.totalReview;
                    this.monthlyPoint = data.monthlyPoint;
                    this.review = data.review;
                    this.isLoading = false;
                }
                else {
                    this.$router.push({
                        name: 'teacher.student-ratings',
                        params: {
                            studentId: this.$route.params.studentId
                        }
                    });
                }
            }).catch(err=>{
                this.$router.push({
                    name: 'teacher.student-ratings',
                    params: {
                        studentId: this.$route.params.studentId
                    }
                });
                console.error(err.response.data);
            })
        },
        submitReview() {
            this.form.post("/teacher/api/submit-review").then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    swal.fire("Success",data.msg,"success").then(()=>{
                        window.location.reload();
                    });
                    
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        }
    },
    mounted() {
        this.checkStudentReview();
    }
}
</script>

<style>

</style>