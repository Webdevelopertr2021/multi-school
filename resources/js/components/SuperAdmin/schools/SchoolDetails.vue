<template>
  <div>
    <div class="row">
      <div class="col-12 mb-4">
        <router-link :to="{name: 'admin.school-list'}" class="btn btn-sm btn-primary"><i class="fas fa-arrow-left"></i> Go back to list</router-link>
      </div>
      <div class="col-md-4">
          <div class="card">
              <div class="card-header d-flex justify-content-between">
                  <h4>School Details</h4>
              </div>
              <div class="card-body">
                  <div class="row" v-if="isLoading">
                    <div class="col-12 mb-3">
                      <skeleton width="100%" height="50px"></skeleton>
                    </div>
                    <div class="col-12 mb-3">
                      <skeleton width="100%" height="50px"></skeleton>
                    </div>
                    <div class="col-12 mb-3">
                      <skeleton width="100%" height="50px"></skeleton>
                    </div>
                  </div>

                  <ul class="list-group" v-else>
                    <li class="list-group-item"><b>School name :</b> {{ schoolData.name }}</li>
                    <li class="list-group-item"><b>Address :</b> {{ schoolData.address }}</li>
                    <li class="list-group-item"><b>School Code :</b> {{ schoolData.code }}</li>
                  </ul>

              </div>
          </div>
      </div>
    </div>
    <div class="row mt-2">
      <!-- <div class="col-md-5">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <h4>Super Visors List <i class="fas fa-list"></i></h4>
            <router-link :to="{name: 'admin.add-supervisor', params: {schoolId: $route.params.schoolId}}" class="btn btn-sm btn-primary"><i class="fas fa-user-plus"></i> Add Supervisor</router-link>
          </div>
          <div class="card-body">
            <div class="row" v-if="isLoading">
              <div class="col-12 mb-2" v-for="n in 5" :key="n">
                <skeleton width="100%" height="40px" />
              </div>
            </div>
            <div class="table-responsive" v-else>
              <table class="table table-bordered table-striped">
                <thead>
                  <tr class="text-center">
                    <th>Supervisor name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="superVisors.length <= 0">
                    <tr class="text-center">
                      <td colspan="4" class="text-danger"><span>No records found</span></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(user,i) in superVisors" :key="i" class="text-center">
                      <td>{{ user.name }}</td>
                      <td>{{ user.phone }}</td>
                      <td>{{ user.email }}</td>
                      <td>
                        <router-link :to="{name: 'admin.edit-superv', params: {userId : user.id}}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></router-link>
                        <button @click="deleteSuperv(user.id,i)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> -->

      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <h4>Teacher List <i class="fas fa-list"></i></h4>
            <router-link :to="{name: 'admin.add-teacher', params:{schoolId: $route.params.schoolId}}" class="btn btn-sm btn-primary"><i class="fas fa-user-plus"></i> Add Teacher</router-link>
          </div>
          <div class="card-body">
            <div class="row" v-if="isLoading">
              <div class="col-12 mb-2" v-for="n in 5" :key="n">
                <skeleton width="100%" height="40px" />
              </div>
            </div>
            <div class="table-responsive" v-else>
              <table class="table table-bordered table-hover table-striped">
                <thead>
                  <tr class="text-center">
                    <th>Teacher name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Overall Ratings</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="teachers.length <=0">
                    <tr class="text-center text-danger">
                      <td colspan="6"><span>No records found</span></td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="(teacher,i) in teachers" :key="i" class="text-center">
                      <td>{{ teacher.name }}</td>
                      <td>{{ teacher.phone }}</td>
                      <td>{{ teacher.email }}</td>
                      <td>{{  calculateRating(teacher.rating) }} <i class="fas fa-star text-warning"></i></td>
                      <td>
                        <router-link :to="{name: 'admin.teacher-edit', params: {teacherId: teacher.id}}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></router-link>
                        <button class="btn btn-sm btn-danger" @click="deleteTeacher(teacher.id,i)"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </template>
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
      schoolData: {},
      superVisors: [],
      teachers: []
    }
  },
  methods: {
    getSchoolData() {
      axios.get("/admin/api/get-school-data",{
        params: {
          schoolId : this.$route.params.schoolId,
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok")
        {
          this.schoolData = data.schoolData;
          this.superVisors = data.supervisors;
          this.teachers = data.teacher;
          this.isLoading = false;
        }
        else
        {
          this.$router.push({
            name: 'admin.school-list'
          })
        }
        
      }).catch(err=>{
        this.$router.push({
          name: 'admin.school-list'
        })
        console.log(err.response.data);
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
        
    },
    calculateRating(ratings) {
        let totalRates = 0;
        let totalPoints = 0;
        ratings.forEach((item,i)=>{

            totalRates++;
            totalPoints+= item.total;
        });

        return (totalPoints/totalRates).toFixed(1);
    },
    deleteTeacher(id,index) {
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
          axios.post("/admin/api/delete-teacher",{
              teacherId: id,
          }).then(resp=>{
              return resp.data;
          }).then(data=>{
              if(data.status == "ok")
              {
                swal.fire("Techer deleted",data.msg,"success");
                this.teachers.splice(index,1);
              }
          }).catch(err=>{
              toastr.error("Failed to delete","Internal Server error");
              console.error(err.response.data);
          })
          }
      });//
    }
    

  },
  mounted() {
    this.getSchoolData();
  }
}
</script>

<style>

</style>