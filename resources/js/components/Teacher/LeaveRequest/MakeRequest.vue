<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Make new leave request</h4>
                <router-link :to="{name: 'teacher.leave-list'}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Go back</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm" class="row">
                    <div class="col-md-6 mb-4">
                        <label for="">Leave from</label>
                        <input type="date" class="form-control" :class="{'is-invalid':form.errors.has('fromDate')}" v-model="form.fromDate">
                        <HasError :form="form" field="fromDate" />
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="">Number of days</label>
                        <input type="number" class="form-control" :class="{'is-invalid':form.errors.has('toDate')}" v-model="form.toDate">
                        <HasError :form="form" field="toDate" />
                    </div>
                    <div class="col-md-12 mb-4">
                        <label for="">Leave Subject</label>
                        <input type="text" class="form-control" :class="{'is-invalid':form.errors.has('subject')}" placeholder="Subject" v-model="form.subject">
                        <HasError :form="form" field="subject" />
                    </div>
                    <div class="col-md-12 mb-4">
                        <label for="">Vacation Type</label>
                        <select class="form-control" :class="{'is-invalid':form.errors.has('reason')}" v-model="form.reason">
                            <option value="" disabled hidden>Select vacation type</option>
                            <option value="Salary Clinic">Salary Clinic</option>
                            <option value="Ordinary without salary">Ordinary without salary</option>
                            <option value="1 Hour">1 Hour</option>
                            <option value="Time Satisfying">Time Satisfying</option>
                        </select>
                        <HasError :form="form" field="reason" />
                    </div>
                    <div class="col-md-12 mb-4">
                        <label for="">Description</label>
                        <textarea class="form-control" :class="{'is-invalid':form.errors.has('desc')}" placeholder="Write your description..." v-model="form.desc"></textarea>
                        <HasError :form="form" field="desc" />
                    </div>
                    <div class="col-md-12 mb-4">
                        <Button :form="form" class="btn btn-success">Submit</Button>
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
            form: new Form({
                fromDate: "",
                toDate: 1,
                subject: "",
                desc: "",
                reason: "",
            }),
        }
    },
    methods: {
        submitForm() {
            this.form.post("/teacher/api/submit-leave-request").then(resp=>{
                return resp.data;
            }).then(data=>{
                console.log(data);
                if(data.status == "ok") {
                    swal.fire("Request submitted",data.msg,"success").then(()=>{
                        this.$router.push({
                            name: 'teacher.leave-list'
                        })
                    });
                }
                else {
                    swal.fire("Can not submit this request",data.msg,"warning");
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        }
    }
}
</script>

<style>

</style>