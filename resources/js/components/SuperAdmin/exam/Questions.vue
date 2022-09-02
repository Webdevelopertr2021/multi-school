<template>
  <div class="row justify-content-center">
    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h4>Exam : {{ examData.title }}</h4>
          <button @click="addQuestionMode = true" class="btn btn-sm btn-primary">Make question</button>
        </div>
        <div class="card-body">
          <h6><strong><u>Formla : </u></strong></h6>
          <p class="text-muted">{{ examData.formla }}</p>
          <ul v-if="isLoading">
            <li><skeleton width="200px" height="20px" /></li>
            <li><skeleton width="200px" height="20px" /></li>
            <li><skeleton width="200px" height="20px" /></li>
            <li><skeleton width="200px" height="20px" /></li>
            <li><skeleton width="200px" height="20px" /></li>
          </ul>

          <ul v-else>
            <li>Exam start time : <strong>{{ moment(examData.start_time).format("DD MMM YYYY, h:m A") }}</strong></li>
            <li>Exam end time : <strong>{{ moment(examData.end_time).format("DD MMM YYYY, h:m A") }}</strong></li>
            <li>School : <strong>{{ examData.school.name }}</strong></li>
            <li>Class : <strong>{{ examData.classes.name }}</strong></li>
            <li>Section : <strong>{{ examData.section.name }}</strong></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-12 mb-2" v-if="addQuestionMode">
      <div class="card">
        <div class="card-header">
          <h4>Make question</h4>
        </div>
        <div class="card-body">
          <form class="row" @submit.prevent="addQuestion">
            <div class="col-md-4 mb-4">
              <label for="">Question Body</label>
              <textarea class="form-control" :class="{'is-invalid' : qForm.errors.has('body')}" placeholder="Write your question body here..." v-model="qForm.body"></textarea>
              <HasError :form="qForm" field="body" />
            </div>

            <div class="col-md-4 mb-4">
              <label for="">Mark</label>
              <input type="number" class="form-control" :class="{'is-invalid' : qForm.errors.has('marks')}" placeholder="Marks" v-model="qForm.marks">
              <HasError :form="qForm" field="marks" />
            </div>

            <div class="col-md-4 mb-4">
              <label for="">Correct Answer</label>
              <input type="number" class="form-control" :class="{'is-invalid' : qForm.errors.has('correctAns')}" placeholder="Write your correct answer..." v-model="qForm.correctAns">
              <HasError :form="qForm" field="correctAns" />
            </div>

            <div class="col-md-4 mb-4">
              <label for="">Answer image</label>
              <input type="file" class="form-control-file" @change="fileChange" id="ansFile">
            </div>

            <div class="col-md-1 mb-4">
              <div class="q-card">
                <div class="title">
                  <h6>Preview</h6>
                </div>
                <p>{{ qForm.body }}</p>
              </div>
            </div>

            <div class="col-md-12 mb-4">
              <Button :form="qForm" type="submit" class="btn btn-sm btn-success">Add question</Button>
              <button @click="addQuestionMode = false" type="button" class="btn btn-sm btn-danger">Cancel</button>
            </div>

          </form>
        </div>
      </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Questions | Total : {{ allQuestions.length }}</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-lg-1 col-md-2 col-sm-3 col-6 mb-3" v-for="n in 12" :key="n">
                    <skeleton width="100%" height="300px" />  
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-lg-1 col-md-2 col-sm-3 col-6 mb-3" v-for="(question,i) in allQuestions" :key="i">
                    <div class="q-card">
                      <div class="title">
                        <h6>Q. {{ i+1 }}</h6>
                      </div>
                      <p>{{ question.body }}</p>
                        <div class="footer">
                          <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                          <button @click="removeQuestion(question.id,i)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
      qForm : new Form({
        examId: this.$route.params.examId,
        body: "",
        marks: "",
        correctAns: "",
        ansFile: null,
      }),
      examData: {},
      isLoading: true,
      addQuestionMode: false,
      allQuestions: [],
      moment: moment,
    }
  },
  methods: {
    getExamData() {
      axios.get("/admin/api/get-exam-data?examId="+this.$route.params.examId).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {

          this.examData = data.examData;
          this.allQuestions = data.questions;
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: "admin.exam-list"
          })
        }
      }).catch(err=>{
        console.error(err.response.data);
        this.$router.push({
          name: "admin.exam-list"
        })
      })
    },
    async addQuestion() {
      await this.qForm.post("/admin/api/store-question").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Question added",data.msg,"success");
          this.allQuestions.push(data.question);
          this.qForm.reset();
          $("#ansFile").val("");
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    
    fileChange(e) {
      let file = e.target.files[0];
      if(file) {
        this.qForm.ansFile = file;
      }
      else {
        this.qForm.ansFile = null;
      }
    },

    removeQuestion(id,index) {
      swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post("/admin/api/delete-question",{
            questionId: id,
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            if(data.status == "ok")
            {
              swal.fire("Removed",data.msg,"success");
              this.allQuestions.splice(index,1);
            }
          }).catch(err=>{
            toastr.error("Failed to delete","Internal Server error");
            console.error(err.response.data);
          })
        }
      }); //swal
    }

    
  },

  mounted() {
    this.getExamData();
  }
}
</script>

<style>

</style>