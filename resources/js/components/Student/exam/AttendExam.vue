<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>{{ examData.title }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
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
        tries: 0,
        timer: 0,
        isLast: false,
      }),

      attempt: 1,
      stopWatch: null,
      isCorrectNow: false,
      isLast: false,

    }
  },
  methods: {
    attendExam() {
      if(this.selectedQuestionIndex == this.questions.length-1)
      {
        this.answerForm.isLast = true;
      }

      axios.get("/student/api/attend-exam?examId="+this.$route.params.examId).then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        if(data.status == "ok") {
          this.examData = data.examData;
          this.questions = data.questions;
          this.questionSelector(0);
          this.startTimer();
          if(this.answerForm.isLast) {
            this.isLast = true;
          }
          if(this.isLast == true) {
            swal.fire("Exam finished","You have finished your exam. See your report","success");
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
        }
        else
        {
          this.questionSelector(index+1);
        }
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
          toastr.success("Great!",data.msg);
          this.isCorrectNow = true;
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
    }
  },
  mounted() {
    this.attendExam();
  }
}
</script>

<style>

</style>