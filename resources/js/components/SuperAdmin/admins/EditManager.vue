<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Edit manager profile</h4>
                <router-link :to="{name: 'admin.manager-list'}" class="btn btn-sm btn-primary"><i class="fas fa-list"></i> Manager list</router-link>
            </div>
            <div class="card-body">
                <form @submit.prevent="updateAdmin" class="row">
                  <div class="col-md-12 mb-4">
                    <label for="">Manager name</label>
                    <input type="text" class="form-control" v-model="form.name" placeholder="Admin name..." :class="{'is-invalid' : form.errors.has('name')}">
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
                    <label for="">Change Password</label>
                    <input type="password" class="form-control" v-model="form.password" placeholder="Set password..." :class="{'is-invalid' : form.errors.has('password')}">
                    <HasError :form="form" field="password" />
                  </div>

                  <div class="col-md-12 mb-4">
                    <label for="">Change Photo</label>
                    <input type="file" class="form-control-file" accept="image/*" @change="fileChange" :class="{'is-invalid' : form.errors.has('photo')}">
                    <HasError :form="form" field="photo" />
                  </div>

                  <div class="col-md-12 mb-4">
                      <label for="access"><input type="checkbox" id="access" v-model="form.accessToLeave"> &nbsp; Can access teachers leave applications</label>
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
        adminId: this.$route.params.adminId,
        name: '',
        email : '',
        phone: '',
        password: 'school2022',
        photo: null,
        accessToLeave: false,
      })
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
    getAdminData() {
      axios.get("/admin/api/get-admin-data?adminId="+this.$route.params.adminId).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.form.name = data.user.name;
          this.form.phone = data.user.phone;
          this.form.email = data.user.email;
          this.form.password = null;
          if(data.user.teacher_application_permission == 1)
          {
            this.form.accessToLeave = true;
          }
        }
        else
        {
          this.$router.push({
            name: 'admin.admin-list'
          });
        }
      }).catch(err=>{
        this.$router.push({
          name: 'admin.admin-list'
        });
      });
    },
    async updateAdmin() {
      await this.form.post('/admin/api/update-manager').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Updated",data.msg,"success");
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getAdminData();
  }
}
</script>

<style>

</style>