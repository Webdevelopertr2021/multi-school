<template>
  <div class="row justify-content-center">

    <div class="col-md-12" v-if="mode == 'add'">
        <div class="card">
            <div class="card-header">
                <h4>Add new Section</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="addSection" class="row justify-content-center">
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
                        <multiselect @input="getClassList" :class="{'is-invalid': addForm.errors.has('classId')}" v-model="addForm.class" :options="classes" 
                        :preserve-search="true" placeholder="Select class" label="name" track-by="id" :loading="isClassLoading"></multiselect>
                        <HasError  :form="addForm" field="classId"/>
                    </div>

                    <div class="col-md-7 mb-4">
                        <label for="">Section name</label>
                        <input type="text" class="form-control" placeholder="Section name..." v-model="addForm.name" :class="{'is-invalid' : addForm.errors.has('name')}">
                        <HasError  :form="addForm" field="name"/>
                    </div>
                    
                    <div class="col-md-7 mb-4">
                        <Button :form="addForm" type="submit" class="btn btn-success">Add</Button>
                        <button @click="mode='list'" type="button" class="btn btn-danger">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12" v-else-if="mode == 'list'">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All Classes</h4>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <button @click="mode='add'" class="btn btn-primary"><i class="fas fa-plus"></i> Add section</button>
                    </div>
                </div>

                <div class="row" v-if="isLoading">
                    <div class="col-md-12 mb-3" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="40px" />
                    </div>
                </div>

                <div class="row" v-else>
                    <div class="col-md-12 table-responsive">
                        <table class="table table-borderd">
                            <thead>
                                <tr class="text-center">
                                    <th>Section ID</th>
                                    <th>Section name</th>
                                    <th>Class</th>
                                    <th>School</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center" v-for="(section,i) in sections" :key="i">
                                    <td>{{ section.id }}</td>
                                    <td>{{ section.name }}</td>
                                    <td>{{ section.classes.name }}</td>
                                    <td>{{ section.school.name }}</td>
                                    <td>
                                        <router-link :to="{name: 'admin.edit-section', params: {sectionId: section.id}}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></router-link>
                                        <button @click="deleteSection(section.id,i)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <pagination :data="paginateData" @pagination-change-page="getSectionList"></pagination>
                    </div>
                </div>
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
            isLoading: true,
            isClassLoading: false,
            classes: [],
            schools: [],
            sections: [],
            mode: 'list',
            addForm: new Form({
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
        getSchools() {
            axios.get("/admin/api/get-all-schools").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.schools = data;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        addSection() {

            if(this.addForm.school != null)
            {
                this.addForm.schoolId = this.addForm.school.id;
            }
            if(this.addForm.class != null) 
            {
                this.addForm.classId = this.addForm.class.id;
            }

            this.addForm.post('/admin/api/add-new-section').then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.sections.unshift(data.data);
                    swal.fire("Section added",data.msg,"success");
                    this.mode = 'list';
                    this.addForm.reset();
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        getClassList() {
            this.classes = [];
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
        getSectionList(page = 1) {
            axios.get("/admin/api/get-section-list?page="+page).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.sections = data.data;
                this.paginateData = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        deleteSection(id,index){
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
                axios.post("/admin/api/delete-section",{
                    sectionId: id,
                }).then(resp=>{
                    return resp.data;
                }).then(data=>{
                    if(data.status == "ok") {
                        swal.fire("Deleted",data.msg,"success");
                        this.sections.splice(index,1);
                    }
                }).catch(err=>{
                    toastr.error("Failed to delete","Internal Server error");
                    console.error(err.response.data);
                })
                }
            }); // swal
        },
    },
    mounted() {
        this.getSchools();
        this.getSectionList();
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