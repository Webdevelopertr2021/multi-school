<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4><i class="fas fa-list"></i> School List</h4>
                <router-link :to="{name : 'admin.add-school'}" class="btn btn-primary"><i class="fas fa-plus"></i> Add school</router-link>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-6 mb-4">
                    <select @change="getSchoolList(page = 1)" class="form-control w-md-50" v-model="perPage">
                      <option value="10">10</option>
                      <option value="30">30</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </div>
                  <div class="col-6 mb-4">
                    <input @keyup="$event.keyCode!=16?getSchoolList(page = 1):''" type="text" class="form-control w-md-50" placeholder="Serch schools..." v-model="searchText">
                  </div>
                </div>
                <div class="row" v-if="isLoading">
                  <div class="col-12 mb-2" v-for="n in 10" :key="n">
                    <skeleton width="100%" height="40px"></skeleton>
                  </div>
                </div>
                <div class="row" v-else>
                  
                  <div class="col-12 mb-4">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr class="text-center">
                          <th>School ID</th>
                          <th>School Name</th>
                          <th>Address</th>
                          <th>School Code</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <template v-if="schools.data.length <= 0">
                          <tr>
                            <td colspan="4" class="text-danger text-center">No schools</td>
                          </tr>
                        </template>
                        <template v-else>
                          <tr class="text-center" v-for="(school,i) in schools.data" :key="i">
                            <td>{{ school.id }}</td>
                            <td><router-link :to="{name: 'admin.school-details', params: {schoolId: school.id}}">{{ school.name }}</router-link></td>
                            <td>{{ school.address }}</td>
                            <td>{{ school.code }}</td>
                            <td>
                              <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                </button>
                                <div class="dropdown-menu">
                                  <a class="dropdown-item" href="#" @click="editModal(school,i)"><i class="fas fa-edit"></i> Edit</a>
                                  <router-link class="dropdown-item" :to="{name: 'admin.school-details', params: {schoolId: school.id}}"><i class="fas fa-eye"></i> View School</router-link>
                                  <router-link class="dropdown-item" :to="{name: 'admin.add-supervisor', params: {schoolId: school.id}}"><i class="fas fa-user-plus"></i> Add Supervisor</router-link>
                                  <router-link class="dropdown-item" :to="{name: 'admin.add-teacher', params: {schoolId: school.id}}"><i class="fas fa-user-plus"></i> Add Teacher</router-link>
                                  <div class="dropdown-divider"></div>
                                  <a @click="deleteSchool(school.id,i)" class="dropdown-item text-danger" href="#"><i class="fas fa-trash"></i> Delete</a>
                                </div>
                              </div>
                            </td>
                          </tr>
                        </template>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-12 d-flex justify-content-center">
                    <pagination :data="schools" @pagination-change-page="getSchoolList"></pagination>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <modal-comp :schoolData="selectedRow" :index="selectedIndex" @updated="dataUpdated"></modal-comp>
  </div>
</template>

<script>
import ModalComp from "./EditSchoolModal.vue";
export default {
  components: {
    "modal-comp": ModalComp,
  },
  data() {
    return {
      isLoading: true,
      schools: {},
      searchText: "",
      perPage: 10,
      selectedRow: {},
      selectedIndex: "",
    }
  },
  methods: {
    dataUpdated(data,msg,index){
      swal.fire("Success",msg,"success");
      let newData  = this.schools.data[index];
      newData.id = data.id;
      newData.name = data.name;
      newData.address = data.address;
      newData.code = data.code;
      newData.created_at = data.created_at;
      newData.updated_at = data.updated_at;
      $("#editModal").modal("hide");
    },
    getSchoolList(page = 1) {
      this.isLoading = true;
      axios.get("/admin/api/get-school-list",{
        params: {
          search: this.searchText,
          perPage: this.perPage,
          page: page,
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        this.schools = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    editModal(data,index) {
      this.selectedRow = data;
      this.selectedIndex = index;
      $('#editModal').modal("show");
    },
    deleteSchool(id,index) {
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
          axios.post("/admin/api/delete-school",{
            schoolId: id,
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            if(data.status == "ok")
            {
              swal.fire("School deleted",data.msg,"success");
              this.schools.data.splice(index,1);
            }
          }).catch(err=>{
            toastr.error("Failed to delete","Internal Server error");
            console.error(err.response.data);
          })
        }
      })//
    }
  },
  mounted() {
    this.getSchoolList();
  },
  created(){
    setTimeout(function(){
      var modal = $('#editModal');
      $("body").append(modal);
    },2000);
  }
}
</script>

<style>

</style>