<template>
  <div class="row justify-content-center">
    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-header">
          <h4>{{ examData.title }} - Reports</h4>
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

    <div class="col-md-12 mb-2">
      <div class="card">
        <div class="card-body">
          <h6>Total Participent : {{ participent }}</h6>
          <div class="table-responsive mt-5">
            <table id="datatable" class="table table-striped table-bordered">
              <thead>
                <tr class="text-center">
                  <th>Student ID</th>
                  <th>Student Name</th>
                  <th>Total Question <i class="fas fa-question-circle"></i></th>
                  <th>Correctly Answered <i class="fas fa-check-circle text-success"></i></th>
                  <th>Full Mark</th>
                  <th>Obtained Mark</th>
                  <th>Attend Time</th>
                  <th>Submission Time</th>
                  <th>Action</th>
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
      participent: 0,
      isLoading: true,
    }
  },
  methods: {
    async checkExamData() {
        axios.get("/admin/api/check-exam-data?examId="+this.$route.params.examId).then(resp=>{
            return resp.data;
        }).then(data=>{
            if(data.status == "ok") {
              this.examData = data.exam;
              this.participent = data.participent;
              this.isLoading = false;
            } else {
                this.$router.push({
                    name: "admin.exam-list"
                });
            }
        }).catch(err=>{
            this.$router.push({
                name: "admin.exam-list"
            });
            console.error(err.response.data);
        })
    },
  },
  mounted() {
    this.checkExamData();

    var _self = this;
    $('#datatable').DataTable({
      processing: true,
      serverside:true,
      ajax: '/admin/api/get-exam-report?examId='+_self.$route.params.examId,
      lengthChange : true,
      columns: [
        {data: "student_id"},
        { data: "name" },
        { data: 'total_question' },
        { data: 'correct' },
        { data: 'full_mark' },
        { data: 'obtain_mark' },
        { data: 'attend_time' },
        { data: 'submission_time' },
        { data: 'action', name: 'action', orderable: false, searchable: false  }
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

    $(document).on("click","button[data-result]",function(){
      let studentId = $(this).data("student");
      _self.$router.push({
        name: "admin.result-view",
        params: {
          examId: _self.$route.params.examId,
          studentId: studentId
        }
      })
    })
  }
}
</script>

<style>

</style>