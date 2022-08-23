<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Payment List</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th>Payment Date</th>
                                        <th>Paid Month</th>
                                        <th>Paid To</th>
                                        <th>Amount</th>
                                        <th>Bank Name</th>
                                        <th>Reciept Nummber</th>
                                        <th>Attachment</th>
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

    }
  },
  methods : {

  },
  mounted() {
    var _self = this;
        $("#datatable").DataTable({
            processing: true,
            serverside:true,
            ajax: '/admin/api/payment-list',
            lengthChange : true,
            columns: [
                {data: "date"},
                {data: "paid_month"},
                {data: "paid_to"},
                {data: "amount"},
                {data: "bank_name"},
                {data: "number"},
                {data: "attachment"},
                {data: "status"},
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
  }
}
</script>

<style>

</style>