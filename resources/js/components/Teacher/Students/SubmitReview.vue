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
                    <h6>Average rating points : <strong class="text-warning">{{ monthlyPoint }}</strong></h6>
                </div>

                <form v-if="!reviewFound" @submit.prevent="submitReview" class="row">
                    <div class="col-12 mb-4">
                        <h6>{{ totalReview }} Reviews <i class="fas fa-star text-warning"></i></h6>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-4">
                        <h5>Attendance</h5>
                        <div class="text-center">
                            <h2>{{ form.rate1 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="15" step="1" style="padding: 0 !important" v-model="form.rate1">
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Fetch Syllabus</h5>
                        <div class="text-center">
                            <h2>{{ form.rate2 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="10" step="1" style="padding: 0 !important" v-model="form.rate2">
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Intercation with class</h5>
                        <div class="text-center">
                            <h2>{{ form.rate3 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="15" step="1" style="padding: 0 !important" v-model="form.rate3">
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Homework Solution</h5>
                        <div class="text-center">
                            <h2>{{ form.rate4 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="20" step="1" style="padding: 0 !important" v-model="form.rate4">
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Speed and accuracy of the solution </h5>
                        <div class="text-center">
                            <h2>{{ form.rate5 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="20" step="1" style="padding: 0 !important" v-model="form.rate5">
                    </div>

                    <div class="col-md-12 mb-4">
                        <h5>Test Results</h5>
                        <div class="text-center">
                            <h2>{{ form.rate6 }}</h2>
                        </div>
                        <input type="range" class="form-control" min="1" max="20" step="1" style="padding: 0 !important" v-model="form.rate6">
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
                                <p class="text-muted">Attendance</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate2 }}</span>
                                <p class="text-muted">Fetch Syllabus</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate3 }}</span>
                                <p class="text-muted">Intercation with class</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="rating-thumb">
                                <span>{{ rev.rate4 }}</span>
                                <p class="text-muted">Homework Solution</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                                <div class="rating-thumb">
                                    <span>{{ rev.rate5 }}</span>
                                    <p class="text-muted">Speed and accuracy of the solution</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="rating-thumb">
                                    <span>{{ rev.rate6 }}</span>
                                    <p class="text-muted">Test Results</p>
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
                rate6: 1,
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