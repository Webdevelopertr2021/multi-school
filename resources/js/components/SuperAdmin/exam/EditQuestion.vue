<template>
  <div class="row justify-content-center">
    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h4>Edit question</h4>
          <button @click="$router.go(-1)" class="btn btn-sm btn-primary"><i class="fas fa-arrow-left"></i> Go back</button>
        </div>
        <div class="card-body">
          <form class="row" @submit.prevent="updateQuestion">
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
              <Button :form="qForm" type="submit" class="btn btn-sm btn-success">Update</Button>
            </div>

          </form>
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
        qstnId: this.$route.params.qstnId,
        body: "",
        marks: "",
        correctAns: "",
        ansFile: null,
      }),
    }
  },
  methods : {
    getQuestionData() {
      axios.get("/admin/api/get-question-data?qstnId="+this.$route.params.qstnId).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.qForm.body = data.qstn.body;
          this.qForm.marks = data.qstn.marks;
          this.qForm.correctAns = data.qstn.correct_ans;
        }
        else {
          this.$router.go(-1);
        }
      }).catch(err=>{
        this.$router.go(-1);
        console.error(err.response.data);
      })
    },
    async updateQuestion() {
      await this.qForm.post("/admin/api/update-question").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Question updated",data.msg,"success");
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
  },
  mounted() {
    this.getQuestionData();
  }
}
</script>

<style>

</style>