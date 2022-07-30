<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Add new school</h4>
                <router-link :to="{name : 'admin.school-list'}" class="btn btn-primary"><i class="fas fa-list"></i> School List</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="addSchool" class="row">

                    <div class="col-md-12 form-group">
                        <label for="">School Name</label>
                        <input type="text" class="form-control" placeholder="School name" v-model="form.name" :class="{'is-invalid':form.errors.has('name')}">
                        <HasError :form="form" field="name" />
                    </div>

                    <div class="col-md-12 form-group">
                        <label for="">School Address</label>
                        <textarea type="text" class="form-control" placeholder="School address" 
                        v-model="form.address" :class="{'is-invalid':form.errors.has('address')}"></textarea>
                        <HasError :form="form" field="address" />
                    </div>

                    <div class="col-md-12 form-group">
                        <label for="">School Code</label>
                        <input type="text" class="form-control" placeholder="School code" v-model="form.schoolCode" :class="{'is-invalid':form.errors.has('schoolCode')}">
                        <HasError :form="form" field="schoolCode" />
                    </div>

                    <div class="col-md-12 form-group">
                        <Button class="btn btn-success" :form="form" type="submit">Add</Button>
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
                name: "",
                address : "",
                schoolCode: "",
            }),
        }
    },
    methods : {
        addSchool() {
            this.form.post("/admin/api/store-school").then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    swal.fire("School created",data.msg,"success");
                    this.form.reset();
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