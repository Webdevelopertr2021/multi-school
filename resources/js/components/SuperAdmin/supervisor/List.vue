<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All Supervisors</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <input @keyup="$event.keyCode!=16?getSupervisors(page = 1):''" type="search" class="form-control"
                         placeholder="Search supervisors..." v-model="searchText">
                    </div>
                </div>
                <div class="row mt-2" v-if="isLoading">
                    <div class="col-12 mb-2" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="40px" />
                    </div>
                </div>
                <div class="row mt-2" v-else>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>Photo</th>
                                        <th>Supervisor name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(superv,i) in supervisors.data" :key="i" class="text-center">
                                        <td>
                                            <img v-if="superv.photo!=null" :src="superv.photo_url" class="user-thumb-40" alt="">
                                            <img v-else src="/image/portrait-placeholder.png" class="user-thumb-40" alt="">
                                        </td>
                                        <td>{{ superv.name }}</td>
                                        <td>{{ superv.phone }}</td>
                                        <td>{{ superv.email }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <router-link :to="{name: 'admin.superv.teachers', params: {supervisorId: superv.id}}" class="dropdown-item">
                                                        <i class="fas fa-users"></i> Show Teachers
                                                    </router-link>
                                                    <router-link :to="{name: 'admin.edit-superv', params: {userId: superv.id}}" 
                                                    class="dropdown-item"><i class="fas fa-edit"></i> Edit</router-link>
                                                    <div class="dropdown-divider"></div>
                                                    <a @click="deleteSuperv(superv.id,i)" class="dropdown-item text-danger" href="#"><i class="fas fa-trash"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
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
            isLoading: true,
            supervisors: {},
            searchText: "",
        }
    },
    methods : {
        getSupervisors(page = 1) {
            this.isLoading = true;
            axios.get("/admin/api/get-all-supervisors",{
                params: {
                    page: page,
                    search: this.searchText
                }
            }).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.supervisors = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        deleteSuperv(id,index){
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
                    axios.post("/admin/api/delete-supervisor",{
                        userId: id
                    }).then(resp=>{
                        return resp.data;
                    }).then(data=>{
                        if(data.status == "ok") {
                            swal.fire("Deleted",data.msg,"success");
                            this.supervisors.data.splice(index,1);
                        }
                    }).catch(err=>{
                        console.error(err.response.data);
                    })
                }
            })
            
        }
    },
    mounted() {
        this.getSupervisors();
    }
}
</script>

<style>

</style>