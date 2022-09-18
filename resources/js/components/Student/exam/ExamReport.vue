<template>
  <div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My Exams</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-md-12 mb-2" v-for="n in 10" :key="n">
                    <skeleton width="100%" height="30px"/>
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table table-hover table-striped">
                        <thead>
                          <tr class="text-center">
                            <th>Exam Title</th>
                            <th>Exam Date</th>
                            <th>Total Questions</th>
                            <th>Total Marks</th>
                            <th>My Score</th>
                            <th>Correct Answers</th>
                            <th>Wrong Answers</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(exam,i) in exams" :key="i" class="text-center">
                            <td><strong>{{  exam.exam.title  }}</strong></td>
                            <td>{{ moment(exam.exam.start_time).format("DD MMM YYYY , h:m A") }}</td>
                            <td>{{ exam.question_count }}</td>
                            <td>{{ exam.total_marks }}</td>
                            <td>{{ exam.my_marks }}</td>
                            <td class="text-success"><strong>{{ exam.correct_ans }}</strong></td>
                            <td class="text-danger">{{ exam.wrong_ans }}</td>
                            <td>
                              <router-link class="btn btn-sm btn-primary" :to="{name: 'student.result-view', params: {examId: exam.exam_id}}">See Report</router-link>
                            </td>
                          </tr>
                        </tbody>
                      </table>
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
      exams: [],
      moment: moment,
    }
  },
  methods: {
    async getExams() {
      axios.get("/student/api/get-my-exams").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.exams = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getExams();
  }
}
</script>

<style>

</style>