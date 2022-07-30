<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Edit supervisor profile</h4>
            </div>
            <div class="card-body">

                <div class="row" v-if="isLoading">
                    <div class="col-12 mb-4 text-center">
                      <skeleton class="user-thumb-100" width="100px" height="100px" />
                    </div>
                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>
                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>
                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>

                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>

                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>

                    <div class="col-md-4 mb-4">
                        <skeleton width="100px" height="20px"/>
                        <skeleton width="100%" height="40px"/>
                    </div>
                </div>

                <form v-else @submit.prevent="updateSupervisor" class="row">
                    <div class="col-12 mb-4 text-center">
                      <img v-if="userPhoto!=null" :src="userPhoto" class="user-thumb-100" alt="">
                      <img v-else src="/image/portrait-placeholder.png" class="user-thumb-100" alt="">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Select School</label>
                        <select class="form-control" v-model="form.school">
                            <option value="" selected hidden>Select School</option>
                            <option v-for="(school,i) in schools" :key="i" :value="school.id">{{ school.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Supervisor Name</label>
                        <input type="text" class="form-control" :class="{'is-invalid' : form.errors.has('name')}" v-model="form.name" placeholder="Supervisor name">
                        <HasError :form="form" field="name" />
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="">Phone number</label>
                        <input type="tel" class="form-control" :class="{'is-invalid' : form.errors.has('phone')}" v-model="form.phone" placeholder="Phone number...">
                        <HasError :form="form" field="phone" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Email <small>(optional)</small></label>
                        <input type="email" name="email" class="form-control" :class="{'is-invalid' : form.errors.has('email')}" v-model="form.email" placeholder="Email...">
                        <HasError :form="form" field="email" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Change Password <small>(optional)</small></label>
                        <input type="text" class="form-control" :class="{'is-invalid' : form.errors.has('password')}" 
                        v-model="form.password" placeholder="Set password...">
                        <HasError :form="form" field="password" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="">Set profile picture <small>(optional)</small></label>
                        <input type="file" class="form-control-file" 
                        :class="{'is-invalid' : form.errors.has('pp')}" @change="fileChange" accept="image/*">
                        <HasError :form="form" field="pp" />
                    </div>

                    <div class="col-md-12 mb-4">
                        <label for="access"><input type="checkbox" id="access" v-model="form.accessToLeave"> &nbsp; Can access teachers leave applications</label>
                    </div>

                    <div class="col-12 mb-4">
                        <Button class="btn btn-success" :form="form">Update</Button>
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
      form : new Form({
        userId: this.$route.params.userId,
        school: null,
        name: '',
        phone: '',
        email: '',
        role: 'supervisor',
        password: '',
        pp: null,
        accessToLeave: false,
      }),
      schools: [],
      isLoading: true,
      userPhoto: null,
    }
  },
  methods: {
    async updateSupervisor(){
      await this.form.post("/admin/api/update-supervisor-profile").then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Profile updated",data.msg,"success");
          if(data.photo != null){
            this.userPhoto = data.photo;
          }
        }
        else{
          toastr.error("Failed to update","failed");
        }
      }).catch(err=>{
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
    fileChange(e) {
        let file = e.target.files[0];
        if(file) {
            this.form.pp = file;
        }
        else {
            this.form.pp = null;
        }
    },
    getUserData() {
      axios.get("/admin/api/get-user-data?userId="+this.$route.params.userId).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.form.school = data.user.school_id;
          this.form.name = data.user.name;
          this.form.phone = data.user.phone;
          this.form.email = data.user.email;
          this.userPhoto = data.user.photo_url;
          if(data.user.teacher_application_permission == 1)
          {
            this.form.accessToLeave = true;
          }
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: 'admin.supervisor-list'
          });
        }
      }).catch(err=>{
        this.$router.push({
          name: 'admin.supervisor-list'
        });
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getSchools();
    this.getUserData();
  }
}
</script>

<style>

</style>