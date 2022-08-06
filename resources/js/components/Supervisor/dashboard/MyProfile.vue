<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My Profile</h4>
                
            </div>
            <div class="card-body">
                <form @submit.prevent="updateProfile" class="row">
                  <div class="col-md-12 text-center mb-4">
                    <img v-if="userPhoto == null" src="/image/portrait-placeholder.png" class="user-thumb-100" alt="">
                    <img v-else :src="userPhoto" class="user-thumb-100" alt="">
                  </div>
                  <div class="col-md-12">
                    <label for="">Profile Name</label>
                    <input type="text" class="form-control" placeholder="Profile name..." :class="{'is-invalid' : form.errors.has('name')}" v-model="form.name">
                    <HasError :form="form" field="name" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Phone</label>
                    <input type="tel" class="form-control" v-model="form.phone" placeholder="Admin phone..." :class="{'is-invalid' : form.errors.has('phone')}">
                    <HasError :form="form" field="phone" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Email</label>
                    <input type="email" class="form-control" v-model="form.email" placeholder="Admin email..." :class="{'is-invalid' : form.errors.has('email')}">
                    <HasError :form="form" field="email" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Password</label>
                    <input type="password" class="form-control" v-model="form.password" placeholder="Set password..." :class="{'is-invalid' : form.errors.has('password')}">
                    <HasError :form="form" field="password" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Photo</label>
                    <input type="file" class="form-control-file" accept="image/*" @change="fileChange" :class="{'is-invalid' : form.errors.has('photo')}">
                    <HasError :form="form" field="photo" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <Button :form="form" class="btn btn-success">Save</Button>
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
        email: "",
        phone: "",
        password: null,
        photo: null,
      }),
      userPhoto: null,
    }
  },
  methods: {
    fileChange(e) {
      let file = e.target.files[0];
      if(file) {
        this.form.photo = file;
      }
      else {
        this.form.photo = null;
      }
    },
    getMyProfile() {
      axios.get("/supervisor/api/get-my-profile-data").then(resp=>{
        return resp.data;
      }).then(data=>{
        console.log(data);
        if(data.status == "ok") {
          this.form.name = data.user.name;
          this.form.email = data.user.email;
          this.form.phone = data.user.phone;
          if(data.user.photo != null)
          {
            this.userPhoto = data.user.photo_url;
          }
        }
        else {
          this.$router.push({
            name: 'superv.dashboard'
          })
        }
      }).catch(err=>{
        this.$router.push({
          name: 'superv.dashboard'
        })
        console.error(err.response.data);
      })
    },
    async updateProfile() {
      await this.form.post('/supervisor/api/update-my-profile').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Profile updated",data.msg,"success");
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted () {
    this.getMyProfile();
  }
}
</script>

<style>

</style>