<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Edit Student records</h4>
            </div>
            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-12">
                      <img v-if="studentPhotoUrl == null" class="user-thumb-100" src="/image/portrait-placeholder.png" alt="">
                      <img v-else class="user-thumb-100" :src="studentPhotoUrl" alt="">
                    </div>
                </div>

                <form class="row" @submit.prevent="updateStudent">

                  <div class="col-md-4 mb-4">
                      <label for="">Select School</label>
                      <multiselect :class="{'is-invalid': form.errors.has('schoolId')}" v-model="selectedSchool" :options="schools" 
                       @input="getClassList" :preserve-search="true" placeholder="Select section" label="name" track-by="id"></multiselect>
                      <HasError  :form="form" field="sectionId"/>
                  </div>

                  <div class="col-md-4 mb-4">
                      <label for="">Select Class</label>
                      <multiselect :class="{'is-invalid': form.errors.has('classId')}" v-model="selectedClass" :options="classes" 
                       @input="getSectionList" :preserve-search="true" placeholder="Select section" label="name" track-by="id" :loading="isLoadingClass"></multiselect>
                      <HasError  :form="form" field="classId"/>
                  </div>

                  <div class="col-md-4 mb-4">
                      <label for="">Select Section</label>
                      <multiselect :class="{'is-invalid': form.errors.has('sectionId')}" v-model="selectedSection" :options="sections" 
                        :preserve-search="true" placeholder="Select section" label="name" track-by="id" :loading="isLoadingSection"></multiselect>
                      <HasError  :form="form" field="sectionId"/>
                  </div>

                  <div class="col-md-4 mb-4">
                      <label for="">Student name</label>
                      <input type="text" class="form-control" :class="{'is-invalid': form.errors.has('name')}"
                       placeholder="Student name" v-model="form.name">
                       <HasError  :form="form" field="name"/>
                  </div>

                  <div class="col-md-4 mb-4">
                      <label for="">Student phone number</label>
                      <input type="tel" class="form-control" :class="{'is-invalid': form.errors.has('phone')}"
                       placeholder="Student phone..." v-model="form.phone">
                       <HasError  :form="form" field="phone"/>
                  </div>

                  <div class="col-md-4 mb-4">
                      <label for="">Student email <small>(Optional)</small></label>
                      <input type="email" class="form-control" :class="{'is-invalid': form.errors.has('stdEmail')}"
                       placeholder="Student email..." v-model="form.stdEmail">
                       <HasError  :form="form" field="stdEmail"/>
                  </div>
                  <div class="col-md-7 mb-4">
                    <label for="">Address</label>
                    <textarea class="form-control" :class="{'is-invalid': form.errors.has('address')}"
                     v-model="form.address" placeholder="Student address..."></textarea>
                     <HasError  :form="form" field="address"/>
                  </div>
                  <div class="col-md-7 mb-4">
                    <label for="">Change Student photo</label>
                    <input type="file" class="form-control-file" @change="fileChange" accept="image/*" id="stdFile">
                  </div>
                  <div class="col-md-12 mb-4">
                    <Button :form="form" class="btn btn-success">Update</Button>
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
      selectedSchool: null,
      selectedClass: null,
      selectedSection: null,
      form: new Form({
        studentId: this.$route.params.studentId,
        name: "",
        stdEmail: "",
        phone: "",
        schoolId: "",
        classId: "",
        sectionId: "",
        photo: null,

      }),
      schools: [],
      classes: [],
      sections: [],
      isLoadingClass: false,
      isLoadingSection: false,
      studentPhotoUrl: null,
    }
  },
  methods :{
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
        this.form.photo = file;
      }
      else {
        this.form.photo = null;
      }
    },

    getClassList() {
      this.isLoadingClass = true;
      this.classes = [];
      this.sections = [];
      this.selectedSection = null;
      this.selectedClass = null;

      axios.get("/admin/api/get-class-list?schoolId="+this.selectedSchool.id).then(resp=>{
          return resp.data;
      }).then(data=>{
          this.classes = data;
          this.isLoadingClass = false;
      }).catch(err=>{
          console.error(err.response.data);
      });

    },
    getSectionList() {
      this.isLoadingSection = true;
      this.sections = [];
      this.selectedSection = null;
      axios.get("/admin/api/get-section-list?classId="+this.selectedClass.id).then(resp=>{
          return resp.data;
      }).then(data=>{
          this.sections = data;
          this.isLoadingSection = false;
      }).catch(err=>{
          console.error(err.response.data);
      })
    },
    async updateStudent() {
      this.form.schoolId = this.selectedSchool.id;
      this.form.classId = this.selectedClass.id;
      this.form.sectionId = this.selectedSection.id;
      await this.form.post('/admin/api/update-student').then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          swal.fire("Added",data.msg,"success");
          $("#stdFile").val("");
        }
      }).catch(err=>{
        console.error(err.response.data);
      })
    },
    getStudentData() {
      axios.get('/admin/api/get-student-data',{
        params: {
          studentId : this.$route.params.studentId,
        }
      }).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          this.getSchools();

          this.selectedSchool = data.student.school;
          this.selectedClass = data.student.class;
          this.selectedSection = data.student.section;
          this.form.name = data.student.name;
          this.form.phone = data.student.phone;
          this.form.email = data.student.email;
          this.form.address = data.student.address;
          this.form.schoolId = data.student.school_id;
          this.form.sectionId = data.student.section_id;
          this.form.classId = data.student.class_id;
          this.classes = data.classes;
          this.sections = data.section;
          if(data.student.photo != null)
          {
            this.studentPhotoUrl = data.student.photo_url;
          }

        }
        else {
          this.$router.push({
            name: 'admin.student-list'
          });
        }
      }).catch(err=>{
        this.$router.push({
          name: 'admin.student-list'
        });
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getStudentData();
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