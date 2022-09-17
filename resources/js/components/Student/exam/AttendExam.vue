<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>{{ examData.title }}</h4>
            </div>
            <div class="card-body" v-if="!examDone && !solutionMode && examStatus=='ok'">
              <div class="row" v-if="examData.endTime != ''">
                <div class="col-md-12 text-right">
                  <vac :start-time="moment().format()" :end-time="moment(examData.end_time).format()" @finish="timeUp()">
                    <h6 class="text-warning"
                      slot="process"
                      slot-scope="{ timeObj }">
                        Exam will end in : &nbsp;<span v-if="timeObj.d > 0">{{ timeObj.d }} days</span>
                         <span v-if="timeObj.h > 0">{{ timeObj.h }} hours</span>
                         <span v-if="timeObj.m > 0">{{ timeObj.m }} min</span>
                         <span v-if="timeObj.s > 0">{{ timeObj.s }} sec</span>
                    </h6>
                  </vac>
                </div>
              </div>
              <div class="row">
                <div class="col-md-7 mb-4">
                  <button @click="setQuestion(selectedQuestionIndex-1)" v-if="selectedQuestionIndex > 0" class="btn btn-sm btn-outline-info float-left"><i class="fas fa-arrow-left"></i>Previous</button>
                  <button @click="setQuestion(selectedQuestionIndex+1)" v-if="selectedQuestionIndex <= questions.length" class="btn btn-sm btn-outline-info float-right">Next <i class="fas fa-arrow-right"></i></button>
                </div>
                <div class="col-md-7 mb-4">
                  <form class="row" v-if="selectedQuestion != null" @submit.prevent="submitAnswer">
                    <div class="col-md-12 d-flex justify-content-end">

                      <template v-if="selectedQuestion.status == 'not_submited'"> 
                        <h6 class="text-warning mr-5">Attempt : {{ answerForm.tries }}</h6>
                        <h6>Time : {{ answerForm.timer }}s</h6>
                      </template>

                      <template v-if="selectedQuestion.status=='submited'"> 
                        <h6 class="text-warning mr-5">Attempt : {{ selectedQuestion.total_tries }}</h6>
                        <h6 class="mr-5">Time taken : {{ selectedQuestion.total_time_to_ans }}s</h6>
                        <h6 v-if="selectedQuestion.is_correct=1" class="text-success">Correct <i class="fas fa-check-circle"></i></h6>
                      </template>

                    </div>
                    <div class="col-lg-2 mb-3">
                        <h4 class="text-muted">Question {{ selectedQuestionIndex + 1 }}</h4>
                        <div class="student-question mt-3">
                          <div class="body">{{ selectedQuestion.qstn.body }}</div>
                        </div>
                    </div>
                    <div class="col-lg-7 d-flex align-self-end">
                      <div class="w-100">
                        <div class="form-group">
                          <label for="">Write your answer</label>
                          <input type="text" :class="{'is-invalid' : answerForm.errors.has('nowAns'), 'is-valid' : selectedQuestion.is_correct==1?true:false}" class="form-control" 
                          placeholder="Write your answer here..." v-model="answerForm.nowAns" :disabled="selectedQuestion.status=='submited'?true:false">
                          <HasError :form="answerForm" field="nowAns" />
                        </div>
                        <div class="form-group text-right" v-if="selectedQuestion.status=='not_submited' && !isCorrectNow">
                          <Button :form="answerForm" class="btn btn-primary" type="submit">{{ selectedQuestionIndex == questions.length-1?'Finish':'Submit Answer' }} <i class="fas fa-arrow-right"></i></Button>
                        </div>
                        <div class="form-group text-right" v-if="isCorrectNow">
                          <button @click="nextQuestion" class="btn btn-warning" type="button">Next Question<i class="fas fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12 mt-5" v-if="isCorrectNow">
                      <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Well done!</h4>
                        <p class="text-white">Your answer was correct. Go to next question</p>
                        <hr>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="col-md-5 mb-4">
                  <div class="teacher-img">
                    <img src="/image/teacher.png" alt="">
                  </div>
                  <div class="text-center">
                    <h4>Question will appear one by one.</h4>
                    <p class="text-success">Keep answering. Your time and number of tries will be count for each question</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body" v-if="examDone">
              <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                  <h3 class="text-success">You have answered all the question</h3>
                  <img class="congo mt-3" src="/image/congrats.gif" alt="">
                  <div class="mt-3">
                    <button @click="seeSolution()" class="btn btn-warning">See Solution <i class="fas fa-th"></i></button>
                    <button @click="tryAgain()" class="btn btn-primary ml-3">Try again <i class="fas fa-circle-notch"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body" v-if="solutionMode">
              <div class="row">
                <div class="col-12 text-center mb-4">
                  <h3 class="text-warning">Solution for the wrong answers</h3>
                </div>
              </div>
              <div class="row">

                <template v-for="(q,i) in questions">
                  <div class="col-6 col-md-3 col-lg-2" :key="i" v-if="q.status=='not_submited'">
                    <div class="pricing pricing-highlight">
                      <div class="pricing-title">
                        Question {{ i+1 }}
                      </div>
                      <div class="pricing-padding">
                        <div class="pricing-details d-block">
                          <div class="pricing-item">
                            <div class="student-question">
                              <div class="body">{{ q.qstn.body }}</div>
                            </div>
                          </div>
                        </div>

                        <div class="pricing-price mb-0 mt-4">
                          <h6>Your Answer : </h6>
                          <h2 class="text-danger">{{ q.correct_ans }} <i style="font-size: 25px;" class="fas fa-times-circle"></i></h2>
                        </div>
                        <hr>
                        <div class="pricing-price mb-0 mt-4">
                          <h6>Correct Answer is</h6>
                          <h2 class="text-success">{{ q.correct_ans }} <i style="font-size: 25px;" class="fas fa-check-circle"></i></h2>
                        </div>
                        
                      </div>
                      <div class="pricing-cta mt-0">
                        <a href="#">Way to solve <i class="fas fa-arrow-right"></i></a>
                      </div>
                    </div>
                  </div>
                </template>

              </div>
              <div class="row mt-5">
                <div class="col-md-12 text-center">
                  <button @click="tryAgain()" class="btn btn-success">Try Again <i class="fas fa-circle-notch"></i></button>
                </div>
              </div>
            </div>
            <div class="card-body" v-if="examStatus=='time_up'">
              <div class="row">
                <div class="col-md-12 text-center">
                  <h3>Exam is over</h3>
                  <button class="btn btn-success mt-5">See your Report <i class="fas fa-file"></i></button>
                </div>
              </div>
            </div>
            <div class="card-body" v-if="examStatus == 'not_started'">
              <div class="row justify-content-center">
                <div class="col-md-6 text-center pb-5 pt-5">
                  <vac :start-time="moment().format()" :end-time="moment(examData.start_time).format()" @finish="startExam()">
                    <h3
                      slot="process"
                      slot-scope="{ timeObj }">
                        Exam Starts in : &nbsp;<span v-if="timeObj.d > 0">{{ timeObj.d }} days</span>
                         <span v-if="timeObj.h > 0">{{ timeObj.h }} hours</span>
                         <span v-if="timeObj.m > 0">{{ timeObj.m }} min</span>
                         <span v-if="timeObj.s > 0">{{ timeObj.s }} sec</span>
                    </h3>
                  </vac>
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
      examData: {},
      questions: [],

      selectedQuestion: null,
      selectedQuestionIndex: null,

      answerForm: new Form({
        answerId: "",
        nowAns: "",
        tries: 1,
        timer: 0,
        isLast: false,
      }),

      attempt: 1,
      stopWatch: null,
      isCorrectNow: false,
      isLast: false,
      examDone: false,
      solutionMode: false,
      examStatus: "",
    }
  },
  methods: {
    attendExam() {
      axios.get("/student/api/attend-exam?examId="+this.$route.params.examId).then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        if(data.status == "ok") {
          this.examData = data.examData;
          this.examStatus = data.examStatus;
          if(data.examStatus == "ok") {
            this.questions = data.questions;
            this.questionSelector(0);
            this.startTimer();
          }
          
        }
        else{
          toastr.error("Failed",data.msg);
          this.$router.push({
            name: "student.upcoming-exam"
          });
        }


      }).catch(err=>{
        this.$router.push({
          name: "student.upcoming-exam"
        });
        console.error(err.response.data);
      })
    },
    questionSelector(index) {
      if(index > this.questions.length-1)
      {
        this.selectedQuestion = this.questions[0];
        this.selectedQuestionIndex = 0;
        this.answerForm.timer = this.selectedQuestion.total_time_to_ans;
        this.answerForm.tries = this.selectedQuestion.total_tries;
        this.answerForm.nowAns = this.selectedQuestion.answer;
        swal.fire("INFO","You've answered all the question",'info');
      }
      else
      {
        if(this.questions[index].status == "not_submited")
        {
          this.selectedQuestion = this.questions[index];
          this.selectedQuestionIndex = index;
          this.answerForm.timer = this.selectedQuestion.total_time_to_ans;
          this.answerForm.tries = this.selectedQuestion.total_tries;
          this.answerForm.nowAns = this.selectedQuestion.answer;

          if(this.selectedQuestionIndex == this.questions.length - 1) {
            this.answerForm.isLast = true;
            this.isLast = true;
          }
        }
        else
        {
          this.questionSelector(index+1);
        }
      }
    },
    setQuestion(index) {
      if(index > this.questions.length-1) {
        this.examDone = true;
      }
      else {
        this.selectedQuestion = this.questions[index];
        this.selectedQuestionIndex = index;
        this.answerForm.timer = this.selectedQuestion.total_time_to_ans;
        this.answerForm.tries = this.selectedQuestion.total_tries;
        this.answerForm.nowAns = this.selectedQuestion.answer;
      }
    },
    startTimer() {
      this.stopWatch = setInterval(()=>{
        this.answerForm.timer += 1;
      },1000);
    },
    submitAnswer() {
      this.answerForm.answerId = this.selectedQuestion.id;

      this.answerForm.post("/student/api/submit-answer").then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        if(data.status == "correct") {
          this.questions[this.selectedQuestionIndex].status = 'submited';
          toastr.success("Great!",data.msg);
          this.isCorrectNow = true;
          if(this.isLast == true) {
            this.examDone = true;
            swal.fire("Exam finished","You have completed the exam","success");
          }
        }
        else if(data.status == "incorrect") {
          this.answerForm.tries += 1;
          toastr.error("X",data.msg);
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    nextQuestion() {
      this.isCorrectNow = false;
      this.questionSelector(this.selectedQuestionIndex+1);
    },
    tryAgain() {
      this.solutionMode = false;
      this.examDone = false;
      this.questionSelector(0);
    },
    timeUp() {
      this.examStatus = "time_up";
    },
    seeSolution() {
      this.examDone = false;
      this.solutionMode = true;
    },
    startExam() {
      this.attendExam();
    }
  },
  mounted() {
    this.attendExam();
  }
}
</script>

<style>

</style>