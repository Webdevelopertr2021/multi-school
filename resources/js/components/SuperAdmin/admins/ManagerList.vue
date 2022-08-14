<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>All managers</h4>
                <router-link :to="{name: 'admin.create-manager'}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add manager</router-link>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-md-12 mb-2" v-for="n in 10" :key="n">
                    <skeleton width="100%" height="40px" />
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-12">
                    <p><strong>{{ admins.length }}</strong> Admins</p>
                    <table class="table table-hover table-bordered">
                      <thead>
                        <tr>
                          <th>Photo</th>
                          <th>Manager name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(admin,i) in admins" :key="i">
                          <td>
                            <img v-if="admin.photo == null" class="user-thumb-40" src="/image/portrait-placeholder.png" alt="">
                            <img v-else class="user-thumb-40" :src="admin.photo_url" alt="">
                          </td>
                          <td>{{ admin.name }}</td>
                          <td>{{ admin.email }}</td>
                          <td>{{ admin.phone }}</td>
                          <td>
                            <router-link :to="{name: 'admin.edit-manager', params: {adminId : admin.id}}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></router-link>
                            <button @click="deleteAdmin(admin.id,i)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
</template>

<script>
export default {
  data() {
    return {
      isLoading: true,
      admins: [],
    }
  },
  methods: {
    getAdmins() {
      axios.get("/admin/api/get-manager-list").then(resp=>{
        return resp.data;
      }).then(data=>{
        this.admins = data;
        this.isLoading = false;
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    deleteAdmin(id,index) {
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
            axios.post("/admin/api/delete-admin",{
              adminId : id
            }).then(resp=>{
              return resp.data;
            }).then(data=>{
              if(data.status == "ok"){
                swal.fire("Success",data.msg,"success");
                this.admins.splice(index,1);
              }
            })
          }
      }); // swal
    }
  },
  mounted() {
    this.getAdmins();
  }
}
</script>

<style>

</style>