<template>
  <div class="row">
    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-body">
          <h4 class="text-center"><i class="fas fa-clock"></i> Upcoming Exams</h4>
        </div>
      </div>
    </div>

    <template v-if="isLoading">
      <div class="col-12 col-md-6 col-lg-4">
        <skeleton height="300px" />
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <skeleton height="300px" />
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <skeleton height="300px" />
      </div>

    </template>

    <template v-else>
      <div class="col-12 col-md-6 col-lg-4" v-for="(exam,i) in exams" :key="i">
        <div class="card card-warning">
          <div class="card-header d-flex justify-content-between">
            <h4>{{ exam.title }}</h4>
            <h6>Starts - {{ exam.start_text }}</h6>
          </div>
          <div class="card-body">
            <ul>
              <li class="text-muted">Total question : <strong>{{ exam.question_count }}</strong></li>
              <li class="text-muted">Total Time : <strong>{{ exam.total_time }}</strong></li>
              <li class="text-muted">Total Marks : <strong>{{ exam.total_marks }} Points</strong></li>
              <li class="text-muted">Exam By : <strong>{{ exam.created_by }}</strong></li>
            </ul>
          </div>
          <div class="card-footer text-right">
            <router-link :to="{name: 'student.attend', params: {examId: exam.id}}" class="btn btn-sm btn-primary">Attend Exam <i class="fas fa-arrow-right"></i></router-link>
          </div>
        </div>
      </div>
    </template>

  </div>
</template>

<script>
export default {
  data() {
    return {
      exams: [],
      isLoading: false,
    }
  },
  methods: {
    getUpcomingExam() {
      axios.get("/student/api/get-upcoming-exams").then(resp=>{
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
    this.getUpcomingExam();
  }
}
</script>

<style>

</style>