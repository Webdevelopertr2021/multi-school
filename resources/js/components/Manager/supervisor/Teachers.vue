<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All Teachers</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Supervisors</th>
                                        <th>School</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Total Students</th>
                                        <th>Ratings ({{ moment().format("MMMM") }})</th>
                                        <th>Stars ({{ moment().format("MMMM") }})</th>
                                        <th>Actions</th>
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
            moment: moment,
        }
    },
    mounted() {
        var _self = this;
        $("#datatable").DataTable({
            processing: true,
            serverside:true,
            ajax: '/manager/api/get-teacher-list-by-supervisor?supervisorId='+_self.$route.params.supervisorId,
            lengthChange : true,
            columns: [
                {data: "id"},
                { data: "name" },
                { data: "phone" },
                { data: "email" },
                { data: "supervisors" },
                { data: "school" },
                { data: "class" },
                { data: "section" },
                { data: "total_students" },
                { data: 'ratings' },
                { data: 'stars' },
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
        });
        $('#datatable').removeClass("dataTable");
        $('#datatable').css("width" , "100%");

        // delete
        $(document).on("click","button[data-delete-teacher]",function(){
            let teacherId = $(this).data("delete-teacher");
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
                axios.post("/admin/api/delete-teacher",{
                    teacherId: teacherId,
                }).then(resp=>{
                    return resp.data;
                }).then(data=>{
                    if(data.status == "ok")
                    {
                    swal.fire("Techer deleted",data.msg,"success").then(()=>{
                        window.location.reload();
                    });
                    }
                }).catch(err=>{
                    toastr.error("Failed to delete","Internal Server error");
                    console.error(err.response.data);
                })
                }
            });//

        });

        $(document).on("click","button[data-edit-teacher]",function(){
            var teacherId = $(this).data("edit-teacher");
            _self.$router.push({
                name: 'admin.teacher-edit',
                params: {
                    teacherId: teacherId
                }
            })
        });

        $(document).on("click","button[data-rate-btn]",function(){
            var teacherId = $(this).data("rate-btn");
            _self.$router.push({
                name: 'manager.teacher-ratings',
                params: {
                    teacherId: teacherId
                }
            })
        });
    }
}
</script>

<style>

</style>