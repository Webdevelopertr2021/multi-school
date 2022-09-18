<template>
  <div class="row justify-content-center">
    
    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-body">
          <div class="mb-4" v-if="!isLoading">
            <h6>{{ examData.students.name }} - ({{ examData.exam.title }}) Report</h6>
            <p class="text-success"><strong>Score : {{ totalMark }}</strong></p>
          </div>
          <div class="table-responsive mt-5">
            <table id="datatable" class="table table-bordered">
              <thead>
                <tr class="text-center">
                  <th></th>
                  <th>Answer</th>
                  <th>Status</th>
                  <th>Actual Answer</th>
                  <th>Answer Submited</th>
                  <th>Mark</th>
                  <th>Total Tries</th>
                  <th>Total Time (second)</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
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
      isLoading: true,
      totalMark: 0,
    }
  },
  methods: {
    getExamResultInfo() {
      axios.get(`/student/api/get-result?exam=${this.$route.params.examId}`).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.examData = data.exam;
          this.totalMark = data.totalMark;
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: "admin.exam-report",
            params: {
              examId: this.$route.params.examId
            }
          });
        }
      }).catch(err=>{
        this.$router.push({
          name: "admin.exam-report",
          params: {
            examId: this.$route.params.examId
          }
        });
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getExamResultInfo();

    var _self = this;
    $('#datatable').DataTable({
      processing: true,
      serverside:true,
      ajax: '/student/api/get-exam-result-datatable?examId='+_self.$route.params.examId,
      lengthChange : true,
      columns: [
        { data: "question" },
        { data: "answer" },
        {data: "status"},
        {data: 'correct'},
        {data: 'is_submit'},
        {data: 'mark'},
        {data:'total_tries'},
        {data: 'total_time'}
      ],
      dom: 'Bfrtip',
      buttons: [
          {
              extend: 'copyHtml5',
              exportOptions: {
                  columns: [ 0, ':visible' ]
              }
          },
          {
              extend: 'excelHtml5',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdfHtml5',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
            extend: 'colvis',
            text: '<i class="fas fa-eye"></i> Columns'
          }
      ],
      createdRow: function( row, data, dataIndex ) {
          // Set the data-status attribute, and add a class
          $( row ).addClass("text-center");
      }

    })
    $('#datatable').removeClass("dataTable");
    $('#datatable').css("width" , "100%");
  }
}
</script>

<style>

</style>