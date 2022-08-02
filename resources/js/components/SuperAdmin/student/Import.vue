<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Import students</h4>
                <router-link :to="{name: 'admin.student-list'}" class="btn btn-sm btn-primary"><i class="fas fa-arrow-left"></i> Go back</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="importStudent">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="">Import Student from Excel File</label>
                            <input type="file" class="form-control-file" id="excelFile"
                            accept=".xlsx,.xls" :class="{'is-invalid' : form.errors.has('excel')}" @change="fileChange">
                            <HasError :form="form" field="excel" />
                        </div>
                        <div class="col-12 mb-4">
                            <Button :form="form" class="btn btn-success">Import</Button>
                        </div>
                    </div>
                </form>
                <div class="mt-3 row">
                    <div class="col-12">
                        <p><strong>Instructions</strong></p>
                        <ul>
                            <li>Must upload an excel sheet</li>
                            <li>Please put the proper ID's of school , class and sections or else student records will not add</li>
                            <li>You can find the ID's from the School, Class and Section List page</li>
                            <li>Use the following excel template <a href="/extra/Student-data-import.xlsx">Student-data-import.xlsx</a></li>
                        </ul>
                    </div>
                    <div class="col-12 mt-4" v-if="importLog.status == 'ok'">
                        <p><Strong>Import Log</Strong></p>
                        <hr>
                        <h6 class="text-success"> <i class="fas fa-check-circle"></i> {{ importLog.success }} Success</h6>

                        <template v-if="importLog.errors > 0">
                            <h6 class="text-danger"> <i class="fas fa-times-circle"></i> {{ importLog.errors }} Failed</h6>
                            <hr>
                            <p><b>Failed to add following students</b></p>
                            <div class="" v-html="importLog.errorLog">

                            </div>
                        </template>
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
            form: new Form({
                excel : null,
            }),
            importLog: {},
        }
    },
    methods: {
        async importStudent() {
            await this.form.post("/admin/api/import-student").then(resp=>{
                return resp.data;
            }).then(data=>{
                console.log(data);
                if(data.status == "ok") {
                    this.importLog = data;
                    swal.fire("Import done",data.msg,"success");
                    this.form.reset();
                    $('#excelFile').val("");
                }
                else {

                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        fileChange(e) {
            let file = e.target.files[0];
            if(file) {
                this.form.excel = file;
            }
            else {
                this.form.excel = null;
            }
        }
    }
}
</script>

<style>

</style>