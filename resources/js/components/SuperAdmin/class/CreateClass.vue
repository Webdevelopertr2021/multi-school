<template>
  <div class="row justify-content-center">

    <div class="col-md-12" v-if="mode == 'add'">
        <div class="card">
            <div class="card-header">
                <h4>Add new class</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="addClass" class="row justify-content-center">
                    <div class="col-md-7">
                        <h6>Add class</h6>
                        <hr>
                    </div>
                    <div class="col-md-7 mb-4">
                        <label for="">Class name</label>
                        <input type="text" class="form-control" placeholder="Class name..." v-model="addForm.name" :class="{'is-invalid': addForm.errors.has('name')}">
                        <HasError  :form="addForm" field="name"/>
                    </div>
                    <div class="col-md-7 mb-4">
                        <label for="">School</label>
                        <select class="form-control" v-model="addForm.school" :class="{'is-invalid': addForm.errors.has('school')}">
                            <option value="" hidden selected>Select school</option>
                            <option v-for="school in schools" :key="school.id" :value="school.id">{{ school.name }}</option>
                        </select>
                        <HasError :form="addForm" field="school" />
                    </div>
                    <div class="col-md-7 mb-4">
                        <Button :form="addForm" type="submit" class="btn btn-success">Add</Button>
                        <button @click="mode='list'" type="button" class="btn btn-danger">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12" v-else-if="mode == 'edit'">
        <div class="card">
            <div class="card-header">
                <h4>Edit class</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="updateClass" class="row justify-content-center">
                    <div class="col-md-7">
                        <h6>Edit</h6>
                        <hr>
                    </div>
                    <input type="hidden" v-model="editForm.id">
                    <div class="col-md-7 mb-4">
                        <label for="">Class name</label>
                        <input type="text" class="form-control" placeholder="Class name..." v-model="editForm.name" :class="{'is-invalid': editForm.errors.has('name')}">
                        <HasError  :form="editForm" field="name"/>
                    </div>
                    <div class="col-md-7 mb-4">
                        <label for="">School</label>
                        <select class="form-control" v-model="editForm.school" :class="{'is-invalid': editForm.errors.has('school')}">
                            <option value="" hidden selected>Select school</option>
                            <option v-for="school in schools" :key="school.id" :value="school.id">{{ school.name }}</option>
                        </select>
                        <HasError :form="editForm" field="school" />
                    </div>
                    <div class="col-md-7 mb-4">
                        <Button :form="editForm" type="submit" class="btn btn-success">Update</Button>
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
                        <button @click="mode='add'" class="btn btn-primary"><i class="fas fa-plus"></i> Add class</button>
                    </div>
                </div>

                <div class="row" v-if="isLoading">
                    <div class="col-md-12 mb-3" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="40px" />
                    </div>
                </div>

                <div class="row" v-else>
                    <div class="col-md-12">
                        <table class="table table-borderd">
                            <thead>
                                <tr class="text-center">
                                    <th>Class ID</th>
                                    <th>Class name</th>
                                    <th>School name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center" v-for="(Class,i) in classes" :key="i">
                                    <td>{{ Class.id }}</td>
                                    <td>{{ Class.name }}</td>
                                    <td>{{ Class.school.name }}</td>
                                    <td>
                                        <button @click="editData(Class,i)" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button @click="deleteClass(Class.id,i)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <pagination :data="paginateData" @pagination-change-page="getClassList"></pagination>
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
            classes: [],
            schools: [],
            mode: 'list',
            addForm: new Form({
                name: '',
                school: '',
            }),
            editForm: new Form({
                id: null,
                name: '',
                school: '',
            }),
            paginateData: {},
            selectedIndex: null,
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
        addClass() {
            this.addForm.post('/admin/api/add-new-class').then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.classes.unshift(data.data);
                    swal.fire("Class added",data.msg,"success");
                    this.mode = 'list';
                    this.addForm.reset();
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        updateClass() {
            this.editForm.post('/admin/api/update-class').then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.classes[this.selectedIndex] = data.data;
                    swal.fire("Class updated",data.msg,"success");
                    this.mode = 'list';
                }
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        getClassList(page = 1) {
            axios.get("/admin/api/get-class-list?page="+page).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.classes = data.data;
                this.paginateData = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        deleteClass(id,index){
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
                axios.post("/admin/api/delete-class",{
                    classId: id,
                }).then(resp=>{
                    return resp.data;
                }).then(data=>{
                    if(data.status == "ok") {
                        swal.fire("Deleted",data.msg,"success");
                        this.classes.splice(index,1);
                    }
                }).catch(err=>{
                    toastr.error("Failed to delete","Internal Server error");
                    console.error(err.response.data);
                })
                }
            }); // swal
        },
        editData(row,index) {
            this.editForm.id = row.id;
            this.editForm.name = row.name;
            this.editForm.school = row.school_id;
            this.selectedIndex = index;
            this.mode = 'edit';
        }
    },
    mounted() {
        this.getSchools();
        this.getClassList();
    }
}
</script>

<style>

</style>