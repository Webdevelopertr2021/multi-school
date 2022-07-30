<template>
  <div class="row justify-content-center">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Edit Section</h4>
                <router-link class="btn btn-sm btn-primary" :to="{name: 'admin.create-section'}"><i class="fas fa-arrow-left"></i> Go back</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="updateSection" class="row justify-content-center">
                    <div class="col-md-7">
                        <h6>Add section</h6>
                        <hr>
                    </div>
                    
                    <div class="col-md-7 mb-4">
                        <label for="">Select School</label>
                        <multiselect @input="getClassList" :class="{'is-invalid': addForm.errors.has('schoolId')}" v-model="addForm.school" :options="schools" 
                        :preserve-search="true" placeholder="Select school" label="name" track-by="id"></multiselect>
                        <HasError  :form="addForm" field="schoolId"/>
                    </div>

                    <div class="col-md-7 mb-4">
                        <label for="">Select Class</label>
                        <multiselect :class="{'is-invalid': addForm.errors.has('classId')}" v-model="addForm.class" :options="classes" 
                        :preserve-search="true" placeholder="Select class" label="name" track-by="id" :loading="isClassLoading"></multiselect>
                        <HasError  :form="addForm" field="classId"/>
                    </div>

                    <div class="col-md-7 mb-4">
                        <label for="">Section name</label>
                        <input type="text" class="form-control" placeholder="Section name..." v-model="addForm.name" :class="{'is-invalid' : addForm.errors.has('name')}">
                        <HasError  :form="addForm" field="name"/>
                    </div>
                    
                    <div class="col-md-7 mb-4">
                        <Button :form="addForm" type="submit" class="btn btn-success">Update</Button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
    components: {
        "multiselect" : Multiselect,
    },
    data() {
        return {
            isClassLoading: false,
            classes: [],
            schools: [],
            sections: [],
            addForm: new Form({
                sectionId: this.$route.params.sectionId,
                school: null,
                name: '',
                class: null,
                schoolId: null,
                classId: null,
            }),
            paginateData: {},
        }
    },
    methods: {
        getSectionData() {
            axios.get("/admin/api/get-section-data",{
                params: {
                    sectionId : this.$route.params.sectionId
                }
            }).then(resp=>{
                return resp.data;
            }).then(data=>{
                console.log(data);
                if(data.status == "ok") {
                    this.addForm.school = data.section.school;
                    this.addForm.class = data.section.classes;
                    this.addForm.name  = data.section.name;
                    this.addForm.schoolId = data.section.school_id;
                    this.addForm.classId = data.section.class_id;
                }
                else {
                    this.$router.push({
                        name: "admin.create-section"
                    })
                }
            }).catch(err=>{
                this.$router.push({
                    name: "admin.create-section"
                })
                console.error(err.response.data);
            })
        },
        getSchools() {
            axios.get("/admin/api/get-all-schools").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.schools = data;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        updateSection() {

            if(this.addForm.school != null)
            {
                this.addForm.schoolId = this.addForm.school.id;
            }
            if(this.addForm.class != null) 
            {
                this.addForm.classId = this.addForm.class.id;
            }

            this.addForm.post('/admin/api/update-section').then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    swal.fire("Section updated",data.msg,"success");
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        getClassList() {
            this.classes = [];
            this.addForm.class = null;
            this.addForm.classId = null;
            this.isClassLoading = true;
            axios.get("/admin/api/get-class-list?schoolId="+this.addForm.school.id).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.classes = data;
                this.isClassLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
    },
    mounted() {
        this.getSectionData();
        this.getSchools();
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
.multiselect__option--highlight {
    background: #6170df !important;
}
.multiselect__option--highlight::after{
  background: #6170df !important;
}
.multiselect__tag{
  background: #6170df !important;
}
.multiselect__option--selected{
  background-color: #e58787 !important;
}
.multiselect__option--selected::after{
  background-color: #e58787 !important;
}
.is-invalid{
  border: 1px solid red;
}
</style>