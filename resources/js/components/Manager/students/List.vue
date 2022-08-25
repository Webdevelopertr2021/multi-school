<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All Students</h4>
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
                            <th>Rating ({{ moment().format("MMMM") }})</th>
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
      ajax: '/manager/api/get-my-students',
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
        { data: 'ratings'},
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

    $(document).on("click","button[data-ratings]",function(){
      let studentId = $(this).data("ratings");
      _self.$router.push({
        name: 'manager.student-rating',
        params: {
          studentId : studentId
        }
      })

    });

  }
}
</script>

<style>

</style>