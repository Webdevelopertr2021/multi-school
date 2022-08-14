<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Leave Requests</h4>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                          <tr class="text-center">
                            <th>Photo</th>
                            <th>Requested By</th>
                            <th>Subject</th>
                            <th>Vacation Type</th>
                            <th>Date Range</th>
                            <th>Total days</th>
                            <th>Application Date</th>
                            <th>Status</th>
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
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLoading: true,
      moment: moment,
    }
  },
  methods : {
    getList() {
      axios.get("/admin/api/get-leave-request").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.paginateData = data.leaves;
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: 'admin.dashboard'
          });
        }
      }).catch(err=>{
        console.error(err.response.data);
      });
    },
    actionModal(data,index) {
      swal.fire({
        title: data.leave.subject,
        html: `
          <hr>
          <div class="col-12 mb-4">
            <p class="text-left">${data.leave.desc}</p>
          </div>
          <hr>
          <div class="col-12 mb-4">
            <label class="text-left"><b>Choose action</b></label>
            <select id="actionStatus" class="form-control">
              <option value="pending" ${data.status=='pending'?'selected':''}>Pending</option>
              <option value="rejected" ${data.status=='rejected'?'selected':''}>Reject</option>
              <option value="approved" ${data.status=='approved'?'selected':''}>Approve</option>
            </select>
          </div>
          <div class="col-12 mb-4">
            <label class="text-left"><b>Write feedback message</b></label>
            <textarea class="form-control" id="actionText" placeholder="Feedback message..."></textarea>
          </div>
          `,
        confirmButtonText: 'Submit',
        focusConfirm: false,
        preConfirm: () => {
          const status = swal.getPopup().querySelector('#actionStatus').value
          const msg = swal.getPopup().querySelector('#actionText').value

          return { status: status, msg: msg }
        }
      }).then((result) => {
        if(result.isConfirmed) {
          axios.post("/admin/api/update-application-status",{
            status: result.value.status,
            msg: result.value.msg,
            id: data.id,
            reqId: data.leave.id,
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            if(data.status == "ok") {
              swal.fire("Data updated",data.msg,"success").then(()=>{
                this.paginateData.data[index].status = data.action;
              });
            }
          }).catch(err=>{
            console.error(err.response.data);
          });
        }
      })
    }
  },
  mounted () {
    var _self = this;
    $("#datatable").DataTable({
      processing: true,
      serverside:true,
      ajax: '/admin/api/get-leave-request',
      lengthChange : true,
      columns: [
        { data: "photo"},
        { data: "request_by" },
        { data: "subject" },
        { data: "vacation_type" },
        { data: "date_range" },
        { data: "total_day" },
        { data: "application_date" },
        { data: "status" },
        // { data: 'action', name: 'action', orderable: false, searchable: false  }
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