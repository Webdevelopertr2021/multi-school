<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4></h4>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                          <tr class="text-center">
                            <th>ID</th>
                            <th>Student name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>School</th>
                            <th>Class</th>
                            <th>Section</th>
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
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {

    }
  },
  mounted() {
    var _self = this;
    $('#datatable').DataTable({
      processing: true,
      serverside:true,
      ajax: '/admin/api/get-student-list',
      lengthChange : true,
      columns: [
        {data: "id"},
        { data: "name" },
        { data: "phone" },
        { data: "email" },
        { data: "address" },
        { data: "school" },
        { data: "class" },
        { data: "section" },
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

    $(document).on("click","button[data-student-delete]",function(){
      let studentId = $(this).data("student-delete");
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
          axios.post("/admin/api/delete-student",{
            studentId: studentId,
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            if(data.status == "ok")
            {
              swal.fire("Student deleted",data.msg,"success").then(()=>{
                window.location.reload();
              });
            }
          }).catch(err=>{
            toastr.error("Failed to delete","Internal Server error");
            console.error(err.response.data);
          })
        }
      })
    });

    $(document).on("click","button[data-student-edit]",function(){
      let studentId = $(this).data('student-edit');
      _self.$router.push({
        name: 'admin.edit-student',
        params: {
          studentId : studentId,
        }
      });

    });
  }
}
</script>

<style>

</style>